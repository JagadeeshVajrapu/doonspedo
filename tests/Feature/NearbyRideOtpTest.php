<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\DriverRegistration;
use App\Models\User;
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

    private function partner(float $lat, float $lng, string $mobile): DriverRegistration
    {
        return DriverRegistration::create([
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
