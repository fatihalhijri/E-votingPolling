<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Http\Request;

/**
 * AdminController — Dashboard utama dan Audit Log untuk admin.
 *
 * LOKASI FILE: app/Http/Controllers/Admin/AdminController.php
 *
 * Metode:
 * - dashboard() → statistik ringkas: total polling, vote, mahasiswa aktif
 * - auditLog()  → log siapa yang sudah vote (TANPA menampilkan pilihan kandidat)
 */
class AdminController extends Controller
{
    /**
     * Halaman dashboard admin: statistik ringkas.
     *
     * CATATAN PERFORMA:
     * Kita pakai query terpisah yang simpel agar mudah dipahami.
     * Di production skala besar, gunakan cache (Cache::remember) agar
     * tidak query DB setiap kali admin buka dashboard.
     */
    public function dashboard()
    {
        // Statistik utama
        $stats = [
            // Total semua polling (semua status)
            'total_polling'   => Poll::count(),

            // Total polling yang sedang aktif
            'polling_aktif'   => Poll::where('status', 'aktif')->count(),

            // Total suara yang sudah masuk ke semua polling
            'total_suara'     => Vote::count(),

            // Total mahasiswa yang terdaftar (role mahasiswa)
            'total_mahasiswa' => User::where('role', 'mahasiswa')->count(),

            // Berapa mahasiswa yang sudah pernah vote (unique user_id di tabel votes)
            // distinct() → hanya hitung user_id yang unik, bukan total baris votes
            'mahasiswa_sudah_vote' => Vote::distinct('user_id')->count('user_id'),
        ];

        // Polling aktif beserta jumlah suara dan kandidat (untuk mini-tabel di dashboard)
        $pollsAktif = Poll::where('status', 'aktif')
                          ->withCount(['votes', 'candidates'])
                          ->orderBy('selesai_pada', 'asc')
                          ->get();

        // 5 suara terakhir yang masuk (untuk activity feed)
        $suaraTerbaru = Vote::with(['user', 'poll'])
                            ->orderBy('voted_at', 'desc')
                            ->take(5)
                            ->get();

        return view('admin.dashboard', compact('stats', 'pollsAktif', 'suaraTerbaru'));
    }

    /**
     * Audit Log — rekam jejak aktivitas voting.
     *
     * PRINSIP SECRET BALLOT:
     * Log ini menampilkan SIAPA sudah vote di polling APA dan KAPAN,
     * tapi TIDAK menampilkan kandidat mana yang dipilih.
     * Ini menjaga kerahasiaan pilihan mahasiswa (secret ballot principle).
     *
     * Kenapa audit log tetap perlu?
     * Untuk verifikasi integritas: admin bisa cek apakah ada user yang vote
     * lebih dari sekali (seharusnya tidak bisa karena ada unique constraint),
     * atau cek timestamp mencurigakan.
     */
    public function auditLog(Request $request)
    {
        // Query dasar: ambil semua vote dengan relasi user dan poll
        $query = Vote::with(['user', 'poll'])
                     ->orderBy('voted_at', 'desc');

        // Filter berdasarkan polling tertentu (jika admin pilih dari dropdown)
        if ($request->filled('poll_id')) {
            $query->where('poll_id', $request->poll_id);
        }

        // Paginasi 20 baris per halaman
        $votes = $query->paginate(20)->withQueryString();

        // Daftar polling untuk dropdown filter
        $polls = Poll::orderBy('created_at', 'desc')->get();

        return view('admin.audit', compact('votes', 'polls'));
    }
}
