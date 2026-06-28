<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Poll;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * VoteController — Controller utama untuk mahasiswa melakukan voting.
 *
 * LOKASI FILE: app/Http/Controllers/VoteController.php
 *
 * Alur voting mahasiswa:
 * 1. index()  → tampilkan daftar semua polling yang sedang aktif
 * 2. show()   → tampilkan detail polling + kartu kandidat
 *              (kalau sudah vote → tampilkan halaman konfirmasi sudah vote)
 * 3. store()  → proses vote: simpan ke DB, cegah double vote
 * 4. hasil()  → tampilkan hasil/persentase setelah mahasiswa vote
 */
class VoteController extends Controller
{
    /**
     * Tampilkan daftar semua polling aktif untuk mahasiswa.
     *
     * CATATAN: Kita tampilkan polling dengan status 'aktif' DAN dalam rentang waktu.
     * Jika polling aktif tapi waktunya sudah lewat, tetap tidak ditampilkan.
     */
    public function index()
    {
        $user = auth()->user();

        // Ambil semua polling berstatus 'aktif'
        // with('candidates') → eager load untuk menampilkan jumlah kandidat
        // withCount('votes') → untuk tampilkan total suara masuk
        $polls = Poll::where('status', 'aktif')
                     ->withCount(['votes', 'candidates'])
                     ->orderBy('selesai_pada', 'asc') // yang hampir berakhir tampil duluan
                     ->get();

        // Untuk setiap polling, tandai apakah user ini sudah vote
        // Ini efisien: 1 query untuk semua poll_id sekaligus
        $sudahVotePollIds = Vote::where('user_id', $user->id)
                                ->pluck('poll_id')
                                ->toArray();

        return view('polling.index', compact('polls', 'sudahVotePollIds'));
    }

    /**
     * Tampilkan detail satu polling + daftar kandidat.
     *
     * Jika mahasiswa sudah vote → redirect ke halaman konfirmasi sudah vote.
     * Jika polling sudah tidak aktif → tampilkan pesan informatif.
     */
    public function show(Poll $poll)
    {
        $user = auth()->user();

        // Cek apakah polling ini masih bisa diakses
        // (status aktif DAN masih dalam rentang waktu)
        if (!$poll->sedangAktif()) {
            return view('polling.tidak-aktif', compact('poll'));
        }

        // Cek apakah mahasiswa ini sudah vote di polling ini
        $sudahVote = $user->sudahVote($poll->id);

        if ($sudahVote) {
            // Ambil data vote user ini untuk ditampilkan di halaman konfirmasi
            $voteUser = Vote::where('poll_id', $poll->id)
                            ->where('user_id', $user->id)
                            ->with('candidate')
                            ->firstOrFail();

            return view('polling.sudah-vote', compact('poll', 'voteUser'));
        }

        // Ambil semua kandidat, diurutkan nomor urut, dengan jumlah suara
        $candidates = $poll->candidates()->withCount('votes')->get();

        return view('polling.show', compact('poll', 'candidates'));
    }

    /**
     * Proses vote mahasiswa — simpan suara ke database.
     *
     * KEAMANAN BERLAPIS:
     * 1. Validasi candidate_id ada dan milik poll ini
     * 2. Cek sudah vote via PHP (user->sudahVote())
     * 3. Unique constraint di database (last line of defense)
     * 4. DB::transaction() → kalau ada error di tengah, rollback otomatis
     */
    public function store(Request $request, Poll $poll)
    {
        $user = auth()->user();

        // Pengecekan 1: Polling masih aktif?
        if (!$poll->sedangAktif()) {
            return back()->with('error', 'Maaf, polling ini sudah tidak aktif.');
        }

        // Pengecekan 2: Validasi candidate_id
        // 'exists:candidates,id' → cek ke tabel candidates, kolom id
        // Rule custom: kandidat harus milik polling ini (bukan kandidat polling lain)
        $request->validate([
            'candidate_id' => [
                'required',
                'integer',
                // Validasi bahwa kandidat ini memang milik poll yang sedang dibuka
                function ($attribute, $value, $fail) use ($poll) {
                    $ada = $poll->candidates()->where('id', $value)->exists();
                    if (!$ada) {
                        $fail('Kandidat tidak valid untuk polling ini.');
                    }
                },
            ],
        ], [
            'candidate_id.required' => 'Pilih kandidat terlebih dahulu.',
        ]);

        // Pengecekan 3: Sudah vote? (PHP level)
        if ($user->sudahVote($poll->id)) {
            return back()->with('error', 'Anda sudah memberikan suara di polling ini.');
        }

        // Simpan vote dalam transaksi database
        // Kenapa pakai DB::transaction()?
        // Jika ada error di tengah (misal koneksi putus), semua perubahan
        // dibatalkan otomatis — data tidak setengah-setengah tersimpan.
        try {
            DB::transaction(function () use ($request, $poll, $user) {
                Vote::create([
                    'poll_id'      => $poll->id,
                    'candidate_id' => $request->candidate_id,
                    'user_id'      => $user->id,
                    'voted_at'     => now(),
                ]);
            });

            return redirect()->route('polling.show', $poll)
                             ->with('vote_success', true);

        } catch (\Illuminate\Database\QueryException $e) {
            // Tangkap error unique constraint violation (kode 23000)
            // Ini terjadi jika ada race condition — 2 request bersamaan lolos PHP check
            if ($e->getCode() === '23000') {
                return back()->with('error', 'Suara Anda sudah tercatat. Double voting tidak diizinkan.');
            }
            // Error lain yang tidak terduga
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    /**
     * Tampilkan hasil/rekapitulasi suara polling.
     *
     * Hanya mahasiswa yang sudah vote yang bisa lihat hasil detail.
     * Untuk yang belum vote, redirect ke halaman vote.
     */
    public function hasil(Poll $poll)
    {
        $user = auth()->user();

        // Ambil semua kandidat beserta jumlah suaranya
        $candidates = $poll->candidates()
                           ->withCount('votes')
                           ->orderBy('votes_count', 'desc') // urut dari suara terbanyak
                           ->get();

        $totalSuara = $candidates->sum('votes_count');

        // Tambahkan persentase untuk setiap kandidat
        $candidates->each(function ($c) use ($totalSuara) {
            $c->persentase = $totalSuara > 0
                ? round(($c->votes_count / $totalSuara) * 100, 1)
                : 0;
        });

        $sudahVote = $user->sudahVote($poll->id);

        return view('polling.hasil', compact('poll', 'candidates', 'totalSuara', 'sudahVote'));
    }
}
