<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

/**
 * Base TestCase
 * -----------------------------------------------------------------------------
 * Semua feature test memakai folder data terpisah agar data asli pada
 * storage/app/data tidak tersentuh. Folder dibersihkan sebelum dan sesudah
 * setiap test sehingga hasilnya konsisten.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Path data khusus pengujian.
     */
    protected string $dataPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dataPath = storage_path('framework/testing/data');
        config(['app.data_path' => $this->dataPath]);

        $this->cleanDataDirectory();

        if (! is_dir($this->dataPath)) {
            mkdir($this->dataPath, 0775, true);
        }
    }

    protected function tearDown(): void
    {
        $this->cleanDataDirectory();

        parent::tearDown();
    }

    /**
     * Menulis satu user langsung ke users.json untuk kebutuhan test.
     * Password default: "password123".
     */
    protected function createUser(
        string $username = 'tester',
        string $password = 'password123',
        string $name = 'Tester Presensia'
    ): void {
        $users = [];

        if (is_file($this->dataPath.'/users.json')) {
            $decoded = json_decode((string) file_get_contents($this->dataPath.'/users.json'), true);
            $users = is_array($decoded) ? $decoded : [];
        }

        $users[$username] = [
            'name' => $name,
            'username' => $username,
            'password' => Hash::make($password),
            'created_at' => now()->toDateTimeString(),
        ];

        file_put_contents(
            $this->dataPath.'/users.json',
            json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Menghapus seluruh file JSON pada folder data pengujian.
     */
    private function cleanDataDirectory(): void
    {
        if (! is_dir($this->dataPath)) {
            return;
        }

        foreach (glob($this->dataPath.'/*') ?: [] as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
