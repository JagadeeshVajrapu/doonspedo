<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\DriverRegistration;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Support\RideFare;
use App\Services\CloudinaryStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NearbyRideOtpTest extends TestCase
{
    use RefreshDatabase;

    private function category(string $name = 'Bike', float $base = 25, float $rate = 8): VehicleCategory
    {
        return VehicleCategory::firstOrCreate(
            ['name' => $name],
            ['base_fare' => $base, 'rate_per_km' => $rate, 'is_active' => true]
        );
    }

    private function partner(float $lat, float $lng, string $mobile, string $categoryName = 'Bike'): DriverRegistration
    {
        $driver = DriverRegistration::create([
            'name' => 'Nearby Partner',
            'mobile' => $mobile,
            'email' => $mobile.'@example.com',
            'vehicle_type' => 'bike',
            'vehicle_number' => 'UK07'.$mobile,
            'license_number' => 'LIC'.$mobile,
            'city' => 'Delhi',
            'status' => 'approved',
            'wallet_balance' => '500.00',
            'is_online' => true,
            'current_lat' => $lat,
            'current_lng' => $lng,
            'ride_preferences' => ['max_distance' => 10],
        ]);
        $category = $this->category($categoryName);
        Vehicle::create([
            'driver_id' => $driver->id,
            'vehicle_category_id' => $category->id,
            'brand' => 'Test',
            'model' => $categoryName,
            'number_plate' => 'UK'.$mobile,
            'status' => 'active',
            'verification_status' => 'approved',
        ]);

        return $driver;
    }

    private function ride(float $lat, float $lng, ?int $driverId = null, string $status = 'pending'): Booking
    {
        return Booking::create([
            'user_id' => User::factory()->create()->id,
            'driver_id' => $driverId,
            'status' => $status,
            'pickup_location' => 'Rajapuri, Delhi',
            'dropoff_location' => 'ISBT, Delhi',
            'pickup_lat' => $lat,
            'pickup_lng' => $lng,
            'dropoff_lat' => 28.6300,
            'dropoff_lng' => 77.2200,
            'vehicle_category_id' => $this->category()->id,
            'fare' => 120,
            'payment_method' => 'cash',
            'ride_otp' => '482913',
            'arrived_at' => $status === 'accepted' ? now() : null,
        ]);
    }

    public function test_only_a_nearby_partner_sees_and_can_accept_the_ride(): void
    {
        $near = $this->partner(28.6120, 77.0350, '9000002221');
        $far = $this->partner(30.3165, 78.0322, '9000002222');
        $ride = $this->ride(28.6092, 77.0320);

        $nearList = $this->withSession(['driver_id' => $near->id])
            ->getJson(route('driver.rides.requests'))
            ->assertOk()
            ->json('requests');
        $farList = $this->withSession(['driver_id' => $far->id])
            ->getJson(route('driver.rides.requests'))
            ->assertOk()
            ->json('requests');

        $this->assertSame([$ride->id], collect($nearList)->pluck('id')->all());
        $this->assertSame([], $farList);

        $this->withSession(['driver_id' => $far->id])
            ->from(route('driver.dashboard'))
            ->post(route('driver.rides.accept', $ride->id))
            ->assertRedirect(route('driver.dashboard'));

        $this->assertSame('pending', $ride->fresh()->status);
        $this->assertNull($ride->fresh()->driver_id);

        $this->withSession(['driver_id' => $near->id])
            ->post(route('driver.rides.accept', $ride->id))
            ->assertRedirect(route('driver.rides.details', $ride->id));

        $this->assertSame('accepted', $ride->fresh()->status);
        $this->assertSame($near->id, $ride->fresh()->driver_id);
    }

    public function test_same_state_is_required_even_when_the_driver_is_nearby(): void
    {
        $delhi = $this->partner(28.6120, 77.0350, '9000004441');
        $dehradun = $this->partner(28.6120, 77.0350, '9000004442');
        $dehradun->update(['city' => 'Dehradun']);
        $ride = $this->ride(28.6092, 77.0320);

        $delhiList = $this->withSession(['driver_id' => $delhi->id])
            ->getJson(route('driver.rides.requests'))
            ->assertOk()
            ->json('requests');
        $dehradunList = $this->withSession(['driver_id' => $dehradun->id])
            ->getJson(route('driver.rides.requests'))
            ->assertOk()
            ->json('requests');

        $this->assertSame([$ride->id], collect($delhiList)->pluck('id')->all());
        $this->assertSame([], $dehradunList);
        $this->assertSame('120.00', number_format((float) $delhiList[0]['fare'], 2, '.', ''));

        $this->withSession(['driver_id' => $dehradun->id])
            ->from(route('driver.dashboard'))
            ->post(route('driver.rides.accept', $ride->id))
            ->assertRedirect(route('driver.dashboard'))
            ->assertSessionHas('error');

        $this->assertSame('pending', $ride->fresh()->status);
    }

    public function test_state_names_match_after_normalizing_city_text(): void
    {
        $partner = $this->partner(30.3200, 78.0400, '9000004443');
        $partner->update(['city' => 'uttarakhand ']);
        $otherState = $this->partner(30.3200, 78.0400, '9000004444');
        $otherState->update(['city' => 'Uttar Pradesh']);
        $ride = $this->ride(30.3165, 78.0322);
        $ride->update(['pickup_location' => 'Clock Tower, Dehradun']);

        $homeList = $this->withSession(['driver_id' => $partner->id])
            ->getJson(route('driver.rides.requests'))
            ->assertOk()
            ->json('requests');
        $awayList = $this->withSession(['driver_id' => $otherState->id])
            ->getJson(route('driver.rides.requests'))
            ->assertOk()
            ->json('requests');

        $this->assertSame([$ride->id], collect($homeList)->pluck('id')->all());
        $this->assertSame([], $awayList);
    }

    public function test_a_missing_client_fare_uses_the_category_price(): void
    {
        $user = User::factory()->create();
        $category = VehicleCategory::create([
            'name' => 'Cab',
            'base_fare' => 60,
            'rate_per_km' => 15,
            'is_active' => true,
        ]);

        $this->actingAs($user)->postJson(route('rider.bookings.store'), [
            'pickup_location' => 'Connaught Place, Delhi',
            'dropoff_location' => 'Karol Bagh, Delhi',
            'service_type' => 'ride',
            'vehicle_category_id' => $category->id,
            'fare' => 0,
            'payment_method' => 'cash',
            'pickup_lat' => 28.6315,
            'pickup_lng' => 77.2167,
            'dropoff_lat' => 28.6400,
            'dropoff_lng' => 77.2200,
            'distance' => 4,
        ])->assertOk()
            ->assertJsonPath('booking.fare', 120);
    }

    public function test_the_server_fare_ignores_the_client_amount(): void
    {
        $user = User::factory()->create();
        $category = $this->category('Cab', 60, 15);
        $payload = [
            'pickup_location' => 'Clock Tower, Dehradun',
            'dropoff_location' => 'ISBT, Dehradun',
            'service_type' => 'ride',
            'vehicle_category_id' => $category->id,
            'payment_method' => 'cash',
            'pickup_lat' => 30.3165,
            'pickup_lng' => 78.0322,
            'dropoff_lat' => 30.2890,
            'dropoff_lng' => 78.0420,
            'distance' => 4.2,
        ];
        $expected = RideFare::calculate($category, 4.2, [
            'ac_preference' => 'ac',
            'service_type' => 'ride',
            'pickup_location' => 'Clock Tower, Dehradun',
            'pickup_lat' => 30.3165,
            'pickup_lng' => 78.0322,
            'dropoff_lat' => 30.2890,
            'dropoff_lng' => 78.0420,
        ]);

        foreach ([0, 0.18, 59.10, 99999, $expected] as $clientFare) {
            $user = User::factory()->create();
            $fare = $this->actingAs($user)->postJson(route('rider.bookings.store'), $payload + [
                'fare' => $clientFare,
                'ac_preference' => 'ac',
                'dropoff_location' => 'ISBT, Dehradun '.$clientFare,
                'dropoff_lat' => 30.2890 + ($clientFare / 100000),
            ])->assertOk()->json('booking.fare');

            $this->assertEquals($expected, (float) $fare);
        }
    }

    public function test_a_zero_distance_ride_is_rejected(): void
    {
        $user = User::factory()->create();
        $category = $this->category();

        $this->actingAs($user)->postJson(route('rider.bookings.store'), [
            'pickup_location' => 'Clock Tower, Dehradun',
            'dropoff_location' => 'Clock Tower, Dehradun',
            'service_type' => 'ride',
            'vehicle_category_id' => $category->id,
            'fare' => 0,
            'payment_method' => 'cash',
            'pickup_lat' => 30.3165,
            'pickup_lng' => 78.0322,
            'dropoff_lat' => 30.3165,
            'dropoff_lng' => 78.0322,
            'distance' => 0,
        ])->assertStatus(422);

        $this->assertSame(0, Booking::count());
    }

    public function test_an_immediate_repeat_does_not_create_a_second_booking(): void
    {
        $user = User::factory()->create();
        $category = $this->category();
        $payload = [
            'pickup_location' => 'Clock Tower, Dehradun',
            'dropoff_location' => 'ISBT, Dehradun',
            'service_type' => 'ride',
            'vehicle_category_id' => $category->id,
            'fare' => 999,
            'payment_method' => 'cash',
            'pickup_lat' => 30.3165,
            'pickup_lng' => 78.0322,
            'dropoff_lat' => 30.2890,
            'dropoff_lng' => 78.0420,
            'distance' => 4.2,
        ];

        $first = $this->actingAs($user)->postJson(route('rider.bookings.store'), $payload)->assertOk()->json('booking.id');
        $second = $this->actingAs($user)->postJson(route('rider.bookings.store'), $payload)->assertOk();

        $this->assertTrue($second->json('reused'));
        $this->assertSame($first, $second->json('booking.id'));
        $this->assertSame(1, Booking::count());
    }

    public function test_requests_are_limited_to_the_drivers_active_category(): void
    {
        $bike = $this->partner(30.3200, 78.0400, '9000005551', 'Bike');
        $autoDriver = $this->partner(30.3200, 78.0400, '9000005552', 'Auto');
        $bike->update(['city' => 'Dehradun']);
        $autoDriver->update(['city' => 'Dehradun']);
        $auto = $this->category('Auto', 40, 12);
        $ride = $this->ride(30.3165, 78.0322);
        $ride->update([
            'pickup_location' => 'Clock Tower, Dehradun',
            'vehicle_category_id' => $auto->id,
        ]);

        $bikeList = $this->withSession(['driver_id' => $bike->id])->getJson(route('driver.rides.requests'))->json('requests');
        $autoList = $this->withSession(['driver_id' => $autoDriver->id])->getJson(route('driver.rides.requests'))->json('requests');

        $this->assertSame([], $bikeList);
        $this->assertSame([$ride->id], collect($autoList)->pluck('id')->all());
    }

    public function test_a_rejection_hides_the_ride_from_that_driver_only(): void
    {
        $first = $this->partner(28.6120, 77.0350, '9000006661');
        $second = $this->partner(28.6120, 77.0350, '9000006662');
        $ride = $this->ride(28.6092, 77.0320);

        $this->withSession(['driver_id' => $first->id])
            ->postJson(route('driver.rides.reject', $ride->id))
            ->assertOk();

        $firstList = $this->withSession(['driver_id' => $first->id])->getJson(route('driver.rides.requests'))->json('requests');
        $secondList = $this->withSession(['driver_id' => $second->id])->getJson(route('driver.rides.requests'))->json('requests');

        $this->assertSame([], $firstList);
        $this->assertSame([$ride->id], collect($secondList)->pluck('id')->all());
        $this->assertSame('pending', $ride->fresh()->status);
    }

    public function test_arrival_success_is_visible_on_the_ride_screen(): void
    {
        $partner = $this->partner(28.6120, 77.0350, '9000007771');
        $ride = $this->ride(28.6092, 77.0320, $partner->id, 'accepted');

        $this->withSession(['driver_id' => $partner->id])
            ->from(route('driver.rides.details', $ride->id))
            ->followingRedirects()
            ->post(route('driver.rides.arrived', $ride->id))
            ->assertOk()
            ->assertSee('You have reached the rider', false)
            ->assertDontSee('482913');
    }

    public function test_partner_must_enter_the_riders_otp_after_arrival(): void
    {
        $partner = $this->partner(28.6120, 77.0350, '9000003331');
        $ride = $this->ride(28.6092, 77.0320, $partner->id, 'accepted');

        $this->withSession(['driver_id' => $partner->id])
            ->get(route('driver.rides.details', $ride->id))
            ->assertOk()
            ->assertSee('Ask the rider for the OTP')
            ->assertDontSee('482913');

        $this->withSession(['driver_id' => $partner->id])
            ->from(route('driver.rides.details', $ride->id))
            ->post(route('driver.rides.pickup', $ride->id), ['otp' => '000000'])
            ->assertRedirect(route('driver.rides.details', $ride->id));

        $this->assertSame('accepted', $ride->fresh()->status);
        $this->assertNull($ride->fresh()->ride_otp_verified_at);

        $this->withSession(['driver_id' => $partner->id])
            ->post(route('driver.rides.pickup', $ride->id), ['otp' => '482913'])
            ->assertRedirect();

        $ride->refresh();
        $this->assertSame('ongoing', $ride->status);
        $this->assertNotNull($ride->ride_otp_verified_at);
    }

    public function test_cloudinary_upload_stores_the_secure_url(): void
    {
        Config::set('services.cloudinary.cloud_name', 'example-cloud');
        Config::set('services.cloudinary.api_key', 'test-key');
        Config::set('services.cloudinary.api_secret', 'test-secret');
        Http::fake([
            'api.cloudinary.com/*' => Http::response(['secure_url' => 'https://res.cloudinary.com/example-cloud/image/upload/qr.png'], 200),
        ]);

        $url = app(CloudinaryStorage::class)->storeUploadedFile(UploadedFile::fake()->image('qr.png'), 'payment-qr');

        $this->assertSame('https://res.cloudinary.com/example-cloud/image/upload/qr.png', $url);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/example-cloud/auto/upload')
                && !str_contains($request->body(), 'test-secret');
        });
    }

    public function test_uploads_stay_on_local_storage_when_cloudinary_is_not_configured(): void
    {
        Config::set('services.cloudinary.cloud_name', '');
        Config::set('services.cloudinary.api_key', '');
        Config::set('services.cloudinary.api_secret', '');
        Storage::fake('public');

        $path = app(CloudinaryStorage::class)->storeUploadedFile(UploadedFile::fake()->image('doc.png'), 'drivers/kyc');

        Storage::disk('public')->assertExists($path);
        $this->assertStringStartsWith('drivers/kyc/', $path);
    }
}
