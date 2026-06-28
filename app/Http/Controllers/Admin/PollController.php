<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePollRequest;
use App\Http\Requests\UpdatePollRequest;
use App\Models\Poll;
use Illuminate\Http\Request;

/**
 * PollController — CRUD polling untuk admin.
 *
 * LOKASI FILE: app/Http/Controllers/Admin/PollController.php
 *
 * Ini adalah Resource Controller: satu class menangani semua operasi CRUD.
 * Laravel memberikan 7 method standar:
 * - index()   → tampilkan daftar semua polling
 * - create()  → tampilkan form buat polling baru
 * - store()   → simpan polling baru ke database
 * - show()    → tampilkan detail satu polling (tidak kita pakai)
 * - edit()    → tampilkan form edit polling
 * - update()  → simpan perubahan polling ke database
 * - destroy() → hapus polling
 */
class PollController extends Controller
{
    /**
     * Tampilkan daftar semua polling.
     *
     * KENAPA pakai withCount('votes')?
     * Ini adalah "Eager Loading" untuk agregat — Laravel akan otomatis
     * hitung jumlah vote per polling dalam SATU query tambahan, bukan
     * N query (satu per polling). Ini mencegah N+1 Query Problem.
     *
     * N+1 Problem: kalau ada 10 polling dan kita hitung vote di dalam loop,
     * itu 1 (ambil polls) + 10 (hitung vote per poll) = 11 query.
     * Dengan withCount, hanya 2 query total.
     */
    public function index()
    {
        // Ambil semua polling, hitung vote-nya, urutkan terbaru dulu
        $polls = Poll::withCount('votes')
                     ->with('creator')          // eager load nama admin pembuat
                     ->orderBy('created_at', 'desc')
                     ->paginate(10);             // paginasi 10 item per halaman

        return view('admin.polls.index', compact('polls'));
    }

    /**
     * Tampilkan form buat polling baru.
     */
    public function create()
    {
        return view('admin.polls.create');
    }

    /**
     * Simpan polling baru ke database.
     *
     * Laravel otomatis inject StorePollRequest yang sudah berisi validasi.
     * Kalau validasi gagal, Laravel OTOMATIS redirect kembali ke form
     * dengan pesan error — kita tidak perlu handle ini secara manual.
     */
    public function store(StorePollRequest $request)
    {
        // $request->validated() hanya mengambil field yang lolos validasi
        // (bukan semua input yang dikirim user) — lebih aman dari mass assignment
        $data = $request->validated();
        $data['created_by'] = auth()->id(); // catat admin yang membuat

        Poll::create($data);

        // redirect() ke daftar polling + flash message sukses
        // session('success') akan ditangkap oleh layout admin untuk ditampilkan sebagai toast
        return redirect()->route('admin.polls.index')
                         ->with('success', 'Polling berhasil dibuat!');
    }

    /**
     * Tampilkan form edit polling.
     *
     * Laravel otomatis cari Poll berdasarkan ID dari URL (Route Model Binding).
     * Jadi kita tidak perlu Poll::findOrFail($id) lagi — Laravel sudah handle itu.
     */
    public function edit(Poll $poll)
    {
        return view('admin.polls.edit', compact('poll'));
    }

    /**
     * Simpan perubahan polling ke database.
     */
    public function update(UpdatePollRequest $request, Poll $poll)
    {
        $poll->update($request->validated());

        return redirect()->route('admin.polls.index')
                         ->with('success', "Polling \"{$poll->judul}\" berhasil diperbarui!");
    }

    /**
     * Hapus polling dari database.
     *
     * PENTING: Di tabel polls, kita pakai onDelete('cascade') pada FK.
     * Artinya saat polling dihapus, semua kandidat dan suara yang terkait
     * OTOMATIS ikut terhapus oleh MySQL — kita tidak perlu hapus manual.
     */
    public function destroy(Poll $poll)
    {
        $judul = $poll->judul;
        $poll->delete();

        return redirect()->route('admin.polls.index')
                         ->with('success', "Polling \"{$judul}\" berhasil dihapus.");
    }

    /**
     * Ubah status polling (draft → aktif → selesai).
     *
     * Method ini TIDAK termasuk di 7 method standar resource controller.
     * Kita tambahkan sebagai method custom karena butuh route terpisah.
     *
     * KENAPA tidak biarkan admin ubah status lewat form edit biasa?
     * Karena perubahan status adalah aksi kritis yang harus disengaja.
     * Dengan tombol terpisah (dan konfirmasi JS), risiko salah klik lebih kecil.
     */
    public function updateStatus(Request $request, Poll $poll)
    {
        // Validasi status yang dikirim valid
        $request->validate([
            'status' => 'required|in:draft,aktif,selesai',
        ]);

        $poll->update(['status' => $request->status]);

        $labelStatus = match($request->status) {
            'draft'   => 'Draft',
            'aktif'   => 'Aktif',
            'selesai' => 'Selesai',
        };

        return redirect()->route('admin.polls.index')
                         ->with('success', "Status polling \"{$poll->judul}\" diubah ke: {$labelStatus}.");
    }
}
