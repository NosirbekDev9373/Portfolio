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

    public function test_login_uses_the_single_admin_and_logout_works(): void
    {
        $user = User::factory()->create([
            'email' => config('portfolio.admin.email'),
            'password' => config('portfolio.admin.password'),
        ]);

        $this->assertSame(1, User::count());

        $this->post('/login', [
            'email' => $user->email,
            'password' => config('portfolio.admin.password'),
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);

        $this->get('/admin')->assertOk();

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = User::factory()->create([
            'email' => config('portfolio.admin.email'),
            'password' => config('portfolio.admin.password'),
        ]);

        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertRedirect('/login')->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_registration_route_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
    }
}
