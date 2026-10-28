<?php

/*
|--------------------------------------------------------------------------
| Route Absensi Mahasiswa
|--------------------------------------------------------------------------
| Semua logika tetap berada di controller. File ini hanya memetakan URL.
|
|   GET  /                     -> redirect ke dashboard/login
|   GET  /login                -> form login
|   POST /login                -> proses login
|   GET  /register             -> form register
|   POST /register             -> proses register
|   POST /logout               -> logout
|   GET  /dashboard            -> dashboard absensi
|   POST /attendance/hadir     -> tandai hadir
|   POST /attendance/tidak-hadir -> tandai tidak hadir
|
*/

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [AttendanceController::class, 'index'])->name('dashboard');
Route::post('/attendance/hadir', [AttendanceController::class, 'hadir'])->name('attendance.hadir');
Route::post('/attendance/tidak-hadir', [AttendanceController::class, 'tidakHadir'])->name('attendance.tidak-hadir');
