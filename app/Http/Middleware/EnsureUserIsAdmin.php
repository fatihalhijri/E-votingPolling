<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware EnsureUserIsAdmin
 *
 * LOKASI FILE: app/Http/Middleware/EnsureUserIsAdmin.php
 *
 * CARA KERJA:
 * Middleware ini berjalan SEBELUM request sampai ke controller admin.
 * Ia memeriksa dua hal:
 * 1. Apakah user sudah login? (via Auth::check())
 * 2. Apakah role user adalah 'admin'? (via $request->user()->isAdmin())
 *
 * Kalau salah satu tidak terpenuhi → tolak akses.
 *
 * KENAPA tidak cukup hanya middleware 'auth' bawaan Laravel?
 * Middleware 'auth' hanya cek apakah user sudah LOGIN.
 * Tapi kita perlu cek lebih jauh: apakah user yang login itu ADMIN?
 * Mahasiswa yang sudah login pun tidak boleh akses halaman admin.
 * Itulah kenapa kita perlu middleware tambahan ini.
 *
 * CARA DAFTARKAN: Di bootstrap/app.php dengan alias 'admin'.
 * CARA PAKAI DI ROUTE: Route::middleware(['auth', 'admin'])->group(...)
 */
class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek 1: Apakah user sudah login?
        // Kalau belum login, Auth::user() akan null, jadi isAdmin() akan error.
        // redirect ke login adalah perilaku standar yang diharapkan user.
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Cek 2: Apakah user yang login adalah admin?
        // isAdmin() adalah helper method yang kita buat di model User.
        // Kalau bukan admin (mahasiswa yang coba akses URL admin secara langsung),
        // kembalikan error 403 Forbidden dengan pesan yang jelas.
        if (!$request->user()->isAdmin()) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk administrator.');
        }

        // Kalau lolos kedua pengecekan → lanjutkan request ke controller
        return $next($request);
    }
}
