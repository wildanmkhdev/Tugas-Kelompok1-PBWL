<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * HomeController
 * -----------------------------------------------------------------------------
 * Halaman "/" hanya bertugas mengarahkan pengunjung:
 * - sudah login  -> dashboard
 * - belum login  -> halaman login
 */
class HomeController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        return $this->currentUser($request) !== null
            ? redirect()->route('dashboard')
            : redirect()->route('login');
    }
}
