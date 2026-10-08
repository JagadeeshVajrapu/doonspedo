<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\DriverDocument;
use App\Models\DriverRegistration;
use App\Models\KycRequirement;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Support\Geo;
use App\Support\RideFare;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FareDocumentsDistanceTest extends TestCase
{
    use RefreshDatabase;

    private const PICKUP_LAT = 28.6092;

    private const PICKUP_LNG = 77.0320;

    protected function tearDown(): void
    {
        Geo::$acceptanceKmOverride = null;
        parent::tearDown();
    }

    public function test_gst_is_folded_into_base_fare_and_service_fee_stays(): void
    {
        $parts = RideFare::breakdown(118);
        $this->assertSame(0.0, $parts['tax']);
        $this->assertSame(2.36, $parts['service_fee']);
        $this->assertSame(115.64, $parts['base_fare']);
        $this->assertSame(118.0, $parts['total']);
        $this->assertEqualsWithDelta(118, $parts['base_fare'] + $parts['tax'] + $parts['service_fee'], 0.001);

        $category = VehicleCategory::create([
            'name' => 'Cab',
            'base_fare' => 60,
            'rate_per_km' => 15,
            'is_active' => true,
        ]);
        $this->assertSame(120.0, RideFare::calculate($category, 4));

        $user = User::factory()->create(['mobile' => '9000001111']);
        $driver = $this->partner(self::PICKUP_LAT, self::PICKUP_LNG, '9000003331');
        $booking = Booking::create([
            'user_id' => $user->id,
            'driver_id' => $driver->id,
            'status' => 'completed',
            'pickup_location' => 'Rajapuri, Delhi',
            'dropoff_location' => 'ISBT, Delhi',
            'pickup_lat' => self::PICKUP_LAT,
            'pickup_lng' => self::PICKUP_LNG,
            'vehicle_category_id' => $category->id,
            'fare' => 118,
            'payment_method' => 'cash',
            'service_type' => 'ride',
        ]);

        $show = $this->actingAs($user)->get(route('rider.bookings.show', $booking->id))->assertOk()->getContent();
        $this->assertStringContainsString('₹115.64', $show);
        $this->assertStringContainsString('₹0.00', $show);
        $this->assertStringContainsString('₹2.36', $show);
        $this->assertStringContainsString('₹118.00', $show);
        $this->assertStringNotContainsString('18% GST', $show);

        $invoice = $this->actingAs($user)->get(route('rider.bookings.invoice', $booking->id))->assertOk()->getContent();
        $this->assertStringContainsString('₹118.00', $invoice);
        $this->assertStringContainsString('₹0.00', $invoice);
        $this->assertStringNotContainsString('GST (18%)', $invoice);
        $this->assertStringNotContainsString('TAX (18%)', $invoice);
    }

    public function test_unverified_and_rejected_documents_block_requests_and_direct_accept(): void
    {
        Geo::$acceptanceKmOverride = 3;
        $requirement = KycRequirement::create([
            'document_name' => 'Driving Licence',
            'document_type' => 'image',
            'is_required' => true,
            'is_active' => true,
        ]);
        $driver = $this->partner($this->latForKm(1), self::PICKUP_LNG, '9000003332');
        $document = DriverDocument::create([
            'driver_id' => $driver->id,
            'kyc_requirement_id' => $requirement->id,
            'document_path' => 'uploads/kyc/licence.jpg',
            'status' => 'pending',
        ]);
        $ride = $this->ride();

        $this->withSession(['driver_id' => $driver->id])
            ->getJson(route('driver.rides.requests'))
            ->assertOk()
            ->assertJsonPath('requests', [])
            ->assertJsonPath('documents_pending', true);

        $this->withSession(['driver_id' => $driver->id])
            ->postJson(route('driver.rides.accept', $ride->id))
            ->assertForbidden();
        $this->assertSame('pending', $ride->fresh()->status);

        $this->withSession(['driver_id' => $driver->id])
            ->postJson(route('driver.bids.store'), [
                'booking_id' => $ride->id,
                'bid_amount' => 100,
            ])
            ->assertForbidden();

        $admin = Admin::create(['name' => 'Admin', 'email' => 'docs@example.com', 'password' => 'secret']);
        $this->actingAs($admin, 'admin')
            ->from(route('admin.drivers.view', $driver->id))
            ->post(route('admin.drivers.documents.status', $document->id), ['status' => 'rejected'])
            ->assertRedirect(route('admin.drivers.view', $driver->id));
        $this->assertSame('rejected', $document->fresh()->status);
        $this->assertSame('Rejected', $driver->fresh()->documentVerificationLabel());

        $this->withSession(['driver_id' => $driver->id])
            ->postJson(route('driver.rides.accept', $ride->id))
            ->assertForbidden();
        $this->assertNull($ride->fresh()->driver_id);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.drivers.documents.status', $document->id), ['status' => 'approved'])
            ->assertRedirect();
        $this->assertSame('Verified', $driver->fresh()->documentVerificationLabel());

        $this->withSession(['driver_id' => $driver->id])
            ->getJson(route('driver.rides.requests'))
            ->assertOk()
            ->assertJsonPath('requests.0.id', $ride->id);

        $this->withSession(['driver_id' => $driver->id])
            ->post(route('driver.rides.accept', $ride->id))
            ->assertRedirect(route('driver.rides.details', $ride->id));
        $this->assertSame('accepted', $ride->fresh()->status);
        $this->assertSame($driver->id, $ride->fresh()->driver_id);
    }

    public function test_admin_distance_allows_up_to_the_configured_kilometres(): void
    {
        Geo::$acceptanceKmOverride = 3;
        $ride = $this->ride();

        $allowed = [
            ['km' => 1.0, 'mobile' => '9000004101'],
            ['km' => 2.5, 'mobile' => '9000004102'],
            ['km' => 3.0, 'mobile' => '9000004103'],
        ];
        foreach ($allowed as $case) {
            $km = $case['km'];
            $mobile = $case['mobile'];
            $driver = $this->partner($this->latForKm((float) $km), self::PICKUP_LNG, $mobile);
            $ids = $this->withSession(['driver_id' => $driver->id])
                ->getJson(route('driver.rides.requests'))
                ->assertOk()
                ->json('requests');
            $this->assertSame([$ride->id], collect($ids)->pluck('id')->all(), $km.' km should be eligible');
        }

        $outside = $this->partner($this->latForKm(3.1), self::PICKUP_LNG, '9000004104');
        $far = $this->partner($this->latForKm(5), self::PICKUP_LNG, '9000004105');
        foreach ([$outside, $far] as $driver) {
            $this->withSession(['driver_id' => $driver->id])
                ->getJson(route('driver.rides.requests'))
                ->assertOk()
                ->assertJsonPath('requests', []);

            $this->withSession(['driver_id' => $driver->id])
                ->postJson(route('driver.rides.accept', $ride->id))
                ->assertForbidden();
        }

        $this->assertSame('pending', $ride->fresh()->status);
        $this->assertNull($ride->fresh()->driver_id);

        Geo::$acceptanceKmOverride = 5;
        $this->withSession(['driver_id' => $far->id])
            ->getJson(route('driver.rides.requests'))
            ->assertOk()
            ->assertJsonPath('requests.0.id', $ride->id);

        $admin = Admin::create(['name' => 'Admin', 'email' => 'distance@example.com', 'password' => 'secret']);
        $this->actingAs($admin, 'admin')
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('Maximum Driver Acceptance Distance')
            ->assertSee('name="max_driver_acceptance_km"', false);

        $pendingDriver = $this->partner(self::PICKUP_LAT, self::PICKUP_LNG, '9000004199');
        $requirement = KycRequirement::create([
            'document_name' => 'Aadhaar',
            'document_type' => 'image',
            'is_required' => true,
            'is_active' => true,
        ]);
        DriverDocument::create([
            'driver_id' => $pendingDriver->id,
            'kyc_requirement_id' => $requirement->id,
            'document_path' => 'uploads/kyc/aadhaar.jpg',
            'status' => 'pending',
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.drivers.index'))
            ->assertOk()
            ->assertSee('Documents')
            ->assertSee('Pending')
            ->assertSee('Verify');

        $this->actingAs($admin, 'admin')
            ->get(route('admin.drivers.view', $pendingDriver->id))
            ->assertOk()
            ->assertSee('Uploaded KYC documents')
            ->assertSee('Verify')
            ->assertSee('Reject');
    }

    private function latForKm(float $km): float
    {
        return self::PICKUP_LAT + ($km / 6371.0) * (180 / M_PI);
    }

    private function partner(float $lat, float $lng, string $mobile): DriverRegistration
    {
        $category = VehicleCategory::firstOrCreate(
            ['name' => 'Bike'],
            ['base_fare' => 25, 'rate_per_km' => 8, 'is_active' => true]
        );
        $driver = DriverRegistration::create([
            'name' => 'Distance Partner',
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
        Vehicle::create([
            'driver_id' => $driver->id,
            'vehicle_category_id' => $category->id,
            'brand' => 'Test',
            'model' => 'Bike',
            'number_plate' => 'UK'.$mobile,
            'status' => 'active',
            'verification_status' => 'approved',
        ]);

        return $driver;
    }

    private function ride(): Booking
    {
        $category = VehicleCategory::firstOrCreate(
            ['name' => 'Bike'],
            ['base_fare' => 25, 'rate_per_km' => 8, 'is_active' => true]
        );

        return Booking::create([
            'user_id' => User::factory()->create()->id,
            'status' => 'pending',
            'pickup_location' => 'Rajapuri, Delhi',
            'dropoff_location' => 'ISBT, Delhi',
            'pickup_lat' => self::PICKUP_LAT,
            'pickup_lng' => self::PICKUP_LNG,
            'dropoff_lat' => 28.6300,
            'dropoff_lng' => 77.2200,
            'vehicle_category_id' => $category->id,
            'fare' => 120,
            'payment_method' => 'cash',
        ]);
    }
}
