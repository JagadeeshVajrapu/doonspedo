<?php

namespace Tests\Feature;

use App\Http\Controllers\Frontend\RiderBookingController;
use App\Models\Booking;
use App\Models\User;
use App\Models\VehicleCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CancelRideReasonTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancel_requires_a_predefined_reason(): void
    {
        $user = User::factory()->create();
        $category = VehicleCategory::create([
            'name' => 'Bike',
            'base_fare' => 25,
            'rate_per_km' => 8,
            'is_active' => true,
        ]);
        $booking = Booking::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'pickup_location' => 'Clock Tower, Dehradun',
            'dropoff_location' => 'ISBT, Dehradun',
            'pickup_lat' => 30.3165,
            'pickup_lng' => 78.0322,
            'dropoff_lat' => 30.2880,
            'dropoff_lng' => 78.0210,
            'vehicle_category_id' => $category->id,
            'fare' => 71,
            'payment_method' => 'cash',
        ]);

        $this->actingAs($user)
            ->postJson(route('rider.bookings.cancel', $booking->id), [])
            ->assertStatus(422);

        $this->actingAs($user)
            ->postJson(route('rider.bookings.cancel', $booking->id), ['reason' => 'Cancelled by customer'])
            ->assertStatus(422);

        $this->actingAs($user)
            ->postJson(route('rider.bookings.cancel', $booking->id), [
                'reason' => RiderBookingController::CANCEL_REASONS[0],
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $booking->refresh();
        $this->assertSame('cancelled', $booking->status);
        $this->assertStringContainsString(RiderBookingController::CANCEL_REASONS[0], (string) $booking->notes);
    }

    public function test_login_pages_hide_the_website_link_and_rider_app_lists_cancel_reasons(): void
    {
        $this->get(route('login'))->assertOk()->assertDontSee('Back to website', false);
        $this->get(route('driver.login'))->assertOk()->assertDontSee('Back to website', false);
        $this->get(route('branch.login'))->assertOk()->assertDontSee('Back to website', false);
        $this->get(route('admin.login'))->assertOk()->assertDontSee('Back to website', false);

        $user = User::factory()->create();
        $html = $this->actingAs($user)->get(route('rider.app'))->assertOk()->getContent();
        $this->assertStringContainsString('id="cancel-modal"', $html);
        foreach (RiderBookingController::CANCEL_REASONS as $reason) {
            $this->assertStringContainsString($reason, $html);
        }
        $this->assertStringContainsString('brand-logo-ring', $html);
    }
}
