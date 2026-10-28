{{--
    Dashboard absensi mahasiswa.
    -------------------------------------------------------------------------
    Menampilkan status hari ini, tombol Hadir / Tidak Hadir, rekap singkat,
    dan 10 riwayat terbaru. Semua data berasal dari AttendanceController.
--}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <section class="page-head">
        <div>
            <h1>Halo, {{ explode(' ', $user['name'])[0] }}</h1>
            <p>{{ $todayLabel }}</p>
        </div>
        <div class="page-head-side">
            <div class="head-stat">
                <strong>{{ $summary['persen'] }}%</strong>
                <span>kehadiran</span>
            </div>
        </div>
    </section>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Kartu status absensi hari ini. --}}
    <section class="today glass">
        <div class="today-main">
            <span class="label">Absensi hari ini</span>

            @if ($todayRecord === null)
                <h2>Belum absen</h2>
                <p>Pilih status kehadiran Anda hari ini.</p>

                <div class="today-actions">
                    <form method="POST" action="{{ route('attendance.hadir') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">Hadir</button>
                    </form>
                    <form method="POST" action="{{ route('attendance.tidak-hadir') }}">
                        @csrf
                        <button type="submit" class="btn btn-ghost">Tidak Hadir</button>
                    </form>
                </div>
            @else
                <h2>{{ $todayRecord['status'] === 'hadir' ? 'Hadir' : 'Tidak Hadir' }}</h2>
                <p>Absensi tercatat pukul {{ $todayRecord['time'] ?? '-' }}.</p>

                <div class="today-actions">
                    <span class="status {{ $todayRecord['status'] === 'hadir' ? 'is-hadir' : 'is-absent' }}">
                        <i aria-hidden="true"></i>
                        {{ $todayRecord['status'] === 'hadir' ? 'Hadir' : 'Tidak Hadir' }}
                    </span>
                </div>
            @endif
        </div>

        <div class="today-side" aria-hidden="true">
            <div class="status-icon {{ $todayRecord === null ? 'is-empty' : ($todayRecord['status'] === 'hadir' ? 'is-hadir' : 'is-absent') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    @if ($todayRecord !== null && $todayRecord['status'] === 'hadir')
                        <path d="M20 6L9 17l-5-5"/>
                    @else
                        <path d="M12 8v5"/><circle cx="12" cy="17" r="0.5" fill="currentColor"/><circle cx="12" cy="12" r="9"/>
                    @endif
                </svg>
            </div>
        </div>
    </section>

    {{-- Rekap singkat. --}}
    <section class="summary" aria-label="Rekap absensi">
        <article class="summary-card glass">
            <span>Hadir</span>
            <strong>{{ $summary['hadir'] }}</strong>
            <small>hari</small>
        </article>
        <article class="summary-card glass">
            <span>Tidak Hadir</span>
            <strong>{{ $summary['tidak_hadir'] }}</strong>
            <small>hari</small>
        </article>
        <article class="summary-card glass">
            <span>Persentase</span>
            <strong>{{ $summary['persen'] }}%</strong>
            <small>kehadiran</small>
        </article>
    </section>

    {{-- Riwayat absensi. --}}
    <section class="history glass">
        <div class="history-head">
            <div>
                <h2>Riwayat absensi</h2>
                <p>10 catatan terbaru.</p>
            </div>
            <span class="count">{{ count($history) }} catatan</span>
        </div>

        @if (count($history) === 0)
            <div class="empty">
                <strong>Belum ada riwayat</strong>
                <p>Absensi hari ini akan muncul di sini.</p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Jam</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $row)
                            <tr>
                                <td>{{ $row['tanggal'] }}</td>
                                <td>{{ $row['jam'] }}</td>
                                <td>
                                    <span class="status {{ $row['status'] === 'hadir' ? 'is-hadir' : 'is-absent' }}">
                                        <i aria-hidden="true"></i>
                                        {{ $row['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
