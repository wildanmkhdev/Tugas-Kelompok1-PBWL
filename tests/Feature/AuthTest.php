<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Test alur autentikasi: login, register, proteksi dashboard, logout.
 */
class AuthTest extends TestCase
{
    public function test_halaman_awal_mengarahkan_ke_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_form_login_dan_register_dapat_diakses(): void
    {
        $this->get('/login')->assertOk()->assertSee('Selamat datang kembali');
        $this->get('/register')->assertOk()->assertSee('Buat akun baru');
    }

    public function test_register_membuat_akun_baru(): void
    {
        $response = $this->post('/register', [
            'name' => 'Rani Aditya',
            'username' => 'rani',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $users = json_decode((string) file_get_contents($this->dataPath.'/users.json'), true);
        $this->assertArrayHasKey('rani', $users);
        $this->assertNotSame('password123', $users['rani']['password']);
    }

    public function test_register_menolak_username_duplikat(): void
    {
        $this->createUser('rani');

        $response = $this->from('/register')->post('/register', [
            'name' => 'Rani Lain',
            'username' => 'RANI',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('username');
    }

    public function test_register_menolak_input_tidak_valid(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => '',
            'username' => 'ab',
            'password' => 'pendek',
            'password_confirmation' => 'beda',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['name', 'username', 'password']);
    }

    public function test_login_berhasil(): void
    {
        $this->createUser('rani', 'password123', 'Rani Aditya');

        $response = $this->post('/login', [
            'username' => 'rani',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('auth_user', 'rani');
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        $this->createUser('rani');

        $response = $this->from('/login')->post('/login', [
            'username' => 'rani',
            'password' => 'password-salah',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login');
        $response->assertSessionMissing('auth_user');
    }

    public function test_dashboard_tidak_dapat_diakses_tanpa_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_logout_menghapus_session(): void
    {
        $response = $this->withSession(['auth_user' => 'admin'])->post('/logout');

        $response->assertRedirect(route('login'));
        $response->assertSessionMissing('auth_user');
    }
}
