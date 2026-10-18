<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\DriverRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerPasswordAndLogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_can_register_with_password_and_login_with_branch_email_password(): void
    {
        $branch = Branch::create([
            'name' => 'City Center',
            'is_active' => 1,
        ]);

        $driver = DriverRegistration::create([
            'branch_id' => $branch->id,
            'name' => 'Password Partner',
            'mobile' => '9000012345',
            'email' => 'partner.pass@example.com',
            'password' => 'secret123',
            'vehicle_type' => 'bike',
            'vehicle_number' => 'UK07AB1234',
            'license_number' => 'LIC9000012345',
            'city' => 'Dehradun',
            'status' => 'approved',
        ]);

        $this->assertNotEmpty($driver->fresh()->password);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('secret123', $driver->fresh()->password));

        $this->get(route('driver.login'))
            ->assertOk()
            ->assertSee('Password', false)
            ->assertSee(route('driver.login.submit'), false);

        $this->post(route('driver.login.submit'), [
            'branch_id' => $branch->id,
            'email' => 'partner.pass@example.com',
            'password' => 'secret123',
        ])->assertRedirect(route('driver.dashboard'));

        $this->assertSame($driver->id, session('driver_id'));
    }

    public function test_customer_and_partner_logout_go_to_their_login_pages(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Customer Login', false);

        $branch = Branch::create(['name' => 'City Center', 'is_active' => 1]);
        $driver = DriverRegistration::create([
            'branch_id' => $branch->id,
            'name' => 'Logout Partner',
            'mobile' => '9000099999',
            'email' => 'partner.logout@example.com',
            'password' => 'secret123',
            'vehicle_type' => 'auto',
            'vehicle_number' => 'UK07XY9999',
            'license_number' => 'LIC9000099999',
            'city' => 'Dehradun',
            'status' => 'approved',
        ]);

        $this->withSession(['driver_id' => $driver->id, 'driver_name' => $driver->name])
            ->post(route('driver.logout'))
            ->assertRedirect(route('driver.login'));

        $this->get(route('driver.login'))
            ->assertOk()
            ->assertSee('Partner Login', false)
            ->assertSee('Logged out successfully', false);
    }

    public function test_expired_session_posts_redirect_to_the_matching_login_page(): void
    {
        $this->withSession(['_token' => 'stale-token'])
            ->post(route('logout'), ['_token' => 'wrong-token'])
            ->assertRedirect(route('login'));

        $this->withSession(['_token' => 'stale-token'])
            ->post(route('driver.logout'), ['_token' => 'wrong-token'])
            ->assertRedirect(route('driver.login'));
    }
}
