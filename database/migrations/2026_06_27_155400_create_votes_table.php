<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat tabel votes (suara masuk).
 *
 * INI TABEL PALING KRITIS di sistem ini!
 * Kenapa? Karena di sinilah integritas "1 mahasiswa = 1 suara" dijaga.
 *
 * Dua lapis pengaman:
 * 1. Validasi di PHP (Controller): cek sudah vote atau belum sebelum simpan
 * 2. UNIQUE CONSTRAINT di level database (baris $table->unique di bawah)
 *
 * Kenapa perlu dua lapis? Ini disebut "Defense in Depth" (pertahanan berlapis).
 * Bayangkan ada bug di PHP, atau ada developer lain yang bypass controller
 * dan langsung insert ke database — constraint di database tetap akan menolak.
 * Database adalah "last line of defense" yang tidak bisa ditembus dari kode PHP.
 *
 * LOKASI FILE: database/migrations/2026_06_27_..._create_votes_table.php
 */
return new class extends Migration
{
    /**
     * Jalankan migration — buat tabel votes.
     */
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();

            // poll_id: polling mana yang sedang di-vote.
            // onDelete cascade: kalau polling dihapus, data suaranya juga terhapus.
            $table->foreignId('poll_id')->constrained('polls')->onDelete('cascade');

            // candidate_id: kandidat mana yang dipilih.
            // onDelete cascade: kalau kandidat dihapus, suara untuknya juga terhapus.
            $table->foreignId('candidate_id')->constrained('candidates')->onDelete('cascade');

            // user_id: siapa yang melakukan vote.
            // onDelete cascade: kalau mahasiswa dihapus, data suaranya ikut terhapus.
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // voted_at: waktu tepat saat vote dilakukan.
            // Kenapa bukan hanya pakai created_at?
            // Karena voted_at lebih eksplisit dan semantik — jelas artinya "waktu vote".
            // Ini penting untuk audit log Fase 6 nanti.
            $table->timestamp('voted_at');

            $table->timestamps();

            // =====================================================================
            // UNIQUE CONSTRAINT GABUNGAN (poll_id + user_id)
            //
            // Inilah pengaman di level DATABASE untuk mencegah double voting.
            // Artinya: kombinasi poll_id dan user_id HARUS unik.
            // Satu mahasiswa (user_id) hanya boleh muncul SEKALI untuk setiap
            // polling (poll_id). Kalau ada percobaan insert duplikat, MySQL akan
            // langsung throw error "Duplicate entry" — SEBELUM data tersimpan.
            //
            // Kenapa TIDAK cukup hanya validasi di PHP?
            // Karena:
            // - Race condition: 2 request bersamaan bisa lolos validasi PHP sebelum
            //   yang pertama selesai tersimpan (terutama saat traffic tinggi)
            // - Bypass: orang bisa akses database langsung tanpa lewat aplikasi PHP
            // - Bug: ada developer yang lupa tambahkan validasi di controller baru
            // =====================================================================
            $table->unique(['poll_id', 'user_id'], 'unique_vote_per_user_per_poll');
        });
    }

    /**
     * Rollback: hapus tabel votes.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
