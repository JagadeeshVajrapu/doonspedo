<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\CustomerAadhaarVerification;
use App\Models\CustomerKycSubmission;
use App\Models\DriverRegistration;
use App\Models\User;
use App\Models\VehicleCategory;
use App\Services\Aadhaar\AadhaarVerificationProvider;
use App\Support\CustomerKycGate;
use App\Support\RideFare;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClientRequirementsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CustomerKycGate::$requiredOverride = null;
        parent::tearDown();
    }

    public function test_dehradun_local_rates_follow_admin_settings_and_stop_at_the_distance_limit(): void
    {
        $settings = [
            'dehradun_bike_rate_per_km' => 8,
            'dehradun_auto_rate_per_km' => 12,
            'dehradun_car_rate_per_km' => 20,
            'dehradun_max_local_km' => 40,
        ];
        $bike = $this->category('Bike', 25, 99);
        $auto = $this->category('Auto', 40, 99);
        $car = $this->category('Cab', 60, 15);

        $this->assertSame(105.0, RideFare::calculate($bike, 10, $this->dehradun(10) + ['settings' => $settings]));
        $this->assertSame(160.0, RideFare::calculate($auto, 10, $this->dehradun(10) + ['settings' => $settings]));
        $this->assertSame(260.0, RideFare::calculate($car, 10, $this->dehradun(10) + ['settings' => $settings]));

        $changed = $settings;
        $changed['dehradun_bike_rate_per_km'] = 9;
        $this->assertSame(115.0, RideFare::calculate($bike, 10, $this->dehradun(10) + ['settings' => $changed]));

        $delhi = RideFare::calculate($car, 4, [
            'settings' => $settings,
            'service_type' => 'ride',
            'pickup_location' => 'Connaught Place, Delhi',
            'pickup_lat' => 28.6315,
            'pickup_lng' => 77.2167,
        ]);
        $this->assertSame(120.0, $delhi);

        $user = User::factory()->create();
        $this->actingAs($user)->postJson(route('rider.bookings.store'), $this->payload($car, 10, 1))->assertOk();
        $this->assertSame(260.0, (float) Booking::first()->fare);

        $blocked = $this->actingAs($user)->postJson(route('rider.bookings.store'), $this->payload($car, 41, 2));
        $blocked->assertStatus(422);
        $this->assertStringContainsString('40 km Dehradun local limit', $blocked->json('message'));
        $this->assertSame(1, Booking::count());

        $shrunk = $this->actingAs(User::factory()->create())->postJson(route('rider.bookings.store'), [
            'pickup_location' => 'Clock Tower, Dehradun',
            'dropoff_location' => 'Far point',
            'service_type' => 'ride',
            'vehicle_category_id' => $car->id,
            'fare' => 1,
            'payment_method' => 'cash',
            'pickup_lat' => 30.3165,
            'pickup_lng' => 78.0322,
            'dropoff_lat' => 30.80,
            'dropoff_lng' => 78.0322,
            'distance' => 5,
        ]);
        $shrunk->assertStatus(422);
        $this->assertSame(1, Booking::count());

        $parts = RideFare::breakdown(260);
        $this->assertSame(0.0, $parts['tax']);
        $this->assertSame(5.2, $parts['service_fee']);
        $this->assertSame(260.0, $parts['total']);
    }

    public function test_customer_kyc_statuses_documents_and_aadhaar_otp_do_not_fake_verification(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user)->get(route('rider.kyc'))
            ->assertOk()
            ->assertSee('Not Submitted');

        $this->actingAs($user)->post(route('rider.kyc.store'), [
            'full_name' => 'Test User',
            'document_type' => 'pan',
            'document_number' => 'ABCDE1234F',
            'document' => UploadedFile::fake()->create('notes.txt', 20, 'text/plain'),
        ])->assertSessionHasErrors('document');

        $this->actingAs($user)->post(route('rider.kyc.store'), [
            'full_name' => 'Test User',
            'document_type' => 'aadhaar',
            'document_number' => '234567890123',
            'document' => UploadedFile::fake()->create('aadhaar.pdf', 100, 'application/pdf'),
        ])->assertSessionHas('success');

        $submission = CustomerKycSubmission::first();
        $this->assertSame('XXXX-XXXX-0123', $submission->document_number);
        $this->assertSame('pending', $submission->status);
        Storage::disk('local')->assertExists($submission->document_path);
        $this->assertStringStartsWith('customer-kyc/', $submission->document_path);

        $this->actingAs($other)->get(route('rider.kyc'))
            ->assertOk()
            ->assertDontSee('0123');

        $this->actingAs($other)->get(route('admin.customers.kyc.download', $submission))
            ->assertRedirect();

        $this->actingAs($user)->from(route('rider.kyc'))
            ->post(route('rider.kyc.aadhaar.otp'), ['aadhaar_number' => '234567890123'])
            ->assertRedirect(route('rider.kyc'))
            ->assertSessionHas('error');
        $this->assertSame(0, CustomerAadhaarVerification::where('status', 'verified')->count());

        $this->app->bind(AadhaarVerificationProvider::class, fn () => new class implements AadhaarVerificationProvider {
            public function isConfigured(): bool
            {
                return true;
            }

            public function requestOtp(string $aadhaarNumber): string
            {
                return 'provider-ref';
            }

            public function verifyOtp(string $providerReference, string $otp): bool
            {
                return $otp === '654321';
            }
        });

        $this->actingAs($user)->post(route('rider.kyc.aadhaar.otp'), ['aadhaar_number' => '234567890123'])
            ->assertSessionHas('success');
        $pending = CustomerAadhaarVerification::first();
        $this->assertSame('pending', $pending->status);
        $this->assertSame('0123', $pending->aadhaar_last4);
        $this->assertNotSame('234567890123', $pending->aadhaar_hash);

        $this->actingAs($user)->from(route('rider.kyc'))
            ->post(route('rider.kyc.aadhaar.verify'), ['otp' => '000000'])
            ->assertSessionHas('error');
        $this->assertSame('pending', $pending->fresh()->status);

        $pending->update(['expires_at' => now()->subMinute()]);
        $this->actingAs($user)->from(route('rider.kyc'))
            ->post(route('rider.kyc.aadhaar.verify'), ['otp' => '654321'])
            ->assertSessionHas('error');
        $this->assertSame('expired', $pending->fresh()->status);

        $again = CustomerAadhaarVerification::create([
            'user_id' => $user->id,
            'aadhaar_hash' => hash('sha256', 'again'),
            'aadhaar_last4' => '0123',
            'provider_reference' => 'provider-ref',
            'status' => 'pending',
            'attempts' => 5,
            'expires_at' => now()->addMinutes(5),
        ]);
        $this->actingAs($user)->from(route('rider.kyc'))
            ->post(route('rider.kyc.aadhaar.verify'), ['otp' => '654321'])
            ->assertSessionHas('error');
        $this->assertSame('failed', $again->fresh()->status);
        $this->assertSame(0, CustomerAadhaarVerification::where('status', 'verified')->count());

        CustomerKycGate::$requiredOverride = true;
        $category = $this->category('Cab', 60, 15);
        $this->actingAs($user)->postJson(route('rider.bookings.store'), $this->payload($category, 4, 3, false))
            ->assertStatus(422);
        CustomerKycGate::$requiredOverride = false;
        $this->actingAs($user)->postJson(route('rider.bookings.store'), $this->payload($category, 4, 4, false))
            ->assertOk();
    }

    public function test_customer_sign_up_has_no_back_to_website_link(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertDontSee('Back to website', false)
            ->assertSee('Create Account', false)
            ->assertSee('Login', false);

        $this->get(route('driver.register'))
            ->assertOk()
            ->assertSee('Back to website', false);
    }

    public function test_booking_and_driver_display_numbers_do_not_change_primary_keys(): void
    {
        $user = User::factory()->create();
        $category = $this->category('Bike', 25, 8);
        $first = Booking::create($this->bookingRow($user, $category));
        $second = Booking::create($this->bookingRow($user, $category));

        $this->assertSame($first->id, $first->fresh()->id);
        $this->assertSame('001', $first->displayReference());
        $this->assertSame('002', $second->displayReference());
        $this->assertNotSame($first->reference_no, $second->reference_no);
        $this->assertSame($user->id, $first->fresh()->user_id);

        $driverA = $this->driver('9100000001');
        $driverB = $this->driver('9100000002');
        $this->assertSame('001', $driverA->displayReference());
        $this->assertSame('002', $driverB->displayReference());
        $this->assertNotSame(1001, (int) $driverA->displayReference());
        $this->assertSame($driverA->id, $driverA->fresh()->id);
        $second->update(['driver_id' => $driverA->id]);
        $this->assertSame($driverA->id, $second->fresh()->driver_id);
        $this->assertSame('002', $second->fresh()->displayReference());
    }

    public function test_partner_otp_customer_numbers_earnings_and_logout(): void
    {
        $this->withSession(['mobile' => '9999900001', 'branch_id' => 1])
            ->get(route('driver.login.verifyOtpForm'))
            ->assertOk()
            ->assertDontSee('Back to login', false)
            ->assertSee('Resend OTP', false);

        $first = User::factory()->create();
        $second = User::factory()->create();
        $this->assertSame('001', $first->displayReference());
        $this->assertSame('002', $second->displayReference());
        $this->assertSame($first->id, $first->fresh()->id);

        $driver = $this->driver('9100000099');
        Booking::create(array_merge($this->bookingRow($first, $this->category('Bike', 25, 8)), [
            'driver_id' => $driver->id,
            'status' => 'completed',
            'completed_at' => null,
            'fare' => 80,
        ]));

        $this->withSession(['driver_id' => $driver->id])
            ->get(route('driver.earnings'))
            ->assertOk()
            ->assertSee('Earnings', false);

        $this->actingAs($first)->post(route('logout'))->assertRedirect(route('login'));
        $this->withSession(['driver_id' => $driver->id])
            ->post(route('driver.logout'))
            ->assertRedirect(route('driver.login'));
    }

    private function category(string $name, float $base, float $rate): VehicleCategory
    {
        return VehicleCategory::create([
            'name' => $name,
            'base_fare' => $base,
            'rate_per_km' => $rate,
            'is_active' => true,
        ]);
    }

    private function dehradun(float $km): array
    {
        return [
            'service_type' => 'ride',
            'pickup_location' => 'Clock Tower, Dehradun',
            'pickup_lat' => 30.3165,
            'pickup_lng' => 78.0322,
            'dropoff_lat' => 30.3165 + ($km / 111.32),
            'dropoff_lng' => 78.0322,
        ];
    }

    private function payload(VehicleCategory $category, float $km, int $seed, bool $dehradun = true): array
    {
        $lat = $dehradun ? 30.3165 : 28.6315;
        $lng = $dehradun ? 78.0322 : 77.2167;

        return [
            'pickup_location' => $dehradun ? 'Clock Tower, Dehradun' : 'Connaught Place, Delhi',
            'dropoff_location' => $dehradun ? 'Local drop, Dehradun' : 'Karol Bagh, Delhi',
            'service_type' => 'ride',
            'vehicle_category_id' => $category->id,
            'fare' => 1,
            'payment_method' => 'cash',
            'pickup_lat' => $lat,
            'pickup_lng' => $lng,
            'dropoff_lat' => $lat + min($km, 5) / 111.32,
            'dropoff_lng' => $lng,
            'distance' => $km,
            'notes' => 'seed '.$seed,
        ];
    }

    private function bookingRow(User $user, VehicleCategory $category): array
    {
        return [
            'user_id' => $user->id,
            'vehicle_category_id' => $category->id,
            'service_type' => 'ride',
            'status' => 'pending',
            'pickup_location' => 'Clock Tower, Dehradun',
            'dropoff_location' => 'ISBT, Dehradun',
            'pickup_lat' => 30.3165,
            'pickup_lng' => 78.0322,
            'fare' => 71,
            'payment_method' => 'cash',
        ];
    }

    private function driver(string $mobile): DriverRegistration
    {
        return DriverRegistration::create([
            'name' => 'Display Driver',
            'mobile' => $mobile,
            'email' => $mobile.'@example.com',
            'vehicle_type' => 'bike',
            'vehicle_number' => 'UK'.$mobile,
            'license_number' => 'LIC'.$mobile,
            'city' => 'Dehradun',
            'status' => 'approved',
            'wallet_balance' => '0.00',
        ]);
    }
}
