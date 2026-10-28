<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * AttendanceController
 * -----------------------------------------------------------------------------
 * Logika absensi mahasiswa yang sederhana: satu status per hari.
 *
 * Data disimpan di storage/app/data/attendances.json:
 * {
 *   "admin": {
 *     "2026-10-28": { "status": "hadir", "time": "08:12" }
 *   }
 * }
 *
 * Status hanya dua: "hadir" atau "tidak_hadir".
 */
class AttendanceController extends Controller
{
    private const HADIR = 'hadir';

    private const TIDAK_HADIR = 'tidak_hadir';

    /**
     * Menampilkan dashboard: status hari ini, rekap singkat, dan riwayat.
     */
    public function index(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfGuest($request)) {
            return $redirect;
        }

        $user = $this->currentUser($request);
        $records = $this->recordsFor($user['username']);
        $today = now()->toDateString();

        $hadir = 0;
        $tidakHadir = 0;

        foreach ($records as $record) {
            if (($record['status'] ?? '') === self::HADIR) {
                $hadir++;
            } elseif (($record['status'] ?? '') === self::TIDAK_HADIR) {
                $tidakHadir++;
            }
        }

        $total = $hadir + $tidakHadir;

        return view('dashboard', [
            'user' => $user,
            'todayRecord' => $records[$today] ?? null,
            'todayLabel' => $this->dateLabel(now()->toDateString()),
            'summary' => [
                'hadir' => $hadir,
                'tidak_hadir' => $tidakHadir,
                'persen' => $total > 0 ? (int) round(($hadir / $total) * 100) : 0,
            ],
            'history' => $this->history($records),
        ]);
    }

    /**
     * Menandai hadir untuk hari ini. Satu hari hanya boleh satu status.
     */
    public function hadir(Request $request): RedirectResponse
    {
        return $this->mark($request, self::HADIR);
    }

    /**
     * Menandai tidak hadir untuk hari ini.
     */
    public function tidakHadir(Request $request): RedirectResponse
    {
        return $this->mark($request, self::TIDAK_HADIR);
    }

    /**
     * Menyimpan status absensi hari ini.
     */
    private function mark(Request $request, string $status): RedirectResponse
    {
        if ($redirect = $this->redirectIfGuest($request)) {
            return $redirect;
        }

        $user = $this->currentUser($request);
        $username = $user['username'];
        $today = now()->toDateString();
        $attendance = $this->readJson('attendances.json');

        if (! empty($attendance[$username][$today])) {
            return redirect()
                ->route('dashboard')
                ->withErrors(['attendance' => 'Anda sudah mengisi absensi hari ini.']);
        }

        $attendance[$username][$today] = [
            'status' => $status,
            'time' => now()->format('H:i'),
        ];

        $this->writeJson('attendances.json', $attendance);

        $message = $status === self::HADIR
            ? 'Absensi tercatat: Hadir. Terima kasih!'
            : 'Absensi tercatat: Tidak Hadir.';

        return redirect()->route('dashboard')->with('success', $message);
    }

    /**
     * Mengambil seluruh absensi milik satu user.
     *
     * @return array<string, array{status?: string, time?: string}>
     */
    private function recordsFor(string $username): array
    {
        $attendance = $this->readJson('attendances.json');

        return is_array($attendance[$username] ?? null) ? $attendance[$username] : [];
    }

    /**
     * Membuat 10 riwayat terbaru untuk tabel.
     *
     * @param  array<string, array{status?: string, time?: string}>  $records
     * @return array<int, array{tanggal: string, status: string, label: string, jam: string}>
     */
    private function history(array $records): array
    {
        krsort($records);
        $history = [];

        foreach (array_slice($records, 0, 10, true) as $date => $record) {
            $status = $record['status'] ?? self::TIDAK_HADIR;

            $history[] = [
                'tanggal' => $this->dateLabel((string) $date),
                'status' => $status,
                'label' => $status === self::HADIR ? 'Hadir' : 'Tidak Hadir',
                'jam' => $record['time'] ?? '-',
            ];
        }

        return $history;
    }

    /**
     * Format tanggal menjadi contoh: "Rabu, 28 Oktober 2026".
     */
    private function dateLabel(string $date): string
    {
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $time = strtotime($date);

        return $hari[(int) date('w', $time)].', '.(int) date('j', $time).' '
            .$bulan[(int) date('n', $time)].' '.date('Y', $time);
    }
}
