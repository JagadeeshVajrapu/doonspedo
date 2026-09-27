<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Booking;
use App\Models\CommissionSetting;
use App\Models\DriverRegistration;
use App\Models\PaymentQrCode;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WalletRecharge;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerWalletTest extends TestCase
{
    use RefreshDatabase;

    private function driver(string $balance = '0.00'): DriverRegistration
    {
        return DriverRegistration::create([
            'name' => 'Partner One',
            'mobile' => '9000000001',
            'email' => 'partner@example.com',
            'vehicle_type' => 'bike',
            'vehicle_number' => 'UK07AB1234',
            'license_number' => 'LIC123',
            'city' => 'Dehradun',
            'status' => 'approved',
            'wallet_balance' => $balance,
        ]);
    }

    private function rider(): User
    {
        $n = random_int(1000, 999999);

        return User::create([
            'name' => 'Rider',
            'email' => 'rider'.$n.'@example.com',
            'password' => 'password123',
            'mobile' => '9'.str_pad((string) $n, 9, '0', STR_PAD_LEFT),
        ]);
    }

    private function booking(User $user, ?DriverRegistration $driver, string $status, string $fare = '300.00'): Booking
    {
        return Booking::create([
            'user_id' => $user->id,
            'driver_id' => $driver?->id,
            'status' => $status,
            'pickup_location' => 'A',
            'dropoff_location' => 'B',
            'fare' => $fare,
            'payment_method' => 'cash',
        ]);
    }

    public function test_admin_approval_credits_wallet_once(): void
    {
        $driver = $this->driver('0.00');
        $admin = Admin::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'secret']);
        $qr = PaymentQrCode::active();
        $recharge = WalletRecharge::create([
            'driver_id' => $driver->id,
            'amount' => '500.00',
            'payment_qr_code_id' => $qr->id,
            'payment_reference' => 'UTR123',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $service = app(WalletService::class);
        $first = $service->creditRecharge($recharge->id, $admin->id);
        $second = $service->creditRecharge($recharge->id, $admin->id);

        $this->assertTrue($first['ok']);
        $this->assertTrue($second['already']);
        $this->assertSame('500.00', $service->money($driver->fresh()->wallet_balance));
        $this->assertSame(1, Transaction::where('wallet_recharge_id', $recharge->id)->count());
        $this->assertSame('successful', $recharge->fresh()->status);
    }

    public function test_rejection_does_not_change_balance(): void
    {
        $driver = $this->driver('20.00');
        $admin = Admin::create(['name' => 'Admin', 'email' => 'admin2@example.com', 'password' => 'secret']);
        $recharge = WalletRecharge::create([
            'driver_id' => $driver->id,
            'amount' => '500.00',
            'status' => 'pending',
        ]);

        app(WalletService::class)->rejectRecharge($recharge->id, $admin->id, 'UTR could not be verified.');

        $this->assertSame('20.00', app(WalletService::class)->money($driver->fresh()->wallet_balance));
        $this->assertSame('rejected', $recharge->fresh()->status);
        $this->assertSame(0, Transaction::where('driver_id', $driver->id)->where('category', 'recharge')->count());
    }

    public function test_commission_is_debited_once_on_completion(): void
    {
        CommissionSetting::query()->update(['type' => 'fixed', 'amount' => '50.00', 'is_active' => true]);
        $driver = $this->driver('500.00');
        $ride = $this->booking($this->rider(), $driver, 'ongoing', '300.00');

        $this->withSession(['driver_id' => $driver->id])
            ->post(route('driver.rides.complete', $ride->id))
            ->assertRedirect(route('driver.dashboard'));

        $this->withSession(['driver_id' => $driver->id])
            ->post(route('driver.rides.complete', $ride->id));

        $this->assertSame('450.00', app(WalletService::class)->money($driver->fresh()->wallet_balance));
        $this->assertSame(1, Transaction::where('booking_id', $ride->id)->where('category', 'ride_commission')->count());
        $this->assertSame('completed', $ride->fresh()->status);
    }

    public function test_insufficient_balance_blocks_accept_and_does_not_go_negative(): void
    {
        CommissionSetting::query()->update(['type' => 'fixed', 'amount' => '50.00', 'is_active' => true]);
        $driver = $this->driver('20.00');
        $ride = $this->booking($this->rider(), null, 'pending', '300.00');

        $this->withSession(['driver_id' => $driver->id])
            ->from(route('driver.dashboard'))
            ->post(route('driver.rides.accept', $ride->id))
            ->assertRedirect(route('driver.dashboard'))
            ->assertSessionHas('error');

        $this->assertNull($ride->fresh()->driver_id);
        $this->assertSame('pending', $ride->fresh()->status);

        $ongoing = $this->booking($this->rider(), $driver, 'ongoing', '300.00');
        app(WalletService::class)->debitRideCommission($driver->id, $ongoing->id);
        app(WalletService::class)->debitRideCommission($driver->id, $ongoing->id);

        $this->assertSame('20.00', app(WalletService::class)->money($driver->fresh()->wallet_balance));
        $this->assertSame(0, Transaction::where('category', 'ride_commission')->where('status', 'success')->count());
    }

    public function test_submitted_payment_notifies_admin_and_credits_only_after_approval(): void
    {
        $driver = $this->driver('0.00');
        $admin = Admin::create(['name' => 'Admin', 'email' => 'admin3@example.com', 'password' => 'secret']);
        $qr = PaymentQrCode::active();
        $recharge = WalletRecharge::create([
            'driver_id' => $driver->id,
            'amount' => '100.00',
            'payment_qr_code_id' => $qr?->id,
            'status' => 'pending',
        ]);

        $this->withSession(['driver_id' => $driver->id])
            ->post(route('driver.wallet.pay.submit', $recharge->id), ['payment_reference' => 'UTR-TEST-100'])
            ->assertRedirect(route('driver.wallet'));

        $this->assertSame('0.00', app(WalletService::class)->money($driver->fresh()->wallet_balance));
        $notice = AdminNotification::query()->where('reference_id', $recharge->id)->first();
        $this->assertNotNull($notice);
        $this->assertFalse($notice->is_read);
        $this->assertStringContainsString('UTR-TEST-100', $notice->message);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.finance.recharges.approve', $recharge->id))
            ->assertRedirect();

        $this->assertSame('100.00', app(WalletService::class)->money($driver->fresh()->wallet_balance));
        $this->assertSame('successful', $recharge->fresh()->status);
        $this->assertSame(1, Transaction::where('wallet_recharge_id', $recharge->id)->count());
    }

    public function test_partner_cannot_open_another_partners_recharge(): void
    {
        $owner = $this->driver('0.00');
        $other = DriverRegistration::create([
            'name' => 'Partner Two',
            'mobile' => '9000000003',
            'email' => 'two@example.com',
            'vehicle_type' => 'bike',
            'vehicle_number' => 'UK07AB9999',
            'license_number' => 'LIC999',
            'city' => 'Dehradun',
            'status' => 'approved',
            'wallet_balance' => 0,
        ]);
        $recharge = WalletRecharge::create([
            'driver_id' => $owner->id,
            'amount' => '100.00',
            'status' => 'pending',
        ]);

        $this->withSession(['driver_id' => $other->id])
            ->get(route('driver.wallet.pay', $recharge->id))
            ->assertNotFound();
    }
}
