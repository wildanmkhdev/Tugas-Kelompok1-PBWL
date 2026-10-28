{{--
    Layout utama aplikasi absensi mahasiswa.
    -------------------------------------------------------------------------
    Desain: glassmorphism biru dengan dominasi abu-abu dan putih.
    Palet hanya memakai biru, abu-abu, putih, dan hitam.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Absensi Mahasiswa') — {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/absensi.css') }}?v={{ filemtime(public_path('css/absensi.css')) }}">
</head>
<body>
    {{-- Bulatan cahaya biru lembut di latar. --}}
    <div class="blobs" aria-hidden="true">
        <span class="blob blob-a"></span>
        <span class="blob blob-b"></span>
        <span class="blob blob-c"></span>
    </div>

    <header class="nav-wrap">
        <nav class="nav glass" aria-label="Navigasi utama">
            <a class="brand" href="{{ route('home') }}">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6L9 17l-5-5"/>
                    </svg>
                </span>
                <span>Hadir.<small>Absensi Mahasiswa</small></span>
            </a>

            <div class="nav-actions">
                @if (session('auth_user'))
                    <span class="nav-user">{{ session('auth_name', session('auth_user')) }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-ghost btn-sm" type="submit">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
                @endif
            </div>
        </nav>
    </header>

    <main class="container">
        @yield('content')
    </main>

    <footer class="footer">
        <span>Hadir. — sistem absensi mahasiswa sederhana.</span>
        <span>{{ date('Y') }}</span>
    </footer>

    <script src="{{ asset('js/absensi.js') }}?v={{ filemtime(public_path('js/absensi.js')) }}" defer></script>
</body>
</html>
