<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0 fw-bold" style="color:var(--evote-navy);">
                    Selamat datang, {{ Auth::user()->name }}! 👋
                </h5>
                <small class="text-muted">Berikut adalah polling yang tersedia untuk Anda.</small>
            </div>
            <span class="badge px-3 py-2"
                  style="background:var(--evote-navy);font-size:0.78rem;letter-spacing:0.5px;">
                🎓 Mahasiswa
            </span>
        </div>
    </x-slot>

    {{-- ===================================================================
         DAFTAR POLLING AKTIF DI DASHBOARD
         Menampilkan maks 3 polling aktif. Link "Lihat Semua" ke /polling.
         (masterpromptvoting.md: Fase 4 — mahasiswa bisa lihat dan vote polling)
    =================================================================== --}}

    @if($pollsAktif->isEmpty())
        {{-- ===== EMPTY STATE ===== --}}
        <div class="text-center py-5">
            <div style="font-size:5rem;opacity:0.2;">🗳️</div>
            <h6 class="mt-3 fw-bold" style="color:var(--evote-navy);">Belum ada polling aktif saat ini</h6>
            <p class="text-muted" style="font-size:0.88rem;max-width:360px;margin:0 auto;">
                Cek lagi nanti ya! Polling baru akan muncul di sini ketika Admin membuka sesi voting.
            </p>
            <hr class="my-4" style="max-width:300px;margin:1rem auto;">
            <p class="text-muted" style="font-size:0.8rem;">
                <i class="bi bi-shield-lock me-1"></i>
                Sistem E-Vote Kampus menjamin kerahasiaan pilihan Anda.
            </p>
        </div>
    @else
        {{-- ===== POLLING AKTIF CARDS ===== --}}
        <div class="row g-4">
            @foreach($pollsAktif as $poll)
            @php
                $sudah = in_array($poll->id, $sudahVotePollIds);
                $persenSisa = $poll->selesai_pada->diffInHours(now());
                $urgent = $persenSisa <= 24; // kurang dari 24 jam lagi
            @endphp
            <div class="col-md-6 col-lg-4">
                {{-- Kartu polling: highlight border jika urgent --}}
                <div class="evote-card h-100 position-relative overflow-hidden"
                     style="{{ $urgent ? 'border-top: 3px solid var(--evote-red)' : 'border-top: 3px solid var(--evote-navy)' }}">

                    {{-- Badge sudah vote --}}
                    @if($sudah)
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge rounded-pill"
                                  style="background:var(--evote-green);font-size:0.72rem;padding:5px 10px;">
                                <i class="bi bi-check2-circle me-1"></i>Sudah Vote
                            </span>
                        </div>
                    @endif

                    <div class="p-4">
                        {{-- Ikon & badge status --}}
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:44px;height:44px;background:rgba(15,42,74,0.08);font-size:1.3rem;flex-shrink:0;">
                                🗳️
                            </div>
                            <div>
                                @if($urgent)
                                    <span class="badge" style="background:var(--evote-red);font-size:0.72rem;">
                                        <i class="bi bi-alarm me-1"></i>Hampir Berakhir
                                    </span>
                                @else
                                    <span class="badge" style="background:var(--evote-green);font-size:0.72rem;">
                                        <span class="pulse-dot"></span>Sedang Berlangsung
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Judul polling --}}
                        <h6 class="fw-bold mb-2" style="color:var(--evote-navy);line-height:1.4;">
                            {{ $poll->judul }}
                        </h6>

                        {{-- Meta info --}}
                        <div class="mb-3" style="font-size:0.82rem;color:var(--evote-text-muted);">
                            <div class="mb-1">
                                <i class="bi bi-people me-1"></i>
                                {{ $poll->candidates_count }} kandidat
                            </div>
                            <div>
                                <i class="bi bi-clock me-1"></i>
                                {{ $poll->sisaWaktu() }}
                            </div>
                        </div>

                        {{-- Tombol aksi --}}
                        @if($sudah)
                            <div class="d-flex gap-2">
                                <a href="{{ route('polling.show', $poll) }}"
                                   class="btn btn-sm flex-fill"
                                   style="border:2px solid var(--evote-green);color:var(--evote-green);font-weight:600;">
                                    <i class="bi bi-check-circle me-1"></i>Suara Tercatat
                                </a>
                                <a href="{{ route('polling.hasil', $poll) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-bar-chart"></i>
                                </a>
                            </div>
                        @else
                            <a href="{{ route('polling.show', $poll) }}"
                               class="btn btn-evote-primary w-100">
                                <i class="bi bi-hand-index-thumb me-2"></i>Vote Sekarang
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Link ke semua polling --}}
        <div class="text-center mt-4">
            <a href="{{ route('polling.index') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-grid me-1"></i>Lihat Semua Polling
            </a>
        </div>
    @endif
</x-app-layout>
