{{--
    Form login. Validasi dan pengecekan kredensial ada di AuthController.
--}}
@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
    <section class="auth">
        <div class="auth-card glass">
            <div class="auth-head">
                <h1>Selamat datang kembali</h1>
                <p>Masuk untuk mencatat kehadiran hari ini.</p>
            </div>

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

            <form method="POST" action="{{ route('login.store') }}" novalidate>
                @csrf

                <div class="field">
                    <label for="username">Username</label>
                    <input
                        class="input"
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username', session('registered_username')) }}"
                        placeholder="Masukkan username"
                        autocomplete="username"
                        maxlength="30"
                        required
                        autofocus
                    >
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <input
                            class="input"
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            maxlength="100"
                            required
                        >
                        <button class="toggle-pass" type="button" data-toggle-pass="password" aria-label="Tampilkan password">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Masuk</button>
            </form>

            <p class="switch">Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>

            <div class="demo">
                Akun demo: <strong>admin</strong> / <strong>admin123</strong>
            </div>
        </div>
    </section>
@endsection
