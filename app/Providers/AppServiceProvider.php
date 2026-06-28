<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * boot() dipanggil setelah semua service provider terdaftar.
     * Di sinilah konfigurasi global app: locale, pagination, rate limiter.
     */
    public function boot(): void
    {
        // Set locale Carbon ke Bahasa Indonesia secara global.
        // Efek: $tanggal->diffForHumans() → "15 menit yang lalu" bukan "15 minutes ago"
        Carbon::setLocale('id');

        // Gunakan Bootstrap 5 untuk tampilan pagination Laravel
        // (paginate()->links() akan render dengan class Bootstrap 5)
        Paginator::useBootstrapFive();

        // =====================================================================
        // RATE LIMITER — Batas Request untuk Keamanan
        //
        // Kenapa perlu rate limiter?
        // Tanpa rate limiter, penyerang bisa mengirim ribuan request vote
        // dalam hitungan detik (walaupun sudah ada unique constraint di DB).
        // Rate limiter memblokir di lapisan paling awal (sebelum sampai controller).
        // =====================================================================
        RateLimiter::for('vote', function (Request $request) {
            // Maks 5 percobaan vote per 2 menit per user
            // Key: kombinasi user_id + poll_id → setiap user, setiap polling
            // punya counter sendiri (tidak "jatah bersama").
            return Limit::perMinutes(2, 5)
                        ->by($request->user()?->id . '|' . $request->route('poll'))
                        ->response(function () {
                            // Respons custom 429 jika limit terlampaui
                            return back()->with('error',
                                'Terlalu banyak percobaan. Silakan tunggu 2 menit sebelum mencoba lagi.'
                            );
                        });
        });
    }
}
