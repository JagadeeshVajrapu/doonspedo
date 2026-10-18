<?php

namespace App\Services;

use App\Models\AccountDeletionRequest;
use App\Models\CustomerAadhaarVerification;
use App\Models\CustomerKycSubmission;
use App\Models\DriverDocument;
use App\Models\DriverRegistration;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class AccountDeletionService
{
    /**
     * Anonymize personal data and revoke authentication.
     * Ride/payment rows are retained with anonymized account references.
     */
    public function complete(AccountDeletionRequest $request, ?int $adminId = null, ?string $notes = null): AccountDeletionRequest
    {
        if ($request->status === AccountDeletionRequest::STATUS_COMPLETED) {
            return $request->fresh();
        }

        if ($request->status !== AccountDeletionRequest::STATUS_APPROVED) {
            throw new RuntimeException('Only approved deletion requests can be completed.');
        }

        $filesToDelete = [];

        $completed = DB::transaction(function () use ($request, $adminId, $notes, &$filesToDelete) {
            $locked = AccountDeletionRequest::query()->lockForUpdate()->findOrFail($request->id);

            if ($locked->status === AccountDeletionRequest::STATUS_COMPLETED) {
                return $locked;
            }

            if ($locked->status !== AccountDeletionRequest::STATUS_APPROVED) {
                throw new RuntimeException('Only approved deletion requests can be completed.');
            }

            if ($locked->account_type === 'customer') {
                $filesToDelete = $this->anonymizeCustomer($locked);
            } elseif ($locked->account_type === 'partner') {
                $filesToDelete = $this->anonymizePartner($locked);
            } else {
                throw new RuntimeException('Unsupported account type.');
            }

            $locked->status = AccountDeletionRequest::STATUS_COMPLETED;
            $locked->completed_at = now();
            $locked->processed_at = now();
            $locked->processed_by = $adminId;
            $locked->verification_code_hash = null;
            if ($notes) {
                $locked->admin_notes = trim(($locked->admin_notes ? $locked->admin_notes."\n" : '').$notes);
            }
            $locked->save();

            return $locked->fresh();
        });

        foreach ($filesToDelete as $path) {
            $this->deleteOwnedFile($path);
        }

        return $completed;
    }

    /**
     * @return list<string|null>
     */
    private function anonymizeCustomer(AccountDeletionRequest $request): array
    {
        $files = [];
        $user = null;
        if ($request->account_id) {
            $user = User::query()->find($request->account_id);
        }
        if (!$user && $request->email) {
            $user = User::query()->where('email', $request->email)->first();
        }
        if (!$user && $request->mobile) {
            $user = User::query()->where('mobile', $request->mobile)->first();
        }

        if (!$user) {
            return $files;
        }

        $files[] = $user->photo;

        if (Schema::hasTable('customer_kyc_submissions')) {
            CustomerKycSubmission::query()->where('user_id', $user->id)->get()->each(function (CustomerKycSubmission $row) use (&$files) {
                $files[] = $row->document_path;
                $row->delete();
            });
        }

        if (Schema::hasTable('customer_aadhaar_verifications')) {
            CustomerAadhaarVerification::query()->where('user_id', $user->id)->delete();
        }

        $payload = [
            'name' => 'Deleted User',
            'email' => 'deleted+customer'.$user->id.'@deleted.doonspedo.local',
            'password' => Str::password(40),
        ];

        foreach ([
            'mobile' => null,
            'photo' => null,
            'google_id' => null,
            'facebook_id' => null,
            'remember_token' => null,
            'wallet_balance' => 0,
        ] as $column => $value) {
            if (Schema::hasColumn($user->getTable(), $column)) {
                $payload[$column] = $value;
            }
        }

        $user->forceFill($payload)->save();

        return $files;
    }

    /**
     * @return list<string|null>
     */
    private function anonymizePartner(AccountDeletionRequest $request): array
    {
        $files = [];
        $driver = null;
        if ($request->account_id) {
            $driver = DriverRegistration::query()->find($request->account_id);
        }
        if (!$driver && $request->email) {
            $driver = DriverRegistration::query()->where('email', $request->email)->first();
        }
        if (!$driver && $request->mobile) {
            $driver = DriverRegistration::query()->where('mobile', $request->mobile)->first();
        }

        if (!$driver) {
            return $files;
        }

        foreach (['profile_image', 'license_image', 'aadhaar_image'] as $field) {
            $files[] = $driver->{$field} ?? null;
        }

        if (Schema::hasTable('driver_documents')) {
            DriverDocument::query()->where('driver_id', $driver->id)->get()->each(function (DriverDocument $doc) use (&$files) {
                $files[] = $doc->document_path ?? null;
                $doc->delete();
            });
        }

        $payload = [
            'name' => 'Deleted Partner',
            'email' => 'deleted+partner'.$driver->id.'@deleted.doonspedo.local',
            'mobile' => '000000'.str_pad((string) $driver->id, 4, '0', STR_PAD_LEFT),
            'password' => Str::password(40),
            'status' => 'rejected',
        ];

        foreach ([
            'profile_image' => null,
            'license_image' => null,
            'aadhaar_image' => null,
            'license_number' => 'DELETED',
            'vehicle_number' => 'DELETED',
            'is_blocked' => true,
            'is_online' => false,
            'wallet_balance' => 0,
            'current_lat' => null,
            'current_lng' => null,
        ] as $column => $value) {
            if (Schema::hasColumn($driver->getTable(), $column)) {
                $payload[$column] = $value;
            }
        }

        $driver->forceFill($payload)->save();

        return $files;
    }

    private function deleteOwnedFile(?string $path): void
    {
        if (!$path || preg_match('#^https?://#i', $path)) {
            return;
        }

        $relative = ltrim(str_replace('\\', '/', $path), '/');
        $relative = preg_replace('#^storage/#', '', $relative) ?: $relative;

        try {
            if (Storage::disk('public')->exists($relative)) {
                Storage::disk('public')->delete($relative);
            }
            if (Storage::disk('local')->exists($relative)) {
                Storage::disk('local')->delete($relative);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
