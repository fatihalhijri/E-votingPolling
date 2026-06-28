<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Candidate — merepresentasikan satu kandidat dalam sebuah polling.
 *
 * LOKASI FILE: app/Models/Candidate.php
 *
 * Relasi yang dimiliki model ini:
 * - belongsTo Poll: kandidat ini MILIK sebuah polling tertentu
 * - hasMany Vote: kandidat ini MENERIMA BANYAK suara
 */
class Candidate extends Model
{
    /**
     * $fillable: kolom-kolom yang boleh diisi via create() atau fill().
     *
     * Kita izinkan semua kolom tabel candidates untuk mass assignment,
     * termasuk 'foto' karena path foto akan diset dari controller setelah
     * file berhasil di-upload ke storage.
     */
    protected $fillable = [
        'poll_id',
        'nama_kandidat',
        'nomor_urut',
        'visi_misi',
        'foto',
    ];

    // =========================================================================
    // RELASI ELOQUENT
    // =========================================================================

    /**
     * Relasi: Candidate MILIK sebuah Poll (many-to-one).
     *
     * belongsTo artinya: tabel candidates punya kolom 'poll_id' yang
     * merujuk ke tabel polls. Ini sisi "anak".
     *
     * Penggunaan: $candidate->poll->judul (judul polling dari kandidat ini)
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Relasi: Candidate MENERIMA BANYAK Vote.
     *
     * Penggunaan: $candidate->votes->count() (hitung suara untuk kandidat ini)
     * Atau via withCount: Candidate::withCount('votes')->get()
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    // =========================================================================
    // HELPER METHOD
    // =========================================================================

    /**
     * Helper: dapatkan URL foto kandidat yang siap ditampilkan di view.
     *
     * Kenapa perlu method ini?
     * Karena kolom 'foto' hanya menyimpan PATH file (misal: "kandidat/foto.jpg"),
     * bukan URL lengkap. Kita perlu konversi ke URL dengan asset() atau Storage::url().
     * Kalau foto tidak ada, tampilkan foto placeholder default.
     *
     * Penggunaan di Blade: <img src="{{ $candidate->urlFoto() }}">
     */
    public function urlFoto(): string
    {
        if ($this->foto && \Storage::disk('public')->exists($this->foto)) {
            // File ada di storage/app/public/ → akses via symlink storage/
            return asset('storage/' . $this->foto);
        }

        // Foto belum diupload: tampilkan placeholder dengan initial nama kandidat
        // Kita pakai UI Avatars API (gratis, tidak perlu install library)
        $nama = urlencode($this->nama_kandidat);
        return "https://ui-avatars.com/api/?name={$nama}&background=0F2A4A&color=fff&size=200";
    }
}
