<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\Vote;

/**
 * HomeController — Controller halaman utama PUBLIK.
 *
 * LOKASI FILE: app/Http/Controllers/HomeController.php
 *
 * Halaman ini TIDAK memerlukan login. Semua orang bisa melihat
 * daftar polling yang sedang berjalan / sudah selesai.
 *
 * Ketika guest (belum login) mencoba vote → Laravel middleware 'auth'
 * otomatis menyimpan URL tujuan dan redirect ke login.
 * Setelah login berhasil → otomatis kembali ke halaman poll.
 */
class HomeController extends Controller
{
    /**
     * Tampilkan halaman utama publik beserta daftar polling.
     *
     * Tidak ada middleware auth di sini — siapapun bisa akses.
     */
    public function index()
    {
        // Ambil semua polling yang visible ke publik:
        // 'aktif'   → sedang berjalan (bisa divote setelah login)
        // 'selesai' → sudah tutup (bisa lihat hasil)
        // 'draft'   → TIDAK ditampilkan (belum dibuka admin)
        $polls = Poll::whereIn('status', ['aktif', 'selesai'])
                     ->withCount(['votes', 'candidates'])
                     ->orderByRaw("FIELD(status, 'aktif', 'selesai')") // aktif duluan
                     ->orderBy('selesai_pada', 'asc')
                     ->get();

        // Jika user sudah login, tandai poll mana yang sudah ia vote.
        // Jika belum login → array kosong (semua tombol tampil "Vote Sekarang").
        $sudahVotePollIds = [];
        if (auth()->check()) {
            $sudahVotePollIds = Vote::where('user_id', auth()->id())
                                   ->pluck('poll_id')
                                   ->toArray();
        }

        return view('home', compact('polls', 'sudahVotePollIds'));
    }
}
