<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\PollController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — E-Vote Kampus
|--------------------------------------------------------------------------
|
| LOKASI FILE: routes/web.php
|
| Di sini kita daftarkan semua URL yang bisa diakses lewat browser.
| Konsep penting:
| - Route::get('/path', ...)     → hanya untuk request GET (tampilkan halaman)
| - Route::post('/path', ...)    → untuk submit form (kirim data baru)
| - Route::put/patch('/path')    → untuk update data
| - Route::delete('/path')       → untuk hapus data
| - Route::resource(...)         → otomatis buat 7 route CRUD sekaligus
|
*/

use App\Http\Controllers\HomeController;

// =====================================================================
// HALAMAN UTAMA PUBLIK — siapapun bisa akses TANPA login
//
// Kenapa tidak pakai middleware 'auth'?
// Karena kita ingin pengunjung baru langsung melihat polling yang ada,
// bukan diarahkan ke halaman login yang membingungkan.
//
// Alur yang diinginkan:
// 1. User buka website → tampil halaman home dengan daftar polling
// 2. User klik "Vote Sekarang" → jika belum login → redirect ke login
// 3. Setelah login → Laravel otomatis kembali ke halaman poll tersebut
// =====================================================================
Route::get('/', [HomeController::class, 'index'])->name('home');

// =====================================================================
// ⚠️  ROUTE SEMENTARA — HAPUS SETELAH ADMIN BERHASIL DIBUAT!
// URL: /setup-admin-evote2026
// Dibuat untuk: membuat akun admin pertama di Railway production
// =====================================================================
Route::get('/setup-admin-evote2026', function () {
    // Cek apakah admin sudah ada — cegah duplikasi
    if (\App\Models\User::where('email', 'admin@kampus.ac.id')->exists()) {
        return response()->json([
            'status'  => 'sudah_ada',
            'message' => 'Admin sudah ada! Login dengan admin@kampus.ac.id / admin123',
        ]);
    }

    // Buat akun admin baru
    $admin = \App\Models\User::create([
        'name'     => 'Administrator',
        'email'    => 'admin@kampus.ac.id',
        'password' => bcrypt('admin123'),
        'nim'      => 'ADM001',
        'is_admin' => true,
    ]);

    return response()->json([
        'status'  => 'berhasil',
        'message' => 'Admin berhasil dibuat!',
        'email'   => $admin->email,
        'login'   => 'Gunakan: admin@kampus.ac.id / admin123',
    ]);
});
// =====================================================================
// ⚠️  AKHIR ROUTE SEMENTARA — HAPUS SAMPAI SINI SETELAH DIGUNAKAN!
// =====================================================================

// =====================================================================
// DASHBOARD MAHASISWA
// =====================================================================// Dashboard mahasiswa: inject polling aktif langsung ke view
Route::get('/dashboard', function () {
    $user = auth()->user();
    // Ambil 3 polling aktif terbaru untuk ditampilkan di dashboard
    $pollsAktif = \App\Models\Poll::where('status', 'aktif')
                      ->withCount('candidates')
                      ->orderBy('selesai_pada', 'asc')
                      ->take(3)
                      ->get();
    $sudahVotePollIds = \App\Models\Vote::where('user_id', $user->id)
                            ->pluck('poll_id')->toArray();
    return view('dashboard', compact('pollsAktif', 'sudahVotePollIds'));
})->middleware(['auth', 'verified'])->name('dashboard');

// =====================================================================
// ROUTE VOTING MAHASISWA
// =====================================================================
Route::middleware(['auth'])->group(function () {
    // Daftar semua polling aktif
    Route::get('/polling', [VoteController::class, 'index'])->name('polling.index');

    // Detail polling + tampilkan kandidat
    Route::get('/polling/{poll}', [VoteController::class, 'show'])->name('polling.show');

    // Proses vote — throttle: maks 3 percobaan per menit per user
    // Kenapa throttle? Lapisan keamanan ekstra agar tidak ada brute force.
    // '3,1' = 3 request per 1 menit. Lebih dari itu → error 429 Too Many Requests.
    Route::post('/polling/{poll}/vote', [VoteController::class, 'store'])
         ->middleware('throttle:vote')
         ->name('polling.vote');

    // Hasil/rekapitulasi polling
    Route::get('/polling/{poll}/hasil', [VoteController::class, 'hasil'])->name('polling.hasil');
});

// =====================================================================
// PROFILE (bawaan Breeze, biarkan saja)
// =====================================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =====================================================================
// ROUTE ADMIN — dilindungi middleware ['auth', 'admin']
//
// KENAPA dua middleware sekaligus?
// - 'auth'  → memastikan user sudah LOGIN (bawaan Breeze/Laravel)
// - 'admin' → memastikan user yang login adalah ADMIN (custom middleware kita)
// Keduanya wajib lulus. Urutan penting: cek login dulu, baru cek role.
//
// prefix('admin') → semua URL di group ini diawali /admin/...
// name('admin.')  → semua nama route diawali admin. (misal admin.polls.index)
// =====================================================================
Route::prefix('admin')
     ->middleware(['auth', 'admin'])
     ->name('admin.')
     ->group(function () {

        // Dashboard admin — ringkasan statistik
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // Audit log — rekam jejak voting
        Route::get('/audit', [AdminController::class, 'auditLog'])->name('audit');

        // ---------------------------------------------------------------
        // POLL ROUTES (Resource: index, create, store, edit, update, destroy)
        //
        // Route::resource() otomatis buat 7 route:
        // GET    /admin/polls              → index   (daftar)
        // GET    /admin/polls/create       → create  (form tambah)
        // POST   /admin/polls              → store   (simpan baru)
        // GET    /admin/polls/{poll}       → show    (detail — kita tidak pakai)
        // GET    /admin/polls/{poll}/edit  → edit    (form edit)
        // PUT    /admin/polls/{poll}       → update  (simpan perubahan)
        // DELETE /admin/polls/{poll}       → destroy (hapus)
        // ---------------------------------------------------------------
        Route::resource('polls', PollController::class)->except(['show']);

        // Route custom untuk ubah status polling (bukan bagian dari 7 standar)
        Route::patch('polls/{poll}/status', [PollController::class, 'updateStatus'])
             ->name('polls.updateStatus');

        // ---------------------------------------------------------------
        // CANDIDATE ROUTES (Nested Resource di bawah polls)
        //
        // Nested resource artinya kandidat selalu dalam konteks polling.
        // URL: /admin/polls/{poll}/candidates/{candidate}
        //
        // 'shallow' => true: route yang butuh {candidate} ID tidak perlu
        // juga menyebut {poll} dalam URL (misal edit & delete lebih singkat).
        // Tapi untuk create & index, {poll} tetap ada.
        // ---------------------------------------------------------------
        Route::resource('polls.candidates', CandidateController::class)
             ->except(['show'])
             ->shallow();
     });

// =====================================================================
// AUTH ROUTES (login, register, forgot password, dll — bawaan Breeze)
// =====================================================================
require __DIR__.'/auth.php';
