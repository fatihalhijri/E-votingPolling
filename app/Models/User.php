<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model User — merepresentasikan pengguna sistem (admin atau mahasiswa).
 *
 * LOKASI FILE: app/Models/User.php
 *
 * Kolom tambahan (dari migration add_fields_to_users_table):
 * - nim   : Nomor Induk Mahasiswa, unique
 * - role  : enum 'admin' | 'mahasiswa'
 * - prodi : Program Studi (opsional)
 *
 * Relasi yang dimiliki model ini:
 * - hasMany Poll (sebagai creator): user admin MEMBUAT banyak polling
 * - hasMany Vote: user mahasiswa MEMBERIKAN banyak suara
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * $fillable: kolom yang boleh diisi via create() atau fill().
     *
     * Kita tambahkan 'nim', 'role', 'prodi' ke daftar ini karena kolom
     * tersebut baru kita tambahkan via migration dan perlu bisa diisi
     * saat register atau saat admin membuat user baru.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nim',
        'role',
        'prodi',
    ];

    /**
     * $hidden: kolom yang DISEMBUNYIKAN saat model dikonversi ke JSON/array.
     *
     * 'password' dan 'remember_token' tidak boleh terekspos ke response API
     * atau view — ini praktik keamanan standar.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * $casts: cara Laravel mengonversi tipe data kolom.
     *
     * 'password' => 'hashed': otomatis hash password saat diisi via $fillable.
     * Artinya $user->password = 'plaintext' otomatis jadi bcrypt hash.
     * TAPI: saat menggunakan Hash::make() manual di seeder, tidak perlu lagi.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // =========================================================================
    // RELASI ELOQUENT
    // =========================================================================

    /**
     * Relasi: User (admin) MEMBUAT BANYAK Poll.
     *
     * hasMany artinya: banyak baris di tabel polls punya created_by = user ini.
     * Parameter kedua 'created_by' adalah nama foreign key yang bukan default.
     *
     * Penggunaan: $user->pollsDibuat (collection semua polling yang dibuat admin ini)
     */
    public function pollsDibuat(): HasMany
    {
        return $this->hasMany(Poll::class, 'created_by');
    }

    /**
     * Relasi: User (mahasiswa) MEMBERIKAN BANYAK Vote.
     *
     * Penggunaan: $user->votes (collection semua suara yang pernah diberikan user ini)
     * Lebih sering dipakai untuk cek: $user->votes()->where('poll_id', $poll->id)->exists()
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    // =========================================================================
    // HELPER METHOD
    // =========================================================================

    /**
     * Helper: cek apakah user ini adalah admin.
     *
     * Dipakai di middleware, Blade (@if Auth::user()->isAdmin()), dll.
     * Lebih readable daripada: $user->role === 'admin'
     *
     * Penggunaan: $user->isAdmin() → true/false
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Helper: cek apakah user sudah vote di polling tertentu.
     *
     * Dipakai di controller (Fase 4) untuk mencegah double voting,
     * dan di view untuk disable tombol "Pilih Kandidat Ini".
     *
     * Penggunaan: $user->sudahVote($poll->id) → true/false
     */
    public function sudahVote(int $pollId): bool
    {
        return $this->votes()->where('poll_id', $pollId)->exists();
    }
}
