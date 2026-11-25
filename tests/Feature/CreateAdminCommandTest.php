<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_an_admin_without_deleting_existing_users(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $this->artisan('app:create-admin', ['email' => 'admin@example.com'])
            ->expectsQuestion('Password (min. 12 characters)', 'secure-password-123')
            ->assertSuccessful();

        $this->assertDatabaseHas('users', ['email' => 'existing@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'admin@example.com']);
        $this->assertSame(2, User::count());
        $this->assertTrue(
            Hash::check('secure-password-123', User::where('email', 'admin@example.com')->firstOrFail()->password)
        );
    }

    public function test_rejects_a_password_shorter_than_twelve_characters(): void
    {
        $this->artisan('app:create-admin', ['email' => 'admin@example.com'])
            ->expectsQuestion('Password (min. 12 characters)', 'short')
            ->assertFailed();

        $this->assertDatabaseMissing('users', ['email' => 'admin@example.com']);
    }

    public function test_rejects_an_invalid_email(): void
    {
        $this->artisan('app:create-admin', ['email' => 'not-an-email'])
            ->assertFailed();

        $this->assertSame(0, User::count());
    }

    public function test_updates_an_existing_users_password_without_deleting_them(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'name' => 'Existing Admin',
            'password' => 'old-password-123',
        ]);

        $this->artisan('app:create-admin', ['email' => 'admin@example.com'])
            ->expectsQuestion('Password (min. 12 characters)', 'new-password-456')
            ->assertSuccessful();

        $user = User::where('email', 'admin@example.com')->firstOrFail();

        $this->assertSame(1, User::count());
        $this->assertSame('Existing Admin', $user->name);
        $this->assertTrue(Hash::check('new-password-456', $user->password));
    }
}
