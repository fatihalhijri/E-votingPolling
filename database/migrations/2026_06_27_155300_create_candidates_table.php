<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat tabel candidates (kandidat per polling).
 *
 * KENAPA terpisah dari tabel polls?
 * Karena satu polling bisa punya banyak kandidat (relasi one-to-many).
 * Kalau digabung dalam satu tabel, struktur data jadi tidak normal
 * (melanggar Normal Form database — pelajaran dasar basis data).
 *
 * LOKASI FILE: database/migrations/2026_06_27_..._create_candidates_table.php
 */
return new class extends Migration
{
    /**
     * Jalankan migration — buat tabel candidates.
     */
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();

            // poll_id: foreign key ke polls.id.
            // Ini yang mengikat kandidat ke polling tertentu.
            // onDelete('cascade'): kalau polling dihapus, semua kandidatnya ikut terhapus
            // (logis: kalau pemilihannya dihapus, daftar calonnya juga tidak relevan lagi).
            $table->foreignId('poll_id')->constrained('polls')->onDelete('cascade');

            // nama_kandidat: nama lengkap kandidat yang akan ditampilkan di kartu kandidat.
            $table->string('nama_kandidat');

            // nomor_urut: nomor urut kandidat (1, 2, 3, ...).
            // Kenapa integer, bukan string? Karena kita bisa pakai untuk sorting
            // dan menampilkan nomor urut seperti di surat suara pemilu asli.
            $table->integer('nomor_urut');

            // visi_misi: teks visi dan misi kandidat.
            // Text (bukan string) karena isinya bisa sangat panjang.
            // Nullable: admin bisa tambahkan belakangan setelah daftar kandidat dibuat.
            $table->text('visi_misi')->nullable();

            // foto: path file foto kandidat yang disimpan di storage Laravel.
            // Kita simpan PATH-nya saja (bukan file binarynya) — ini praktik standar.
            // Nullable karena kandidat bisa saja belum ada fotonya.
            $table->string('foto')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Rollback: hapus tabel candidates.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
