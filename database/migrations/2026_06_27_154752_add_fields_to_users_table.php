<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Menambah kolom tambahan ke tabel users.
 *
 * KENAPA file ini terpisah dari 0001_01_01_000000_create_users_table.php?
 * Karena tabel users sudah dibuat oleh Breeze. Kita TIDAK mengubah file
 * migration aslinya (prinsip: jangan ubah migration yang sudah pernah dijalankan),
 * melainkan membuat migration baru yang ALTER TABLE users.
 *
 * LOKASI FILE: database/migrations/2026_06_27_..._add_fields_to_users_table.php
 */
return new class extends Migration
{
    /**
     * Jalankan migration — tambah kolom ke tabel users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // nim: Nomor Induk Mahasiswa — identifier unik setiap mahasiswa.
            // Dibuat unique agar tidak ada dua user dengan NIM sama.
            // Nullable dulu agar user lama (admin) tidak wajib isi NIM.
            $table->string('nim')->nullable()->unique()->after('name');

            // role: membedakan hak akses admin vs mahasiswa.
            // Enum dipilih karena nilainya terbatas dan sudah diketahui sejak awal.
            // Default 'mahasiswa' karena sebagian besar pengguna adalah mahasiswa.
            $table->enum('role', ['admin', 'mahasiswa'])->default('mahasiswa')->after('nim');

            // prodi: Program Studi mahasiswa.
            // Nullable karena user admin tidak perlu isi prodi.
            $table->string('prodi')->nullable()->after('role');
        });
    }

    /**
     * Rollback migration — hapus kolom yang baru ditambahkan.
     * Dijalankan saat `php artisan migrate:rollback`.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nim', 'role', 'prodi']);
        });
    }
};
