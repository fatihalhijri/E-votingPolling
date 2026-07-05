@extends('layouts.public')

@section('title', 'Beranda')

@section('content')

    {{-- ===================================================================
         HALAMAN HOME PUBLIK — E-Vote Kampus
         Dapat diakses TANPA login oleh siapapun.

         Alur guest (belum login):
         1. Buka website → langsung lihat halaman ini + daftar polling
         2. Klik "Masuk untuk Vote" → Laravel redirect ke /login
            (URL polling disimpan otomatis di session sebagai "intended URL")
         3. Setelah login sukses → otomatis kembali ke halaman poll tadi

         Alur user (sudah login):
         - Tombol "Vote Sekarang" atau "Suara Tercatat" tampil sesuai status
    =================================================================== --}}

    {{-- ===== HERO SECTION ===== --}}
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center gy-4">
                <div class="col-lg-7">
                    <div class="hero-badge mb-3">
                        <span class="pulse-dot"></span>
                        Sistem Voting Digital Kampus
                    </div>
                    <h1 class="hero-title mb-3">
                        Suarakan Pilihanmu,<br>
                        Wujudkan <span>Pemimpin</span> Terbaik
                    </h1>
                    <p style="color:rgba(255,255,255,0.7);font-size:0.97rem;max-width:470px;line-height:1.7;">
                        Ikuti polling kampus secara aman, mudah, dan transparan.
                        Hasil voting tersedia real-time setelah sesi ditutup.
                    </p>

                    @guest
                        <div class="d-flex gap-3 mt-4 flex-wrap">
                            <a href="{{ route('login') }}"
                               class="btn btn-lg fw-bold"
                               style="background:var(--evote-gold);color:var(--evote-navy);border-radius:50px;padding:0.65rem 2rem;">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk & Vote
                            </a>
                            <a href="{{ route('register') }}"
                               class="btn btn-lg btn-outline-light"
                               style="border-radius:50px;padding:0.65rem 2rem;">
                                <i class="bi bi-person-plus me-2"></i>Buat Akun Gratis
                            </a>
                        </div>
                        <p style="color:rgba(255,255,255,0.4);font-size:0.75rem;margin-top:0.75rem;">
                            <i class="bi bi-shield-lock me-1"></i>Gratis · Aman · Rahasia
                        </p>
                    @endguest

                    @auth
                        <div class="d-flex gap-3 mt-4">
                            <a href="{{ route('dashboard') }}"
                               class="btn btn-lg fw-bold"
                               style="background:var(--evote-gold);color:var(--evote-navy);border-radius:50px;padding:0.65rem 2rem;">
                                <i class="bi bi-speedometer2 me-2"></i>Ke Dashboard
                            </a>
                        </div>
                    @endauth
                </div>

                <div class="col-lg-5">
                    <div class="d-flex flex-wrap gap-3 justify-content-lg-end">
                        <div class="stat-chip"><i class="bi bi-bar-chart-fill" style="color:var(--evote-gold);"></i> Hasil Real-time</div>
                        <div class="stat-chip"><i class="bi bi-shield-check" style="color:#4ade80;"></i> Suara Rahasia</div>
                        <div class="stat-chip"><i class="bi bi-phone" style="color:var(--evote-gold);"></i> Mobile Friendly</div>
                        <div class="stat-chip"><i class="bi bi-people" style="color:#93c5fd;"></i> Multi Polling</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== SECTION DAFTAR POLLING ===== --}}
    <section style="padding:3rem 0;flex:1;">
        <div class="container">

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="section-title mb-0">Polling Tersedia</h2>
                    <p class="text-muted mb-0" style="font-size:0.83rem;">
                        {{ $polls->where('status','aktif')->count() }} polling aktif ·
                        {{ $polls->where('status','selesai')->count() }} selesai
                    </p>
                </div>
                @guest
                    <a href="{{ route('login') }}"
                       class="btn btn-sm fw-semibold"
                       style="background:var(--evote-navy);color:#fff;border-radius:50px;">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Masuk untuk Vote
                    </a>
                @endguest
            </div>

            {{-- EMPTY STATE --}}
            @if($polls->isEmpty())
                <div class="text-center py-5 evote-card p-5">
                    <div style="font-size:4rem;opacity:0.2;">🗳️</div>
                    <h6 class="mt-3 fw-bold" style="color:var(--evote-navy);">Belum ada polling tersedia</h6>
                    <p class="text-muted" style="font-size:0.88rem;">Pantau terus — polling baru akan muncul di sini.</p>
                </div>

            @else
                {{-- GRID POLLING CARDS --}}
                <div class="row g-4">
                    @foreach($polls as $poll)
                    @php
                        $selesai = $poll->status === 'selesai';
                        $sudah   = in_array($poll->id, $sudahVotePollIds);
                        $urgent  = !$selesai && $poll->selesai_pada->diffInHours(now()) <= 24;
                        $borderColor = $selesai ? '#64748B' : ($urgent ? 'var(--evote-red)' : 'var(--evote-navy)');
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="evote-card h-100" style="border-top:3px solid {{ $borderColor }};">
                            <div class="p-4">

                                {{-- BARIS BADGE: status (kiri) + sudah vote (kanan) --}}
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    {{-- Kiri: ikon + badge status --}}
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                             style="width:36px;height:36px;background:rgba(15,42,74,0.08);font-size:1.1rem;">
                                            🗳️
                                        </div>
                                        @if($selesai)
                                            <span class="badge" style="background:#64748B;font-size:0.7rem;">
                                                <i class="bi bi-flag-fill me-1"></i>Selesai
                                            </span>
                                        @elseif($urgent)
                                            <span class="badge" style="background:var(--evote-red);font-size:0.7rem;">
                                                <i class="bi bi-alarm me-1"></i>Hampir Berakhir
                                            </span>
                                        @else
                                            <span class="badge" style="background:var(--evote-green);font-size:0.7rem;">
                                                <span class="pulse-dot" style="width:6px;height:6px;"></span>Sedang Berlangsung
                                            </span>
                                        @endif
                                    </div>
                                    {{-- Kanan: badge "Sudah Vote" --}}
                                    @if($sudah)
                                        <span class="badge rounded-pill flex-shrink-0"
                                              style="background:var(--evote-green);font-size:0.7rem;padding:4px 10px;">
                                            <i class="bi bi-check2-circle me-1"></i>Sudah Vote
                                        </span>
                                    @endif
                                </div>

                                {{-- Judul --}}
                                <h5 class="fw-bold mb-2" style="color:var(--evote-navy);font-size:1rem;line-height:1.4;">
                                    {{ $poll->judul }}
                                </h5>

                                {{-- Deskripsi --}}
                                @if($poll->deskripsi)
                                    <p class="text-muted mb-3" style="font-size:0.82rem;line-height:1.5;">
                                        {{ Str::limit($poll->deskripsi, 100) }}
                                    </p>
                                @endif

                                {{-- Meta info --}}
                                <div class="d-flex flex-wrap gap-3 mb-3" style="font-size:0.79rem;color:#64748B;">
                                    <span><i class="bi bi-people me-1"></i>{{ $poll->candidates_count }} kandidat</span>
                                    <span><i class="bi bi-hand-index-thumb me-1"></i>{{ number_format($poll->votes_count) }} suara</span>
                                    @if($selesai)
                                        <span><i class="bi bi-calendar-check me-1"></i>Selesai {{ $poll->selesai_pada->locale('id')->diffForHumans() }}</span>
                                    @else
                                        <span><i class="bi bi-clock me-1"></i>{{ $poll->sisaWaktu() }}</span>
                                    @endif
                                </div>

                                {{-- TOMBOL AKSI (logika 4 kondisi) --}}
                                @if($selesai)
                                    {{-- Polling selesai: siapapun bisa lihat hasil --}}
                                    <a href="{{ route('polling.hasil', $poll) }}"
                                       class="btn btn-evote-primary btn-sm w-100">
                                        <i class="bi bi-bar-chart me-1"></i>Lihat Hasil Akhir
                                    </a>

                                @elseif(auth()->check() && $sudah)
                                    {{-- Login + sudah vote --}}
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('polling.show', $poll) }}"
                                           class="btn btn-sm flex-fill fw-semibold"
                                           style="border:2px solid var(--evote-green);color:var(--evote-green);">
                                            <i class="bi bi-check-circle me-1"></i>Suara Tercatat
                                        </a>
                                        <a href="{{ route('polling.hasil', $poll) }}"
                                           class="btn btn-sm btn-outline-secondary" title="Lihat Hasil">
                                            <i class="bi bi-bar-chart"></i>
                                        </a>
                                    </div>

                                @elseif(auth()->check())
                                    {{-- Login + belum vote --}}
                                    <a href="{{ route('polling.show', $poll) }}"
                                       class="btn btn-evote-primary btn-sm w-100">
                                        <i class="bi bi-hand-index-thumb me-2"></i>Vote Sekarang
                                    </a>

                                @else
                                    {{--
                                        GUEST (belum login):
                                        Link ke route('polling.show') yang dilindungi middleware 'auth'.
                                        Laravel menyimpan URL ini di session → redirect ke /login.
                                        Setelah login → otomatis balik ke polling ini. (intended redirect)
                                    --}}
                                    <a href="{{ route('polling.show', $poll) }}"
                                       class="btn-guest-vote">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk untuk Vote
                                    </a>
                                @endif

                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Banner CTA daftar akun untuk guest --}}
                @guest
                    <div class="mt-5 p-4 text-center"
                         style="background:linear-gradient(135deg,var(--evote-navy),#1a3a5c);border-radius:16px;color:#fff;">
                        <div style="font-size:2.2rem;">🔐</div>
                        <h5 class="fw-bold mt-2 mb-1" style="font-family:'Poppins',sans-serif;">Belum punya akun?</h5>
                        <p style="color:rgba(255,255,255,0.65);font-size:0.88rem;margin-bottom:1.25rem;">
                            Daftar gratis sekarang dan ikuti polling kampus Anda!
                        </p>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="{{ route('register') }}"
                               class="btn btn-sm fw-bold"
                               style="background:var(--evote-gold);color:var(--evote-navy);border-radius:50px;padding:0.5rem 1.5rem;">
                                <i class="bi bi-person-plus me-1"></i>Daftar Sekarang
                            </a>
                            <a href="{{ route('login') }}"
                               class="btn btn-sm btn-outline-light"
                               style="border-radius:50px;padding:0.5rem 1.5rem;">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Sudah punya akun
                            </a>
                        </div>
                    </div>
                @endguest
            @endif

        </div>
    </section>

@endsection
