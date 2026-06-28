<x-admin-layout>
    <x-slot name="title">Dashboard Admin</x-slot>
    <x-slot name="breadcrumb">Dashboard</x-slot>

    {{-- =====================================================================
         DASHBOARD ADMIN — Statistik ringkas + activity feed
         (masterpromptvoting.md: Fase 6 — Dashboard & Audit)
    ===================================================================== --}}

    {{-- ===== KARTU STATISTIK ===== --}}
    <div class="row g-3 mb-4">

        {{-- Total Polling --}}
        <div class="col-6 col-lg-3">
            <div class="evote-card p-3 h-100 d-flex align-items-center gap-3"
                 style="border-left:4px solid var(--evote-navy);">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:48px;height:48px;background:rgba(15,42,74,0.1);flex-shrink:0;">
                    <i class="bi bi-ballot-check fs-5" style="color:var(--evote-navy);"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:1.6rem;font-family:'Poppins',sans-serif;color:var(--evote-navy);">
                        {{ $stats['total_polling'] }}
                    </div>
                    <div style="font-size:0.78rem;color:var(--evote-text-muted);">Total Polling</div>
                </div>
            </div>
        </div>

        {{-- Polling Aktif --}}
        <div class="col-6 col-lg-3">
            <div class="evote-card p-3 h-100 d-flex align-items-center gap-3"
                 style="border-left:4px solid var(--evote-green);">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:48px;height:48px;background:rgba(31,157,85,0.1);flex-shrink:0;">
                    <i class="bi bi-play-circle fs-5" style="color:var(--evote-green);"></i>
                </div>
                <div>
                    <div class="fw-bold d-flex align-items-center gap-1"
                         style="font-size:1.6rem;font-family:'Poppins',sans-serif;color:var(--evote-green);">
                        {{ $stats['polling_aktif'] }}
                        @if($stats['polling_aktif'] > 0)
                            <span class="pulse-dot" style="background:var(--evote-green);"></span>
                        @endif
                    </div>
                    <div style="font-size:0.78rem;color:var(--evote-text-muted);">Polling Aktif</div>
                </div>
            </div>
        </div>

        {{-- Total Suara --}}
        <div class="col-6 col-lg-3">
            <div class="evote-card p-3 h-100 d-flex align-items-center gap-3"
                 style="border-left:4px solid var(--evote-gold);">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:48px;height:48px;background:rgba(212,160,23,0.1);flex-shrink:0;">
                    <i class="bi bi-hand-index-thumb fs-5" style="color:var(--evote-gold);"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:1.6rem;font-family:'Poppins',sans-serif;color:var(--evote-gold);">
                        {{ number_format($stats['total_suara']) }}
                    </div>
                    <div style="font-size:0.78rem;color:var(--evote-text-muted);">Total Suara</div>
                </div>
            </div>
        </div>

        {{-- Partisipasi Mahasiswa --}}
        <div class="col-6 col-lg-3">
            <div class="evote-card p-3 h-100 d-flex align-items-center gap-3"
                 style="border-left:4px solid var(--evote-red);">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:48px;height:48px;background:rgba(200,49,60,0.1);flex-shrink:0;">
                    <i class="bi bi-people fs-5" style="color:var(--evote-red);"></i>
                </div>
                <div>
                    @php
                        $persen = $stats['total_mahasiswa'] > 0
                            ? round(($stats['mahasiswa_sudah_vote'] / $stats['total_mahasiswa']) * 100)
                            : 0;
                    @endphp
                    <div class="fw-bold" style="font-size:1.6rem;font-family:'Poppins',sans-serif;color:var(--evote-red);">
                        {{ $persen }}%
                    </div>
                    <div style="font-size:0.78rem;color:var(--evote-text-muted);">
                        Partisipasi ({{ $stats['mahasiswa_sudah_vote'] }}/{{ $stats['total_mahasiswa'] }})
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Polling Aktif — Status Suara --}}
        <div class="col-lg-7">
            <div class="evote-card h-100">
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold" style="color:var(--evote-navy);">
                        <i class="bi bi-bar-chart me-2"></i>Polling Aktif — Status Suara
                    </h6>
                    <a href="{{ route('admin.polls.index') }}" class="btn btn-sm btn-outline-secondary">
                        Kelola
                    </a>
                </div>
                @if($pollsAktif->isEmpty())
                    <div class="p-4 text-center text-muted" style="font-size:0.88rem;">
                        Tidak ada polling aktif saat ini.
                    </div>
                @else
                    <div class="p-3">
                        @foreach($pollsAktif as $poll)
                        @php
                            $partisipasi = $stats['total_mahasiswa'] > 0
                                ? round(($poll->votes_count / $stats['total_mahasiswa']) * 100)
                                : 0;
                        @endphp
                        <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div>
                                    <span class="fw-semibold" style="font-size:0.9rem;color:var(--evote-navy);">
                                        {{ $poll->judul }}
                                    </span>
                                    <span class="badge ms-2" style="background:var(--evote-green);font-size:0.68rem;">
                                        <span class="pulse-dot" style="width:6px;height:6px;"></span>Live
                                    </span>
                                </div>
                                <span class="fw-bold" style="font-size:0.85rem;color:var(--evote-navy);">
                                    {{ $poll->votes_count }} / {{ $stats['total_mahasiswa'] }}
                                </span>
                            </div>
                            <div class="progress mb-1" style="height:8px;border-radius:99px;background:rgba(15,42,74,0.08);">
                                <div class="progress-bar" style="width:{{ $partisipasi }}%;background:var(--evote-navy);border-radius:99px;"></div>
                            </div>
                            <div class="d-flex justify-content-between" style="font-size:0.75rem;color:var(--evote-text-muted);">
                                <span>{{ $partisipasi }}% partisipasi · {{ $poll->candidates_count }} kandidat</span>
                                <a href="{{ route('polling.hasil', $poll) }}" style="color:var(--evote-navy);">Lihat Hasil →</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Activity Feed — Suara Terbaru --}}
        <div class="col-lg-5">
            <div class="evote-card h-100">
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold" style="color:var(--evote-navy);">
                        <i class="bi bi-activity me-2"></i>Aktivitas Terbaru
                    </h6>
                    <a href="{{ route('admin.audit') }}" class="btn btn-sm btn-outline-secondary">
                        Lihat Semua
                    </a>
                </div>
                @if($suaraTerbaru->isEmpty())
                    <div class="p-4 text-center text-muted" style="font-size:0.88rem;">
                        Belum ada suara masuk.
                    </div>
                @else
                    <div class="p-3">
                        @foreach($suaraTerbaru as $vote)
                        <div class="d-flex align-items-start gap-3 mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                            {{-- Avatar --}}
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                 style="width:36px;height:36px;background:rgba(15,42,74,0.1);color:var(--evote-navy);font-size:0.85rem;">
                                {{ strtoupper(substr($vote->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-fill" style="min-width:0;">
                                <div class="fw-semibold text-truncate" style="font-size:0.85rem;color:var(--evote-navy);">
                                    {{ $vote->user->name }}
                                </div>
                                <div class="text-muted text-truncate" style="font-size:0.75rem;">
                                    voted di "{{ Str::limit($vote->poll->judul, 30) }}"
                                </div>
                                <div style="font-size:0.72rem;color:var(--evote-text-muted);">
                                    {{ $vote->voted_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
