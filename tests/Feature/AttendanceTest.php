<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Test absensi sederhana: hadir, tidak hadir, duplikasi, dan proteksi login.
 */
class AttendanceTest extends TestCase
{
    public function test_dashboard_menampilkan_status_belum_absen(): void
    {
        $this->createUser('rani', 'password123', 'Rani Aditya');

        $response = $this->withSession(['auth_user' => 'rani', 'auth_name' => 'Rani Aditya'])
            ->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Rani');
        $response->assertSee('Belum absen');
    }

    public function test_menandai_hadir(): void
    {
        $this->createUser('rani');

        $response = $this->withSession(['auth_user' => 'rani'])
            ->post('/attendance/hadir');

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $attendance = json_decode((string) file_get_contents($this->dataPath.'/attendances.json'), true);
        $today = now()->toDateString();

        $this->assertSame('hadir', $attendance['rani'][$today]['status']);
        $this->assertNotEmpty($attendance['rani'][$today]['time']);
    }

    public function test_menandai_tidak_hadir(): void
    {
        $this->createUser('rani');

        $response = $this->withSession(['auth_user' => 'rani'])
            ->post('/attendance/tidak-hadir');

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $attendance = json_decode((string) file_get_contents($this->dataPath.'/attendances.json'), true);
        $today = now()->toDateString();

        $this->assertSame('tidak_hadir', $attendance['rani'][$today]['status']);
    }

    public function test_absensi_ganda_ditolak(): void
    {
        $this->createUser('rani');

        $session = ['auth_user' => 'rani'];
        $this->withSession($session)->post('/attendance/hadir');

        $response = $this->withSession($session)
            ->from('/dashboard')
            ->post('/attendance/tidak-hadir');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHasErrors('attendance');
    }

    public function test_absensi_tidak_dapat_dilakukan_tanpa_login(): void
    {
        $this->post('/attendance/hadir')->assertRedirect(route('login'));
    }
}
