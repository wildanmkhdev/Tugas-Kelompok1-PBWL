<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * Controller (base)
 * -----------------------------------------------------------------------------
 * Kelas dasar untuk seluruh controller aplikasi.
 *
 * Sesuai ketentuan tugas, semua logika bisnis berada di controller. Karena
 * aplikasi ini tidak memakai database (ekstensi PDO SQLite tidak tersedia),
 * penyimpanan data dilakukan pada file JSON di storage/app/data.
 *
 * Helper di kelas ini sengaja dibuat generik agar tidak terjadi duplikasi kode
 * pembacaan/penulisan file di AuthController dan AttendanceController.
 *
 * Keamanan & integritas data:
 * - Penulisan memakai flock(LOCK_EX) supaya dua request yang bersamaan tidak
 *   saling menimpa data.
 * - Pembacaan memakai flock(LOCK_SH) supaya tidak membaca file yang sedang
 *   ditulis (partial read).
 * - Data ditulis dengan JSON_PRETTY_PRINT agar mudah diperiksa manual.
 */
abstract class Controller
{
    /**
     * Kunci session untuk username yang sedang login.
     * Dipakai bersama oleh AuthController dan AttendanceController.
     */
    protected const SESSION_USER_KEY = 'auth_user';

    /**
     * Mengembalikan path absolut sebuah file data.
     *
     * @param  string  $file  Nama file, contoh: users.json
     */
    protected function dataPath(string $file): string
    {
        return rtrim((string) config('app.data_path'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$file;
    }

    /**
     * Membaca file JSON dan mengembalikan array.
     *
     * Jika file belum ada, kosong, atau isinya bukan JSON valid, method ini
     * mengembalikan array kosong sehingga aplikasi tetap berjalan (fail-safe).
     *
     * @return array<string, mixed>
     */
    protected function readJson(string $file): array
    {
        $path = $this->dataPath($file);

        // File belum pernah dibuat: anggap data kosong.
        if (! is_file($path)) {
            return [];
        }

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return [];
        }

        try {
            // LOCK_SH: boleh dibaca banyak proses, tetapi menunggu proses
            // yang sedang menulis (LOCK_EX) selesai.
            flock($handle, LOCK_SH);
            $contents = stream_get_contents($handle);
            flock($handle, LOCK_UN);
        } finally {
            fclose($handle);
        }

        if ($contents === false || trim($contents) === '') {
            return [];
        }

        $decoded = json_decode($contents, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Menulis array ke file JSON secara aman.
     *
     * @param  array<string, mixed>  $data
     */
    protected function writeJson(string $file, array $data): void
    {
        $path = $this->dataPath($file);
        $directory = dirname($path);

        // Pastikan folder data tersedia.
        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException('Tidak dapat membuat folder data: '.$directory);
        }

        $json = json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        if ($json === false) {
            throw new RuntimeException('Gagal mengubah data menjadi JSON.');
        }

        $handle = fopen($path, 'cb');

        if ($handle === false) {
            throw new RuntimeException('Tidak dapat membuka file data: '.$path);
        }

        try {
            // Kunci eksklusif selama proses tulis agar data tidak korup.
            if (! flock($handle, LOCK_EX)) {
                throw new RuntimeException('Tidak dapat mengunci file data: '.$path);
            }

            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, $json);
            fflush($handle);

            flock($handle, LOCK_UN);
        } finally {
            fclose($handle);
        }
    }

    /**
     * Mengambil data user yang sedang login berdasarkan session.
     *
     * @return array{name: string, username: string, password: string, created_at: string}|null
     */
    protected function currentUser(Request $request): ?array
    {
        $username = $request->session()->get(self::SESSION_USER_KEY);

        if (! is_string($username) || $username === '') {
            return null;
        }

        $users = $this->readJson('users.json');

        return isset($users[$username]) && is_array($users[$username])
            ? $users[$username]
            : null;
    }

    /**
     * Mengarahkan user ke halaman login jika belum terautentikasi.
     *
     * Dipakai di awal method controller yang membutuhkan login, sehingga
     * proteksi halaman tetap konsisten dan berada di controller.
     */
    protected function redirectIfGuest(Request $request): ?RedirectResponse
    {
        if ($this->currentUser($request) !== null) {
            return null;
        }

        return redirect()
            ->route('login')
            ->withErrors(['login' => 'Silakan masuk terlebih dahulu untuk melanjutkan.']);
    }
}
