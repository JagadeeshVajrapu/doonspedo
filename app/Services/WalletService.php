<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\Booking;
use App\Models\CommissionSetting;
use App\Models\DriverNotification;
use App\Models\DriverRegistration;
use App\Models\Transaction;
use App\Models\WalletRecharge;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WalletService
{
    public function money(mixed $value): string
    {
        return bcadd((string) ($value ?? '0'), '0', 2);
    }

    public function activeCommission(): ?CommissionSetting
    {
        $setting = CommissionSetting::current();

        return ($setting && $setting->is_active) ? $setting : null;
    }

    public function requiredCommission(?Booking $booking = null): string
    {
        $setting = $this->activeCommission();
        if (!$setting) {
            return '0.00';
        }

        if ($setting->type === 'percentage') {
            $fare = $this->money($booking?->fare ?? 0);
            if (bccomp($fare, '0', 2) !== 1) {
                return '0.00';
            }

            return bcdiv(bcmul($fare, $this->money($setting->amount), 4), '100', 2);
        }

        return $this->money($setting->amount);
    }

    public function isLowBalance(DriverRegistration $driver): bool
    {
        $setting = CommissionSetting::current();
        if (!$setting) {
            return false;
        }

        return bccomp($this->money($driver->wallet_balance), $this->money($setting->low_balance_threshold), 2) <= 0;
    }

    public function acceptanceBlock(DriverRegistration $driver, ?Booking $booking = null): ?string
    {
        $required = $this->requiredCommission($booking);
        $balance = $this->money($driver->wallet_balance);

        if (bccomp($required, '0', 2) !== 1 || bccomp($balance, $required, 2) >= 0) {
            return null;
        }

        $short = bcsub($required, $balance, 2);

        return 'Insufficient wallet balance. Your wallet balance is ₹'.number_format((float) $balance, 2).'. Please add at least ₹'.number_format((float) $short, 2).' to continue accepting rides.';
    }

    /**
     * Single credit path for admin approval and a future verified payment webhook.
     */
    public function creditRecharge(int $rechargeId, ?int $adminId = null): array
    {
        return DB::transaction(function () use ($rechargeId, $adminId) {
            return $this->creditRechargeLocked($rechargeId, $adminId);
        });
    }

    public function creditRechargeLocked(int $rechargeId, ?int $adminId = null): array
    {
        $recharge = WalletRecharge::query()->whereKey($rechargeId)->lockForUpdate()->first();
        if (!$recharge) {
            throw new RuntimeException('Recharge not found.');
        }

        if ($recharge->status === 'successful') {
            return ['ok' => false, 'already' => true, 'message' => 'Payment already processed.'];
        }

        if ($recharge->status !== 'pending') {
            return ['ok' => false, 'already' => false, 'message' => 'Only a pending recharge can be approved.'];
        }

        $amount = $this->money($recharge->amount);
        if (bccomp($amount, '0', 2) !== 1) {
            return ['ok' => false, 'already' => false, 'message' => 'Recharge amount must be greater than zero.'];
        }

        $driver = DriverRegistration::query()->whereKey($recharge->driver_id)->lockForUpdate()->first();
        if (!$driver) {
            throw new RuntimeException('Partner not found for this recharge.');
        }

        $before = $this->money($driver->wallet_balance);
        $after = bcadd($before, $amount, 2);

        $driver->wallet_balance = $after;
        $driver->save();

        Transaction::create([
            'driver_id' => $driver->id,
            'type' => 'credit',
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $after,
            'description' => 'Wallet recharge',
            'reference_id' => (string) $recharge->id,
            'reference_type' => 'wallet_recharge',
            'category' => 'recharge',
            'wallet_recharge_id' => $recharge->id,
            'status' => 'success',
        ]);

        $recharge->update([
            'status' => 'successful',
            'verified_by' => $adminId,
            'verified_at' => now(),
        ]);

        DriverNotification::create([
            'driver_id' => $driver->id,
            'title' => 'Wallet recharged',
            'message' => '₹'.number_format((float) $amount, 2).' has been added to your wallet.',
            'type' => 'wallet',
            'action_url' => route('driver.wallet'),
            'is_read' => false,
        ]);

        AdminNotification::query()
            ->where('reference_type', 'wallet_recharge')
            ->where('reference_id', $recharge->id)
            ->update([
                'title' => 'Wallet payment approved',
                'message' => '₹'.number_format((float) $amount, 2).' was added to '.$driver->name.'\'s wallet.',
                'is_read' => true,
            ]);

        return ['ok' => true, 'already' => false, 'message' => 'Payment approved and wallet credited.', 'balance' => $after];
    }

    public function rejectRecharge(int $rechargeId, int $adminId, string $reason): array
    {
        return DB::transaction(function () use ($rechargeId, $adminId, $reason) {
            $recharge = WalletRecharge::query()->whereKey($rechargeId)->lockForUpdate()->first();
            if (!$recharge) {
                throw new RuntimeException('Recharge not found.');
            }

            if ($recharge->status === 'successful') {
                return ['ok' => false, 'message' => 'Payment already processed.'];
            }

            if ($recharge->status !== 'pending') {
                return ['ok' => false, 'message' => 'Only a pending recharge can be rejected.'];
            }

            $recharge->update([
                'status' => 'rejected',
                'verified_by' => $adminId,
                'verified_at' => now(),
                'rejection_reason' => $reason,
            ]);

            DriverNotification::create([
                'driver_id' => $recharge->driver_id,
                'title' => 'Recharge rejected',
                'message' => 'Your wallet recharge of ₹'.number_format((float) $recharge->amount, 2).' was rejected. '.$reason,
                'type' => 'wallet',
                'action_url' => route('driver.wallet'),
                'is_read' => false,
            ]);

            AdminNotification::query()
                ->where('reference_type', 'wallet_recharge')
                ->where('reference_id', $recharge->id)
                ->update([
                    'title' => 'Wallet payment rejected',
                    'message' => 'Recharge #'.$recharge->id.' was rejected. '.$reason,
                    'is_read' => true,
                ]);

            return ['ok' => true, 'message' => 'Recharge rejected. Wallet balance was not changed.'];
        });
    }

    public function debitRideCommission(int $driverId, int $bookingId): array
    {
        return DB::transaction(function () use ($driverId, $bookingId) {
            return $this->debitRideCommissionLocked($driverId, $bookingId);
        });
    }

    public function debitRideCommissionLocked(int $driverId, int $bookingId): array
    {
        $existing = Transaction::query()
            ->where('driver_id', $driverId)
            ->where('booking_id', $bookingId)
            ->where('category', 'ride_commission')
            ->lockForUpdate()
            ->first();

        if ($existing) {
            return ['ok' => true, 'already' => true, 'message' => 'Commission already deducted.'];
        }

        $booking = Booking::query()->whereKey($bookingId)->first();
        $amount = $this->requiredCommission($booking);
        if (bccomp($amount, '0', 2) !== 1) {
            return ['ok' => true, 'already' => false, 'skipped' => true, 'message' => 'No active commission.'];
        }

        $driver = DriverRegistration::query()->whereKey($driverId)->lockForUpdate()->first();
        if (!$driver) {
            throw new RuntimeException('Partner not found.');
        }

        $before = $this->money($driver->wallet_balance);
        if (bccomp($before, $amount, 2) < 0) {
            $alreadyFailed = Transaction::query()
                ->where('driver_id', $driver->id)
                ->where('booking_id', $bookingId)
                ->where('category', 'ride_commission_failed')
                ->exists();

            if ($alreadyFailed) {
                return [
                    'ok' => false,
                    'already' => true,
                    'message' => 'Commission could not be deducted because the wallet balance is insufficient. Balance was not changed.',
                ];
            }

            Transaction::create([
                'driver_id' => $driver->id,
                'booking_id' => $bookingId,
                'type' => 'debit',
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $before,
                'description' => 'Ride commission could not be deducted for Ride #'.$bookingId,
                'reference_id' => (string) $bookingId,
                'reference_type' => 'ride',
                'category' => 'ride_commission_failed',
                'status' => 'failed',
            ]);

            return [
                'ok' => false,
                'already' => false,
                'message' => 'Commission could not be deducted because the wallet balance is insufficient. Balance was not changed.',
            ];
        }

        $after = bcsub($before, $amount, 2);
        $driver->wallet_balance = $after;
        $driver->save();

        Transaction::create([
            'driver_id' => $driver->id,
            'booking_id' => $bookingId,
            'type' => 'debit',
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $after,
            'description' => 'Ride commission for Ride #'.$bookingId,
            'reference_id' => (string) $bookingId,
            'reference_type' => 'ride',
            'category' => 'ride_commission',
            'status' => 'success',
        ]);

        DriverNotification::create([
            'driver_id' => $driver->id,
            'title' => 'Ride commission',
            'message' => '₹'.number_format((float) $amount, 2).' ride commission deducted for Ride #'.$bookingId.'.',
            'type' => 'wallet',
            'action_url' => route('driver.wallet'),
            'is_read' => false,
        ]);

        return ['ok' => true, 'already' => false, 'amount' => $amount, 'balance' => $after, 'message' => 'Commission deducted.'];
    }
}
