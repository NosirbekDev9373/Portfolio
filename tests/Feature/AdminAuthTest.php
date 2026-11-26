<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_admin(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_login_and_logout_work(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'secure-password-123',
        ]);

        $this->assertSame(1, User::count());

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'secure-password-123',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);

        $this->get('/admin')->assertOk();

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'secure-password-123',
        ]);

        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertRedirect('/login')->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_page_renders(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('name="robots" content="noindex"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('type="submit"', false)
            ->assertSee('Log in')
            ->assertDontSee('fonts.bunny.net');
    }

    public function test_registration_route_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_removed_breeze_routes_are_unavailable(): void
    {
        $routes = [
            '/register',
            '/forgot-password',
            '/reset-password/test',
            '/verify-email',
            '/confirm-password',
            '/profile',
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertNotFound();
        }
    }
}
