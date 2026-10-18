<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApkBottomNavTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_out_homepage_keeps_the_menu_and_hides_account_navigation(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('navbar-toggler', false);
        $response->assertSee('id="appDrawer"', false);
        $menu = $this->menuHtml($response->getContent());
        $this->assertNotNull($menu);
        foreach (['Home', 'Customer Login', 'Partner Login', 'Partner with Us', 'Services', 'About', 'FAQ', 'Contact'] as $label) {
            $this->assertStringContainsString($label, $menu);
        }
        $this->assertStringContainsString(route('login'), $menu);
        $this->assertStringContainsString(route('driver.login'), $menu);
        $this->assertStringNotContainsString(route('admin.dashboard'), $menu);
        $response->assertDontSee('aria-label="Customer navigation"', false);
        $response->assertDontSee('aria-label="Partner navigation"', false);
    }

    public function test_login_choice_opens_the_existing_login_pages_without_a_shared_form(): void
    {
        $choice = $this->get(route('login.choose'));

        $choice->assertOk();
        $choice->assertSee(route('login'), false);
        $choice->assertSee(route('driver.login'), false);
        $choice->assertSee('Customer Login', false);
        $choice->assertSee('Partner Login', false);
        $choice->assertDontSee('name="password"', false);
        $choice->assertDontSee('name="mobile"', false);
        $choice->assertDontSee('aria-label="Customer navigation"', false);
        $choice->assertDontSee('aria-label="Partner navigation"', false);

        $customerLogin = $this->get(route('login'));
        $customerLogin->assertOk()->assertSee('Customer Login', false);
        $customerNav = $this->navHtml($customerLogin->getContent(), 'rider-bottom-nav');
        $this->assertNotNull($customerNav);
        $this->assertStringNotContainsString('>Home</span>', $customerNav);
        $this->assertStringContainsString('Book Ride', $customerNav);
        $this->assertStringContainsString('My Rides', $customerNav);
        $this->assertStringContainsString('Profile', $customerNav);
        $customerLogin->assertDontSee('aria-label="Partner navigation"', false);

        $partnerLogin = $this->get(route('driver.login'));
        $partnerLogin->assertOk()->assertSee('Partner Login', false);
        $partnerNav = $this->navHtml($partnerLogin->getContent(), 'driver-bottom-nav');
        $this->assertNotNull($partnerNav);
        $this->assertStringNotContainsString('>Home</span>', $partnerNav);
        $this->assertStringContainsString('Rides', $partnerNav);
        $this->assertStringContainsString('Wallet', $partnerNav);
        $this->assertStringContainsString('Earnings', $partnerNav);
        $this->assertStringContainsString('Profile', $partnerNav);
        $partnerLogin->assertDontSee('aria-label="Customer navigation"', false);
    }

    public function test_guest_customer_pages_go_to_the_login_choice_and_other_roles_stay_protected(): void
    {
        $this->get(route('rider.app'))->assertRedirect(route('login.choose'));
        $this->get(route('profile.edit'))->assertRedirect(route('login.choose'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->get(route('driver.dashboard'))->assertRedirect(route('driver.login'));
    }

    public function test_logout_returns_to_the_customer_login_and_hides_account_navigation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('logout'))->assertRedirect(route('login'));

        $this->get('/')
            ->assertOk()
            ->assertDontSee('aria-label="Customer navigation"', false)
            ->assertDontSee('aria-label="Partner navigation"', false);
    }

    public function test_authenticated_customer_gets_existing_customer_routes_only(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $response->assertSee('navbar-toggler', false);
        $nav = $this->navHtml($response->getContent(), 'rider-bottom-nav');
        $this->assertNotNull($nav);
        $this->assertSame(1, substr_count($response->getContent(), 'aria-label="Customer navigation"'));
        $this->assertStringContainsString(route('rider.app'), $nav);
        $this->assertStringContainsString(route('rider.bookings.index'), $nav);
        $this->assertStringContainsString(route('profile.edit'), $nav);
        $this->assertStringContainsString('Book Ride', $nav);
        $this->assertStringContainsString('My Rides', $nav);
        $this->assertStringContainsString('Profile', $nav);
        $this->assertStringNotContainsString('>Home</span>', $nav);
        $this->assertStringNotContainsString('admin', strtolower($nav));
        $this->assertStringNotContainsString('aria-label="Partner navigation"', $response->getContent());
        $menu = $this->menuHtml($response->getContent());
        $this->assertStringContainsString(route('rider.app'), $menu);
        $this->assertStringContainsString(route('rider.bookings.index'), $menu);
        $this->assertStringContainsString(route('profile.edit'), $menu);
        $this->assertStringContainsString('Logout', $menu);
        $this->assertStringNotContainsString('> Home</a>', $menu);
        $this->assertStringNotContainsString(route('driver.wallet'), $menu);
        $this->assertStringNotContainsString(route('admin.dashboard'), $menu);

        $rides = $this->actingAs($user)->get(route('rider.bookings.index'));
        $rides->assertOk();
        $this->assertSame(1, substr_count($rides->getContent(), 'aria-label="Customer navigation"'));
    }

    public function test_authenticated_partner_gets_partner_navigation_instead_of_customer_navigation(): void
    {
        $response = $this->withSession(['driver_id' => 1, 'driver_name' => 'Test Partner'])->get('/');

        $response->assertOk();
        $response->assertSee('navbar-toggler', false);
        $nav = $this->navHtml($response->getContent(), 'driver-bottom-nav');
        $this->assertNotNull($nav);
        $this->assertStringContainsString(route('driver.rides.index'), $nav);
        $this->assertStringContainsString(route('driver.wallet'), $nav);
        $this->assertStringContainsString(route('driver.earnings'), $nav);
        $this->assertStringContainsString(route('driver.profile'), $nav);
        $this->assertStringNotContainsString('>Home</span>', $nav);
        $this->assertStringNotContainsString(route('driver.dashboard'), $nav);
        $this->assertStringNotContainsString('admin', strtolower($nav));
        $this->assertStringNotContainsString('aria-label="Customer navigation"', $response->getContent());
        $menu = $this->menuHtml($response->getContent());
        $this->assertStringContainsString(route('driver.rides.index'), $menu);
        $this->assertStringContainsString(route('driver.wallet'), $menu);
        $this->assertStringContainsString(route('driver.earnings'), $menu);
        $this->assertStringContainsString(route('driver.profile'), $menu);
        $this->assertStringContainsString(route('driver.logout'), $menu);
        $this->assertStringNotContainsString('> Home</a>', $menu);
        $this->assertStringNotContainsString(route('driver.dashboard'), $menu);
        $this->assertStringNotContainsString(route('rider.app'), $menu);
        $this->assertStringNotContainsString(route('admin.dashboard'), $menu);
    }

    private function navHtml(string $html, string $class): ?string
    {
        if (!preg_match('/<nav class="'.$class.'[\s\S]*?<\/nav>/', $html, $match)) {
            return null;
        }

        return $match[0];
    }

    private function menuHtml(string $html): ?string
    {
        if (!preg_match('/<nav class="offcanvas-body" aria-label="App menu">[\s\S]*?<\/nav>/', $html, $match)) {
            return null;
        }

        return $match[0];
    }
}
