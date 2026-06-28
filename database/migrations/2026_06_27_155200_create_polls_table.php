<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat tabel polls (polling/pemilihan).
 *
 * KENAPA file ini penting?
 * Tabel polls adalah "pusat" dari sistem ini. Setiap event voting
 * (misal "Pemilihan Ketua BEM 2026") direpresentasikan sebagai satu baris
 * di tabel ini. Semua kandidat dan suara mengacu ke tabel ini.
 *
 * LOKASI FILE: database/migrations/2026_06_27_..._create_polls_table.php
 */
return new class extends Migration
{
    /**
     * Jalankan migration — buat tabel polls.
     */
    public function up(): void
    {
        Schema::create('polls', function (Blueprint $table) {
            // id: primary key auto-increment, standar Laravel.
            $table->id();

            // judul: nama polling, misal "Pemilihan Ketua BEM 2026".
            // Tidak boleh kosong karena ini identitas utama polling.
            $table->string('judul');

            // deskripsi: penjelasan tambahan tentang polling.
            // Nullable karena judul saja sudah cukup untuk polling sederhana.
            $table->text('deskripsi')->nullable();

            // status: menentukan apakah polling bisa diakses mahasiswa.
            // - 'draft'   : polling belum dibuka (hanya admin yang bisa lihat)
            // - 'aktif'   : polling terbuka, mahasiswa bisa vote
            // - 'selesai' : polling ditutup, tidak bisa vote lagi
            // Menggunakan enum agar nilai status selalu terkontrol (tidak bisa sembarangan diisi).
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');

            // mulai_pada & selesai_pada: rentang waktu polling berlangsung.
            // Kenapa pakai datetime (bukan date)?
            // Karena polling bisa dibuka jam tertentu, misal "buka 08:00, tutup 17:00".
            $table->datetime('mulai_pada');
            $table->datetime('selesai_pada');

            // created_by: foreign key ke users.id — mencatat admin siapa yang membuat polling.
            // Kenapa perlu? Untuk audit: kalau ada masalah, kita tahu siapa yang bertanggung jawab.
            // constrained() otomatis buat foreign key ke tabel users kolom id.
            // onDelete('cascade') berarti kalau admin dihapus, polling-nya ikut terhapus.
            // (Pertimbangkan bisnis: untuk production, mungkin lebih baik restrict/set null)
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            // timestamps: otomatis buat kolom created_at dan updated_at.
            // Laravel mengisi ini otomatis saat create/update.
            $table->timestamps();
        });
    }

    /**
     * Rollback: hapus tabel polls.
     */
    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};
