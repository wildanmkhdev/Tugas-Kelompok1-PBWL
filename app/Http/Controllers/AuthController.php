<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * AuthController
 * -----------------------------------------------------------------------------
 * Menangani login, register, dan logout.
 *
 * Data user disimpan di storage/app/data/users.json:
 * {
 *   "admin": { "name": "...", "username": "admin", "password": "<bcrypt>", "created_at": "..." }
 * }
 *
 * Semua validasi dan pengecekan dilakukan di controller ini.
 */
class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($this->currentUser($request) !== null) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Memproses login: validasi input, cek user, verifikasi password,
     * lalu simpan session.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6', 'max:100'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $username = strtolower(trim($credentials['username']));
        $users = $this->readJson('users.json');
        $user = $users[$username] ?? null;

        if (! is_array($user) || ! Hash::check($credentials['password'], $user['password'] ?? '')) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['login' => 'Username atau password salah.']);
        }

        $request->session()->regenerate();
        $request->session()->put(self::SESSION_USER_KEY, $username);
        $request->session()->put('auth_name', $user['name'] ?? $username);

        return redirect()->route('dashboard')->with('success', 'Berhasil masuk. Selamat datang!');
    }

    public function showRegister(Request $request): View|RedirectResponse
    {
        if ($this->currentUser($request) !== null) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Mendaftarkan akun baru. Username dicek duplikatnya secara manual
     * karena data disimpan dalam file JSON, bukan database.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:60'],
            'username' => ['required', 'string', 'min:4', 'max:20', 'alpha_dash'],
            'password' => ['required', 'string', 'min:8', 'max:100', 'confirmed'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.min' => 'Nama minimal 3 karakter.',
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 4 karakter.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, _ dan -.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $username = strtolower(trim($validated['username']));
        $users = $this->readJson('users.json');

        if (array_key_exists($username, $users)) {
            throw ValidationException::withMessages([
                'username' => 'Username sudah digunakan.',
            ]);
        }

        $users[$username] = [
            'name' => trim($validated['name']),
            'username' => $username,
            'password' => Hash::make($validated['password']),
            'created_at' => now()->toDateTimeString(),
        ];

        $this->writeJson('users.json', $users);

        return redirect()
            ->route('login')
            ->with('success', 'Akun berhasil dibuat. Silakan login.')
            ->with('registered_username', $username);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }
}
