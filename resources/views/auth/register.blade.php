{{--
    Form register. Validasi dan pengecekan username ada di AuthController.
--}}
@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
    <section class="auth">
        <div class="auth-card glass">
            <div class="auth-head">
                <h1>Buat akun baru</h1>
                <p>Isi data di bawah untuk mulai mencatat absensi.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" novalidate>
                @csrf

                <div class="field">
                    <label for="name">Nama lengkap</label>
                    <input
                        class="input"
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Contoh: Rani Aditya"
                        autocomplete="name"
                        maxlength="60"
                        required
                        autofocus
                    >
                </div>

                <div class="field">
                    <label for="username">Username</label>
                    <input
                        class="input"
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="Huruf, angka, _ atau -"
                        autocomplete="username"
                        minlength="4"
                        maxlength="20"
                        pattern="[A-Za-z0-9_\-]+"
                        required
                    >
                    <span class="hint">4–20 karakter.</span>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <input
                            class="input"
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Minimal 8 karakter"
                            autocomplete="new-password"
                            minlength="8"
                            maxlength="100"
                            required
                        >
                        <button class="toggle-pass" type="button" data-toggle-pass="password" aria-label="Tampilkan password">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <div class="field">
                    <label for="password_confirmation">Konfirmasi password</label>
                    <input
                        class="input"
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Ulangi password"
                        autocomplete="new-password"
                        minlength="8"
                        maxlength="100"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary btn-block">Daftar</button>
            </form>

            <p class="switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
        </div>
    </section>
@endsection
