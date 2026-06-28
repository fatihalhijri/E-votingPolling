<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Vote — merepresentasikan satu suara yang diberikan mahasiswa.
 *
 * LOKASI FILE: app/Models/Vote.php
 *
 * Model ini relatif sederhana — tugasnya hanya mencatat SIAPA memilih SIAPA
 * dalam polling APA. Tapi di balik kesederhanaan ini ada unique constraint
 * di database yang menjaga integritas "1 mahasiswa = 1 suara".
 *
 * Relasi yang dimiliki model ini:
 * - belongsTo Poll: suara ini ada di polling tertentu
 * - belongsTo Candidate: suara ini ditujukan ke kandidat tertentu
 * - belongsTo User: suara ini diberikan oleh mahasiswa tertentu
 */
class Vote extends Model
{
    /**
     * $fillable: kolom yang boleh diisi via create() atau fill().
     *
     * 'voted_at' kita include agar bisa diset eksplisit saat menyimpan vote
     * (bukan hanya mengandalkan created_at, karena voted_at lebih semantik).
     */
    protected $fillable = [
        'poll_id',
        'candidate_id',
        'user_id',
        'voted_at',
    ];

    /**
     * $casts: cast 'voted_at' ke Carbon object agar mudah diformat di view.
     *
     * Penggunaan: $vote->voted_at->format('d M Y, H:i') → "27 Jun 2026, 14:30"
     */
    protected $casts = [
        'voted_at' => 'datetime',
    ];

    // =========================================================================
    // RELASI ELOQUENT
    // =========================================================================

    /**
     * Relasi: Vote ini ADA DI sebuah Poll.
     *
     * Penggunaan: $vote->poll->judul
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Relasi: Vote ini DITUJUKAN KE sebuah Candidate.
     *
     * Penggunaan: $vote->candidate->nama_kandidat
     *
     * CATATAN PENTING tentang kerahasiaan:
     * Di halaman Audit Log (Fase 6), kita sengaja TIDAK menampilkan relasi ini
     * ke publik — hanya menampilkan SIAPA yang sudah vote (poll + user),
     * BUKAN kandidat mana yang dipilih. Ini adalah prinsip "secret ballot".
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * Relasi: Vote ini DIBERIKAN OLEH seorang User (mahasiswa).
     *
     * Penggunaan: $vote->user->name, $vote->user->nim
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
