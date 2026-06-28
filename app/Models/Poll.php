<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Model Poll — merepresentasikan satu event polling/pemilihan.
 *
 * LOKASI FILE: app/Models/Poll.php
 *
 * Relasi yang dimiliki model ini:
 * - belongsTo User (creator): poll dibuat OLEH seorang admin
 * - hasMany Candidate: poll MEMILIKI BANYAK kandidat
 * - hasMany Vote: poll MEMILIKI BANYAK suara yang masuk
 */
class Poll extends Model
{
    /**
     * $fillable: daftar kolom yang boleh diisi secara massal (mass assignment).
     *
     * KENAPA perlu $fillable?
     * Laravel secara default melindungi dari "Mass Assignment Vulnerability".
     * Bayangkan seseorang mengirim form dengan field tambahan seperti 'role=admin'
     * — tanpa $fillable, field itu bisa langsung mengubah database.
     * Dengan $fillable, hanya kolom yang kita izinkan yang bisa diisi via create()/fill().
     */
    protected $fillable = [
        'judul',
        'deskripsi',
        'status',
        'mulai_pada',
        'selesai_pada',
        'created_by',
    ];

    /**
     * $casts: memberi tahu Laravel cara mengonversi tipe data kolom.
     *
     * 'mulai_pada' dan 'selesai_pada' di-cast ke Carbon (objek datetime PHP).
     * Manfaatnya: kita bisa langsung pakai $poll->mulai_pada->format('d M Y')
     * atau $poll->selesai_pada->diffForHumans() tanpa konversi manual.
     */
    protected $casts = [
        'mulai_pada'  => 'datetime',
        'selesai_pada' => 'datetime',
    ];

    // =========================================================================
    // RELASI ELOQUENT
    // =========================================================================

    /**
     * Relasi: Poll DIMILIKI OLEH seorang User (admin yang membuat).
     *
     * belongsTo artinya: tabel polls punya kolom 'created_by' yang
     * merujuk ke tabel users. Ini adalah sisi "anak" dari relasi.
     *
     * Penggunaan: $poll->creator->name (nama admin pembuat)
     */
    public function creator(): BelongsTo
    {
        // Parameter kedua 'created_by' adalah nama foreign key di tabel polls
        // (bukan default 'user_id'), jadi kita harus sebutkan eksplisit.
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi: Poll MEMILIKI BANYAK Candidate.
     *
     * hasMany artinya: banyak baris di tabel candidates punya poll_id yang
     * sama dengan id poll ini. Ini adalah sisi "induk" dari relasi.
     *
     * Penggunaan: $poll->candidates (collection semua kandidat di poll ini)
     * Atau:       $poll->candidates()->orderBy('nomor_urut')->get()
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class)->orderBy('nomor_urut');
    }

    /**
     * Relasi: Poll MEMILIKI BANYAK Vote.
     *
     * Penggunaan: $poll->votes->count() (hitung total suara masuk)
     * Atau via withCount: Poll::withCount('votes')->get()
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    // =========================================================================
    // ACCESSOR / HELPER METHOD
    // =========================================================================

    /**
     * Accessor: apakah polling ini sedang aktif dan bisa divote sekarang?
     *
     * KENAPA perlu ini?
     * Kita butuh pengecekan gabungan: status='aktif' DAN waktu sekarang ada
     * di antara mulai_pada dan selesai_pada. Kalau logika ini tersebar di
     * controller dan view, mudah inkonsisten. Lebih baik dipusatkan di model.
     *
     * Cara pakai: $poll->sedang_aktif → true/false (otomatis dipanggil seperti property)
     *
     * CATATAN: Accessor di Laravel 9+ menggunakan Attribute casting.
     * Tapi untuk kesederhanaan (level mahasiswa), kita pakai method biasa.
     */
    public function sedangAktif(): bool
    {
        $sekarang = Carbon::now();

        return $this->status === 'aktif'
            && $sekarang->gte($this->mulai_pada)   // sekarang >= waktu mulai
            && $sekarang->lte($this->selesai_pada); // sekarang <= waktu selesai
    }

    /**
     * Helper: label status untuk ditampilkan di badge UI.
     *
     * Mengembalikan array ['label' => '...', 'class' => '...'] untuk badge Bootstrap.
     *
     * Penggunaan di Blade: @php $badge = $poll->badgeStatus() @endphp
     */
    public function badgeStatus(): array
    {
        return match($this->status) {
            'draft'   => ['label' => 'Draft',             'class' => 'badge-draft'],
            'aktif'   => ['label' => 'Sedang Berlangsung','class' => 'badge-aktif'],
            'selesai' => ['label' => 'Selesai',           'class' => 'badge-selesai'],
            default   => ['label' => 'Unknown',           'class' => 'badge-draft'],
        };
    }

    /**
     * Helper: sisa waktu polling dalam format human-readable.
     *
     * Misal: "Berakhir dalam 2 hari" atau "Berakhir dalam 3 jam".
     * Dipakai di halaman daftar polling mahasiswa (Fase 4).
     */
    public function sisaWaktu(): string
    {
        if (!$this->sedangAktif()) {
            return 'Polling tidak aktif';
        }

        // Set locale Indonesia agar output: "dalam 6 hari" bukan "6 days from now"
        return 'Berakhir ' . $this->selesai_pada->locale('id')->diffForHumans();
    }
}
