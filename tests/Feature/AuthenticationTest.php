<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk ke Akun');
        $response->assertSee('Daftar Akun Baru');
        $response->assertSee('admin@konterku.com');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'email' => 'pemilik@konter.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'pemilik@konter.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'pemilik@konter.com',
            'password' => Hash::make('password123'),
        ]);

        $this->post('/login', [
            'email' => 'pemilik@konter.com',
            'password' => 'salah123',
        ]);

        $this->assertGuest();
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Konter Baru');
        $response->assertSee('Nama Lengkap');
        $response->assertSee('Alamat Email');
        $response->assertSee('Nomor HP / WhatsApp');
    }

    public function test_new_users_can_register_and_are_automatically_authenticated(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Konter',
            'email' => 'budi@konterku.com',
            'phone' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'name' => 'Budi Konter',
            'email' => 'budi@konterku.com',
            'phone' => '081234567890',
            'role' => 'admin',
        ]);

        $user = User::where('email', 'budi@konterku.com')->first();
        $this->assertTrue(Hash::check('rahasia123', $user->password));
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'terdaftar@konterku.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'User Duplikat',
            'email' => 'terdaftar@konterku.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_fails_when_password_confirmation_does_not_match(): void
    {
        $response = $this->post('/register', [
            'name' => 'User Beda Sandi',
            'email' => 'beda@konterku.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'berbeda123',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_update_password_in_settings(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password_lama'),
        ]);

        $response = $this->actingAs($user)->put('/settings/password', [
            'current_password' => 'password_lama',
            'password' => 'password_baru123',
            'password_confirmation' => 'password_baru123',
        ]);

        $response->assertRedirect(route('settings.index', ['tab' => 'keamanan']));
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('password_baru123', $user->fresh()->password));
    }

    public function test_password_update_fails_with_incorrect_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password_lama'),
        ]);

        $response = $this->actingAs($user)->put('/settings/password', [
            'current_password' => 'password_salah',
            'password' => 'password_baru123',
            'password_confirmation' => 'password_baru123',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('password_lama', $user->fresh()->password));
    }

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Lupa Kata Sandi?');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}

