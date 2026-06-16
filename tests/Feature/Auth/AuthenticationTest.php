<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // TC-LOGIN-01: Halaman login dapat diakses
    // =========================================================================

    public function test_halaman_login_dapat_diakses(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    // =========================================================================
    // TC-LOGIN-02: Pengguna yang sudah login diarahkan keluar dari halaman login
    // =========================================================================

    public function test_pengguna_terautentikasi_diarahkan_dari_halaman_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/');
    }

    // =========================================================================
    // TC-LOGIN-03: Login berhasil dengan kredensial valid (karyawan kasir)
    // =========================================================================

    public function test_karyawan_kasir_berhasil_login_dengan_kredensial_valid(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('penjualan.create', absolute: false));
    }

    // =========================================================================
    // TC-LOGIN-04: Login berhasil dengan kredensial valid (owner)
    // =========================================================================

    public function test_owner_berhasil_login_dan_diarahkan_ke_dashboard(): void
    {
        $user = User::factory()->owner()->create();

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    // =========================================================================
    // TC-LOGIN-05: Login gagal - field email kosong
    // =========================================================================

    public function test_login_gagal_jika_email_kosong(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email'    => '',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response
            ->assertRedirect('/login')
            ->assertSessionHasErrors([
                'email' => 'Email masih kosong, harap dilengkapi!',
            ]);
    }

    // =========================================================================
    // TC-LOGIN-06: Login gagal - field password kosong
    // =========================================================================

    public function test_login_gagal_jika_password_kosong(): void
    {
        $user = User::factory()->create();

        $response = $this->from('/login')->post('/login', [
            'email'    => $user->email,
            'password' => '',
        ]);

        $this->assertGuest();
        $response
            ->assertRedirect('/login')
            ->assertSessionHasErrors([
                'password' => 'Password masih kosong, harap dilengkapi!',
            ]);
    }

    // =========================================================================
    // TC-LOGIN-07: Login gagal - email dan password sama-sama kosong
    // =========================================================================

    public function test_login_gagal_jika_email_dan_password_kosong(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email'    => '',
            'password' => '',
        ]);

        $this->assertGuest();
        $response
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email', 'password']);
    }

    // =========================================================================
    // TC-LOGIN-08: Login gagal - format email tidak valid
    // =========================================================================

    public function test_login_gagal_jika_format_email_tidak_valid(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email'    => 'bukan-format-email',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email']);
    }

    // =========================================================================
    // TC-LOGIN-09: Login gagal - email tidak terdaftar di sistem
    // =========================================================================

    public function test_login_gagal_jika_email_tidak_terdaftar(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email'    => 'tidak-terdaftar@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response
            ->assertRedirect('/login')
            ->assertSessionHasErrors([
                'email' => 'Email yang Anda masukkan salah, silahkan cek kembali!',
            ]);
    }

    // =========================================================================
    // TC-LOGIN-10: Login gagal - password salah
    // =========================================================================

    public function test_login_gagal_jika_password_salah(): void
    {
        $user = User::factory()->create();

        $response = $this->from('/login')->post('/login', [
            'email'    => $user->email,
            'password' => 'password-salah',
        ]);

        $this->assertGuest();
        $response
            ->assertRedirect('/login')
            ->assertSessionHasErrors([
                'password' => 'Password yang Anda masukkan salah. Silakan coba lagi.',
            ]);
    }

    // =========================================================================
    // TC-LOGIN-11: Rate limiting - akun dikunci setelah 5 percobaan gagal
    // =========================================================================

    public function test_login_dikunci_setelah_terlalu_banyak_percobaan_gagal(): void
    {
        RateLimiter::clear('login');

        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->from('/login')->post('/login', [
                'email'    => $user->email,
                'password' => 'password-salah',
            ]);
        }

        $response = $this->from('/login')->post('/login', [
            'email'    => $user->email,
            'password' => 'password-salah',
        ]);

        $this->assertGuest();
        $response
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email']);
    }

    // =========================================================================
    // TC-LOGIN-12: Sesi diregenerasi setelah login berhasil (keamanan session fixation)
    // =========================================================================

    public function test_sesi_diregenerasi_setelah_login_berhasil(): void
    {
        $user = User::factory()->create();

        $sessionIdSebelum = session()->getId();

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $sessionIdSesudah = session()->getId();

        $this->assertNotEquals($sessionIdSebelum, $sessionIdSesudah);
    }

    // =========================================================================
    // TC-LOGIN-13: Logout berhasil dan sesi dihapus
    // =========================================================================

    public function test_pengguna_berhasil_logout_dan_sesi_dihapus(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    // =========================================================================
    // TC-LOGIN-14: Pengguna yang belum login tidak bisa mengakses halaman logout
    // =========================================================================

    public function test_pengguna_tidak_terautentikasi_tidak_bisa_akses_logout(): void
    {
        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    // =========================================================================
    // TC-LOGIN-15: Pengguna yang belum login diarahkan ke halaman login
    //              ketika mengakses halaman yang dilindungi
    // =========================================================================

    public function test_pengguna_tidak_terautentikasi_diarahkan_ke_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}
