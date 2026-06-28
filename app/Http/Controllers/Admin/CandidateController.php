<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCandidateRequest;
use App\Models\Candidate;
use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * CandidateController — CRUD kandidat untuk admin.
 *
 * LOKASI FILE: app/Http/Controllers/Admin/CandidateController.php
 *
 * Ini adalah Nested Resource Controller: kandidat selalu dalam konteks
 * sebuah polling. URL-nya: /admin/polls/{poll}/candidates/{candidate}
 *
 * CARA KERJA UPLOAD FOTO LARAVEL:
 * 1. File dikirim via form (enctype="multipart/form-data")
 * 2. Controller terima via $request->file('foto')
 * 3. Simpan ke storage/app/public/kandidat/ via Storage::disk('public')->put()
 * 4. Database menyimpan PATH relatifnya (misal: "kandidat/abc123.jpg")
 * 5. Di view, tampilkan via asset('storage/' . $candidate->foto)
 *
 * KENAPA simpan di storage/app/public/?
 * Karena folder 'storage/app/public' adalah area yang "aman" — tidak bisa
 * diakses langsung dari browser. Akses hanya lewat symlink 'public/storage'
 * yang dibuat oleh: php artisan storage:link
 */
class CandidateController extends Controller
{
    /**
     * Tampilkan daftar kandidat untuk satu polling tertentu.
     *
     * Laravel otomatis inject $poll via Route Model Binding dari URL segment {poll}.
     */
    public function index(Poll $poll)
    {
        // Ambil kandidat yang sudah diurutkan nomor urut (ascending)
        // withCount('votes') untuk tampilkan jumlah suara tiap kandidat di tabel
        $candidates = $poll->candidates()->withCount('votes')->get();

        return view('admin.candidates.index', compact('poll', 'candidates'));
    }

    /**
     * Tampilkan form tambah kandidat baru.
     */
    public function create(Poll $poll)
    {
        return view('admin.candidates.create', compact('poll'));
    }

    /**
     * Simpan kandidat baru ke database (termasuk upload foto jika ada).
     */
    public function store(StoreCandidateRequest $request, Poll $poll)
    {
        $data = $request->validated();
        $data['poll_id'] = $poll->id;

        // ================================================================
        // PROSES UPLOAD FOTO (jika ada file yang dikirim)
        // ================================================================
        if ($request->hasFile('foto')) {
            // store() otomatis generate nama file unik (UUID) untuk mencegah
            // nama file tabrakan. Hasilnya: "kandidat/randomstring.jpg"
            // 'public' = disk yang kita pakai (storage/app/public)
            $data['foto'] = $request->file('foto')->store('kandidat', 'public');
        }

        $candidate = Candidate::create($data);

        return redirect()->route('admin.polls.candidates.index', $poll)
                         ->with('success', "Kandidat \"{$candidate->nama_kandidat}\" berhasil ditambahkan!");
    }

    /**
     * Tampilkan form edit kandidat.
     */
    public function edit(Poll $poll, Candidate $candidate)
    {
        return view('admin.candidates.edit', compact('poll', 'candidate'));
    }

    /**
     * Simpan perubahan kandidat (termasuk ganti foto jika ada upload baru).
     */
    public function update(StoreCandidateRequest $request, Poll $poll, Candidate $candidate)
    {
        $data = $request->validated();

        // Proses upload foto baru jika admin memilih file baru
        if ($request->hasFile('foto')) {
            // HAPUS FOTO LAMA dulu agar tidak memenuhi storage
            // Ini penting untuk menjaga disk tidak penuh!
            if ($candidate->foto) {
                Storage::disk('public')->delete($candidate->foto);
            }

            // Simpan foto baru, dapatkan path-nya
            $data['foto'] = $request->file('foto')->store('kandidat', 'public');
        }

        $candidate->update($data);

        return redirect()->route('admin.polls.candidates.index', $poll)
                         ->with('success', "Data kandidat \"{$candidate->nama_kandidat}\" berhasil diperbarui!");
    }

    /**
     * Hapus kandidat (dan foto-nya dari storage jika ada).
     */
    public function destroy(Poll $poll, Candidate $candidate)
    {
        $nama = $candidate->nama_kandidat;

        // Hapus file foto dari disk storage terlebih dahulu
        // Kalau tidak, foto "orphan" akan tetap ada di server buang-buang space
        if ($candidate->foto) {
            Storage::disk('public')->delete($candidate->foto);
        }

        $candidate->delete();

        return redirect()->route('admin.polls.candidates.index', $poll)
                         ->with('success', "Kandidat \"{$nama}\" berhasil dihapus.");
    }
}
