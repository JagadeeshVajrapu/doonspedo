<?php

namespace Tests\Feature;

use App\Models\AccountDeletionRequest;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\Branch;
use App\Models\DriverRegistration;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VehicleCategory;
use App\Services\AccountDeletionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AccountDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_deletion_page_loads_without_login(): void
    {
        $this->get(route('account.deletion.show'))
            ->assertOk()
            ->assertSee('Request Account Deletion', false)
            ->assertSee('Doonspedo', false)
            ->assertSee('Customer', false)
            ->assertSee('Partner', false)
            ->assertSee('Submit Deletion Request', false)
            ->assertSee(route('account.deletion.store'), false);
    }

    public function test_invalid_form_submissions_are_rejected(): void
    {
        $this->from(route('account.deletion.show'))
            ->post(route('account.deletion.store'), [])
            ->assertRedirect(route('account.deletion.show'))
            ->assertSessionHasErrors(['account_type']);

        $this->from(route('account.deletion.show'))
            ->post(route('account.deletion.store'), [
                'account_type' => 'customer',
            ])
            ->assertRedirect(route('account.deletion.show'))
            ->assertSessionHasErrors();

        $this->assertDatabaseCount('account_deletion_requests', 0);
    }

    public function test_valid_request_is_recorded_and_does_not_delete_account(): void
    {
        $user = User::factory()->create([
            'email' => 'keep.me@example.com',
            'mobile' => '9000011111',
            'name' => 'Keep Me',
        ]);

        $response = $this->post(route('account.deletion.store'), [
            'account_type' => 'customer',
            'email' => 'keep.me@example.com',
            'mobile' => '9000011111',
        ]);

        $record = AccountDeletionRequest::query()->first();
        $this->assertNotNull($record);
        $this->assertSame('customer', $record->account_type);
        $this->assertSame('keep.me@example.com', $record->email);
        $this->assertSame($user->id, $record->account_id);
        $this->assertNotSame(AccountDeletionRequest::STATUS_COMPLETED, $record->status);

        $response->assertRedirect(route('account.deletion.verify', $record->reference_no));

        $this->assertSame('Keep Me', $user->fresh()->name);
        $this->assertSame('keep.me@example.com', $user->fresh()->email);
    }

    public function test_unverified_request_does_not_trigger_deletion(): void
    {
        $user = User::factory()->create([
            'email' => 'still.here@example.com',
            'mobile' => '9000022222',
        ]);

        $this->post(route('account.deletion.store'), [
            'account_type' => 'customer',
            'email' => 'still.here@example.com',
            'mobile' => '9000022222',
        ]);

        $record = AccountDeletionRequest::query()->firstOrFail();
        $this->assertFalse((bool) $record->is_verified);
        $this->assertNotSame(AccountDeletionRequest::STATUS_COMPLETED, $record->status);
        $this->assertSame('still.here@example.com', $user->fresh()->email);
    }

    public function test_otp_verification_moves_request_to_pending_review(): void
    {
        $user = User::factory()->create([
            'email' => 'otp.user@example.com',
            'mobile' => '9000033333',
        ]);

        $this->post(route('account.deletion.store'), [
            'account_type' => 'customer',
            'email' => 'otp.user@example.com',
            'mobile' => '9000033333',
        ]);

        $record = AccountDeletionRequest::query()->firstOrFail();
        $this->assertSame(AccountDeletionRequest::STATUS_PENDING_VERIFICATION, $record->status);
        $this->assertFalse((bool) $record->is_verified);

        $record->forceFill([
            'verification_code_hash' => Hash::make('654321'),
            'otp_expires_at' => now()->addMinutes(10),
            'otp_attempts' => 0,
        ])->save();

        $this->post(route('account.deletion.verify.submit', $record->reference_no), [
            'otp' => '654321',
        ])->assertRedirect(route('account.deletion.success', $record->reference_no));

        $record->refresh();
        $this->assertTrue((bool) $record->is_verified);
        $this->assertSame(AccountDeletionRequest::STATUS_PENDING_REVIEW, $record->status);
        $this->assertNull($record->verification_code_hash);
        $this->assertSame('otp.user@example.com', $user->fresh()->email);
    }

    public function test_wrong_otp_does_not_mark_request_verified(): void
    {
        $this->post(route('account.deletion.store'), [
            'account_type' => 'customer',
            'email' => 'wrong.otp@example.com',
            'mobile' => '9000033344',
        ]);

        $record = AccountDeletionRequest::query()->firstOrFail();
        $record->forceFill([
            'verification_code_hash' => Hash::make('111111'),
            'otp_expires_at' => now()->addMinutes(10),
            'otp_attempts' => 0,
        ])->save();

        $this->from(route('account.deletion.verify', $record->reference_no))
            ->post(route('account.deletion.verify.submit', $record->reference_no), [
                'otp' => '000000',
            ])
            ->assertRedirect(route('account.deletion.verify', $record->reference_no));

        $record->refresh();
        $this->assertFalse((bool) $record->is_verified);
        $this->assertSame(AccountDeletionRequest::STATUS_PENDING_VERIFICATION, $record->status);
        $this->assertSame(1, (int) $record->otp_attempts);
    }

    public function test_email_only_request_is_unverified_pending_review(): void
    {
        $user = User::factory()->create([
            'email' => 'email.only@example.com',
            'mobile' => '9000033355',
        ]);

        $this->post(route('account.deletion.store'), [
            'account_type' => 'customer',
            'email' => 'email.only@example.com',
        ])->assertRedirect();

        $record = AccountDeletionRequest::query()->firstOrFail();
        $this->assertFalse((bool) $record->is_verified);
        $this->assertSame(AccountDeletionRequest::STATUS_PENDING_REVIEW, $record->status);
        $this->assertSame($user->id, $record->account_id);
        $this->assertNotSame(AccountDeletionRequest::STATUS_COMPLETED, $record->status);
        $this->assertSame('email.only@example.com', $user->fresh()->email);
    }

    public function test_partner_request_resolves_driver_registration(): void
    {
        $branch = Branch::create(['name' => 'Dehradun', 'is_active' => 1]);
        $driver = DriverRegistration::create([
            'branch_id' => $branch->id,
            'name' => 'Partner Target',
            'mobile' => '9000033366',
            'email' => 'partner.target@example.com',
            'password' => 'secret123',
            'vehicle_type' => 'bike',
            'vehicle_number' => 'UK07TAR001',
            'license_number' => 'LIC9000033366',
            'city' => 'Dehradun',
            'status' => 'approved',
        ]);

        $this->post(route('account.deletion.store'), [
            'account_type' => 'partner',
            'email' => 'partner.target@example.com',
            'mobile' => '9000033366',
        ]);

        $record = AccountDeletionRequest::query()->firstOrFail();
        $this->assertSame('partner', $record->account_type);
        $this->assertSame($driver->id, $record->account_id);
        $this->assertFalse((bool) $record->is_verified);
    }

    public function test_admin_cannot_approve_pending_verification_otp_outstanding(): void
    {
        $admin = Admin::create(['name' => 'Admin', 'email' => 'deladmin-otp@example.com', 'password' => 'secret']);
        $record = AccountDeletionRequest::create([
            'reference_no' => 'ADR-TEST-OTPBLK',
            'account_type' => 'customer',
            'email' => 'otp.block@example.com',
            'mobile' => '9000033377',
            'status' => AccountDeletionRequest::STATUS_PENDING_VERIFICATION,
            'is_verified' => false,
            'verification_code_hash' => Hash::make('123456'),
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $this->actingAs($admin, 'admin')
            ->from(route('admin.account-deletions.show', $record->id))
            ->post(route('admin.account-deletions.approve', $record->id))
            ->assertRedirect();

        $this->assertSame(AccountDeletionRequest::STATUS_PENDING_VERIFICATION, $record->fresh()->status);
        $this->assertFalse((bool) $record->fresh()->is_verified);
    }

    public function test_rate_limiting_on_store(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('account.deletion.store'), [
                'account_type' => 'customer',
                'email' => "rate{$i}@example.com",
                'mobile' => '90000'.str_pad((string) $i, 5, '0', STR_PAD_LEFT),
            ])->assertRedirect();
        }

        $this->post(route('account.deletion.store'), [
            'account_type' => 'customer',
            'email' => 'rate-limit@example.com',
            'mobile' => '9000099999',
        ])->assertStatus(429);
    }

    public function test_guests_cannot_access_admin_deletion_tools(): void
    {
        $record = AccountDeletionRequest::create([
            'reference_no' => 'ADR-TEST-GUEST1',
            'account_type' => 'customer',
            'email' => 'guest@example.com',
            'status' => AccountDeletionRequest::STATUS_PENDING_REVIEW,
        ]);

        $this->get(route('admin.account-deletions.index'))->assertRedirect();
        $this->get(route('admin.account-deletions.show', $record->id))->assertRedirect();
        $this->post(route('admin.account-deletions.approve', $record->id))->assertRedirect();
        $this->post(route('admin.account-deletions.complete', $record->id))->assertRedirect();
    }

    public function test_customers_cannot_approve_or_complete_requests(): void
    {
        $user = User::factory()->create();
        $record = AccountDeletionRequest::create([
            'reference_no' => 'ADR-TEST-CUST1',
            'account_type' => 'customer',
            'account_id' => $user->id,
            'email' => $user->email,
            'status' => AccountDeletionRequest::STATUS_APPROVED,
            'is_verified' => true,
        ]);

        $this->actingAs($user)
            ->post(route('admin.account-deletions.approve', $record->id))
            ->assertRedirect();

        $this->actingAs($user)
            ->post(route('admin.account-deletions.complete', $record->id))
            ->assertRedirect();

        $this->assertSame(AccountDeletionRequest::STATUS_APPROVED, $record->fresh()->status);
        $this->assertSame($user->email, $user->fresh()->email);
    }

    public function test_admin_can_approve_and_complete_with_retention(): void
    {
        $admin = Admin::create(['name' => 'Admin', 'email' => 'deladmin@example.com', 'password' => 'secret']);
        $user = User::factory()->create([
            'name' => 'Delete Me',
            'email' => 'delete.me@example.com',
            'mobile' => '9000044444',
            'password' => 'old-password',
            'wallet_balance' => 50,
        ]);
        $category = VehicleCategory::firstOrCreate(
            ['name' => 'Bike'],
            ['base_fare' => 25, 'rate_per_km' => 8, 'is_active' => true]
        );
        $booking = Booking::create([
            'user_id' => $user->id,
            'status' => 'completed',
            'pickup_location' => 'A',
            'dropoff_location' => 'B',
            'pickup_lat' => 30.3,
            'pickup_lng' => 78.0,
            'dropoff_lat' => 30.4,
            'dropoff_lng' => 78.1,
            'vehicle_category_id' => $category->id,
            'fare' => 100,
            'payment_method' => 'cash',
        ]);

        $transaction = null;
        if (Schema::hasTable('transactions')) {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'type' => 'debit',
                'amount' => 100,
                'description' => 'Ride payment',
                'status' => 'success',
            ]);
        }

        $record = AccountDeletionRequest::create([
            'reference_no' => 'ADR-TEST-OK001',
            'account_type' => 'customer',
            'account_id' => $user->id,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'status' => AccountDeletionRequest::STATUS_PENDING_REVIEW,
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.account-deletions.approve', $record->id))
            ->assertRedirect();

        $this->assertSame(AccountDeletionRequest::STATUS_APPROVED, $record->fresh()->status);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.account-deletions.complete', $record->id), [
                'admin_notes' => 'Completed after verification',
            ])
            ->assertRedirect();

        $record->refresh();
        $user->refresh();
        $this->assertSame(AccountDeletionRequest::STATUS_COMPLETED, $record->status);
        $this->assertNotNull($record->completed_at);
        $this->assertSame('Deleted User', $user->name);
        $this->assertStringContainsString('deleted+customer', $user->email);
        $this->assertNull($user->mobile);
        $this->assertFalse(Hash::check('old-password', $user->password));
        $this->assertBookingAndPaymentRetained($booking, $transaction, $user->id);

        auth('admin')->logout();
        $this->flushSession();

        $this->from(route('login'))
            ->post(route('login.submit'), [
                'email' => 'delete.me@example.com',
                'password' => 'old-password',
            ])
            ->assertRedirect(route('login'));
        $this->assertGuest();
    }

    private function assertBookingAndPaymentRetained(Booking $booking, ?Transaction $transaction, int $userId): void
    {
        $this->assertTrue(Booking::query()->whereKey($booking->id)->exists());
        $this->assertSame($userId, $booking->fresh()->user_id);
        $this->assertSame(100.0, (float) $booking->fresh()->fare);
        if ($transaction) {
            $this->assertTrue(Transaction::query()->whereKey($transaction->id)->exists());
            $this->assertSame($userId, $transaction->fresh()->user_id);
            $this->assertSame(100.0, (float) $transaction->fresh()->amount);
        }
    }

    public function test_complete_is_idempotent_and_failed_complete_keeps_account(): void
    {
        $admin = Admin::create(['name' => 'Admin', 'email' => 'deladmin2@example.com', 'password' => 'secret']);
        $user = User::factory()->create([
            'email' => 'safe@example.com',
            'mobile' => '9000055555',
            'name' => 'Safe User',
        ]);

        $record = AccountDeletionRequest::create([
            'reference_no' => 'ADR-TEST-IDEMP',
            'account_type' => 'customer',
            'account_id' => $user->id,
            'email' => $user->email,
            'status' => AccountDeletionRequest::STATUS_PENDING_REVIEW,
            'is_verified' => true,
        ]);

        // Completing before approve must fail without changing the account.
        $this->actingAs($admin, 'admin')
            ->post(route('admin.account-deletions.complete', $record->id))
            ->assertRedirect();

        $this->assertSame(AccountDeletionRequest::STATUS_PENDING_REVIEW, $record->fresh()->status);
        $this->assertSame('Safe User', $user->fresh()->name);

        $record->forceFill(['status' => AccountDeletionRequest::STATUS_APPROVED])->save();
        app(AccountDeletionService::class)->complete($record->fresh(), $admin->id);
        $firstEmail = $user->fresh()->email;

        app(AccountDeletionService::class)->complete($record->fresh(), $admin->id);
        $this->assertSame($firstEmail, $user->fresh()->email);
        $this->assertSame(AccountDeletionRequest::STATUS_COMPLETED, $record->fresh()->status);
    }

    public function test_partner_deletion_anonymizes_and_blocks_login(): void
    {
        $admin = Admin::create(['name' => 'Admin', 'email' => 'deladmin3@example.com', 'password' => 'secret']);
        $branch = Branch::create(['name' => 'Dehradun', 'is_active' => 1]);
        $driver = DriverRegistration::create([
            'branch_id' => $branch->id,
            'name' => 'Partner Delete',
            'mobile' => '9000066666',
            'email' => 'partner.delete@example.com',
            'password' => 'secret123',
            'vehicle_type' => 'bike',
            'vehicle_number' => 'UK07DEL001',
            'license_number' => 'LIC9000066666',
            'city' => 'Dehradun',
            'status' => 'approved',
        ]);

        $record = AccountDeletionRequest::create([
            'reference_no' => 'ADR-TEST-PART1',
            'account_type' => 'partner',
            'account_id' => $driver->id,
            'email' => $driver->email,
            'mobile' => $driver->mobile,
            'status' => AccountDeletionRequest::STATUS_APPROVED,
            'is_verified' => true,
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.account-deletions.complete', $record->id))
            ->assertRedirect();

        $driver->refresh();
        $this->assertSame('Deleted Partner', $driver->name);
        $this->assertTrue((bool) $driver->is_blocked);
        $this->assertSame('rejected', $driver->status);
        $this->assertFalse(Hash::check('secret123', $driver->password));

        auth('admin')->logout();
        $this->flushSession();

        $this->from(route('driver.login'))
            ->post(route('driver.login.submit'), [
                'branch_id' => $branch->id,
                'email' => 'partner.delete@example.com',
                'password' => 'secret123',
            ])
            ->assertRedirect(route('driver.login'))
            ->assertSessionHasErrors(['email']);
        $this->assertNull(session('driver_id'));
    }

    public function test_existing_customer_login_still_works(): void
    {
        $user = User::factory()->create([
            'email' => 'login.ok@example.com',
            'password' => 'password',
        ]);

        $this->post(route('login.submit'), [
            'email' => 'login.ok@example.com',
            'password' => 'password',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_dashboard_and_partner_login_still_work(): void
    {
        $admin = Admin::create(['name' => 'Admin', 'email' => 'dash@example.com', 'password' => 'secret']);
        $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'))
            ->assertOk();

        auth('admin')->logout();
        $this->flushSession();

        $branch = Branch::create(['name' => 'City Center', 'is_active' => 1]);
        $driver = DriverRegistration::create([
            'branch_id' => $branch->id,
            'name' => 'Active Partner',
            'mobile' => '9000077777',
            'email' => 'partner.active@example.com',
            'password' => 'secret123',
            'vehicle_type' => 'auto',
            'vehicle_number' => 'UK07ACT001',
            'license_number' => 'LIC9000077777',
            'city' => 'Dehradun',
            'status' => 'approved',
        ]);

        $this->post(route('driver.login.submit'), [
            'branch_id' => $branch->id,
            'email' => 'partner.active@example.com',
            'password' => 'secret123',
        ])->assertRedirect(route('driver.dashboard'));

        $this->assertSame($driver->id, session('driver_id'));
    }

    public function test_migration_creates_account_deletion_requests_table(): void
    {
        $this->assertTrue(Schema::hasTable('account_deletion_requests'));
        $this->assertTrue(Schema::hasColumns('account_deletion_requests', [
            'reference_no',
            'account_type',
            'account_id',
            'email',
            'mobile',
            'status',
            'is_verified',
            'verification_code_hash',
            'processed_at',
            'completed_at',
        ]));
    }
}
