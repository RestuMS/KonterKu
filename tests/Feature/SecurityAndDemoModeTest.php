<?php

namespace Tests\Feature;

use App\Models\Kasbon;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\DemoPortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityAndDemoModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_and_demo_login_are_blocked_when_disabled(): void
    {
        config()->set('app.allow_public_registration', false);
        config()->set('app.demo_mode', false);

        // Login page harus bersih: tidak ada tombol daftar dan tidak ada tombol demo
        $loginRes = $this->get('/login');
        $loginRes->assertStatus(200);
        $loginRes->assertDontSee('Daftar Akun Baru');
        $loginRes->assertDontSee('Masuk Sebagai Demo');

        // Akses GET /register ditolak
        $this->get('/register')
            ->assertRedirect(route('login'))
            ->assertSessionHas('warning');

        // Submit POST /register ditolak dan tidak membuat user baru
        $this->post('/register', [
            'name' => 'Penyusup',
            'email' => 'penyusup@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseMissing('users', ['email' => 'penyusup@example.com']);

        // Endpoint POST /demo-login mengembalikan 404 saat DEMO_MODE=false
        $this->post('/demo-login')->assertStatus(404);
    }

    public function test_login_rate_limiting_blocks_brute_force_attempts(): void
    {
        $user = User::factory()->create([
            'email' => 'pemilik@toko.com',
            'password' => Hash::make('sandi-kuat-123'),
        ]);

        RateLimiter::clear('pemilik@toko.com|127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'pemilik@toko.com',
                'password' => 'salah-' . $i,
            ])->assertSessionHasErrors('email');
        }

        // Percobaan ke-6 langsung diblokir oleh rate limiter
        $response = $this->post('/login', [
            'email' => 'pemilik@toko.com',
            'password' => 'sandi-kuat-123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Terlalu banyak percobaan login gagal', session('errors')->first('email'));
        $this->assertGuest();

        RateLimiter::clear('pemilik@toko.com|127.0.0.1');
    }

    public function test_demo_mode_enables_one_click_login_banner_and_blocks_sensitive_actions(): void
    {
        config()->set('app.demo_mode', true);
        config()->set('app.allow_public_registration', false);
        config()->set('app.demo_email', 'demo@konterku.com');
        config()->set('app.demo_password', 'demo123');

        // Login screen tampilkan tombol 1-klik demo & sembunyikan registrasi
        $loginRes = $this->get('/login');
        $loginRes->assertStatus(200);
        $loginRes->assertSee('Masuk Sebagai Demo (1 Klik)');
        $loginRes->assertDontSee('Daftar Akun Baru');

        // Registrasi publik tetap diblokir di mode demo
        $this->get('/register')->assertRedirect(route('login'));

        // Klik tombol 1-Klik Demo Login
        $demoLoginRes = $this->post('/demo-login');
        $demoLoginRes->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('demo@konterku.com', auth()->user()->email);

        // Dashboard menampilkan banner Mode Demo Portofolio
        $dashRes = $this->get('/dashboard');
        $dashRes->assertStatus(200);
        $dashRes->assertSee('Mode Demo Portofolio');

        // Aksi sensitif: Ganti sandi diblokir di Mode Demo
        $this->put(route('settings.password'), [
            'current_password' => 'demo123',
            'password' => 'hacked123',
            'password_confirmation' => 'hacked123',
        ])->assertRedirect(route('settings.index', ['tab' => 'keamanan']))
          ->assertSessionHas('warning');

        $this->assertTrue(Hash::check('demo123', auth()->user()->fresh()->password));

        // Aksi sensitif: Ubah profil/email akun demo diblokir
        $this->put(route('settings.profile'), [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
        ])->assertRedirect(route('settings.index', ['tab' => 'keamanan']))
          ->assertSessionHas('warning');

        $this->assertEquals('demo@konterku.com', auth()->user()->fresh()->email);

        // Aksi sensitif: Unduh backup database SQL diblokir
        $this->get(route('settings.backup'))
            ->assertRedirect(route('settings.index', ['tab' => 'keamanan']))
            ->assertSessionHas('warning');
    }

    public function test_demo_portfolio_seeder_populates_realistic_data(): void
    {
        $this->seed(DemoPortfolioSeeder::class);

        $this->assertDatabaseHas('users', ['email' => 'demo@konterku.com']);
        $this->assertGreaterThanOrEqual(8, Product::count());
        $this->assertGreaterThan(10, Transaction::count());
        $this->assertEquals(3, Kasbon::count());
    }
}
