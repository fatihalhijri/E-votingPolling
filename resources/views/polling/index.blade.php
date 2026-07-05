<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0 fw-bold" style="color:var(--evote-navy);">Semua Polling</h5>
                <small class="text-muted">Lihat dan ikuti polling yang tersedia.</small>
            </div>
            @php $jumlahAktif = $polls->where('status','aktif')->count(); @endphp
            @if($jumlahAktif > 0)
                <span class="badge px-3 py-2" style="background:var(--evote-green);font-size:0.78rem;">
                    <span class="pulse-dot"></span>{{ $jumlahAktif }} Sedang Berlangsung
                </span>
            @endif
        </div>
    </x-slot>

    {{-- =====================================================================
         HALAMAN DAFTAR POLLING — aktif + selesai (FASE 7 polish)
         Mahasiswa dapat melihat semua polling yang relevan.
    ===================================================================== --}}

    @if($polls->isEmpty())
        <div class="text-center py-5">
            <div style="font-size:5rem;opacity:0.2;">🗳️</div>
            <h6 class="mt-3 fw-bold" style="color:var(--evote-navy);">Tidak ada polling tersedia</h6>
            <p class="text-muted" style="font-size:0.88rem;">Pantau terus — polling baru akan muncul di sini.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-house me-1"></i>Ke Dashboard
            </a>
        </div>
    @else
        <div class="row g-4">
            @foreach($polls as $poll)
            @php
                $sudah   = in_array($poll->id, $sudahVotePollIds);
                $selesai = $poll->status === 'selesai';
                // Warna border kiri: hijau (sudah vote), navy (aktif), abu (selesai)
                $borderColor = $sudah ? 'var(--evote-green)' : ($selesai ? 'var(--evote-text-muted)' : 'var(--evote-navy)');
            @endphp
            <div class="col-md-6">
                <div class="evote-card h-100"
                     style="border-left: 4px solid {{ $borderColor }};">
                    <div class="p-4">

                        {{-- Header kartu: judul + badge status --}}
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-0" style="color:var(--evote-navy);flex:1;min-width:0;padding-right:8px;">
                                {{ $poll->judul }}
                            </h6>
                            @if($selesai)
                                <span class="badge flex-shrink-0" style="background:var(--evote-navy);font-size:0.72rem;">
                                    <i class="bi bi-flag-fill me-1"></i>Selesai
                                </span>
                            @elseif($sudah)
                                <span class="badge flex-shrink-0" style="background:var(--evote-green);font-size:0.72rem;">
                                    <i class="bi bi-check2 me-1"></i>Sudah Vote
                                </span>
                            @else
                                <span class="badge flex-shrink-0" style="background:var(--evote-navy);font-size:0.72rem;">
                                    <span class="pulse-dot" style="width:6px;height:6px;"></span>Aktif
                                </span>
                            @endif
                        </div>

                        {{-- Deskripsi --}}
                        @if($poll->deskripsi)
                            <p class="text-muted mb-3" style="font-size:0.83rem;line-height:1.5;">
                                {{ Str::limit($poll->deskripsi, 110) }}
                            </p>
                        @endif

                        {{-- Info: jumlah kandidat + waktu --}}
                        <div class="d-flex flex-wrap gap-3 mb-3" style="font-size:0.8rem;color:var(--evote-text-muted);">
                            <span><i class="bi bi-people me-1"></i>{{ $poll->candidates_count }} kandidat</span>
                            <span><i class="bi bi-hand-index-thumb me-1"></i>{{ number_format($poll->votes_count) }} suara</span>
                            @if(!$selesai)
                                <span><i class="bi bi-clock me-1"></i>{{ $poll->sisaWaktu() }}</span>
                            @else
                                <span><i class="bi bi-calendar-check me-1"></i>
                                    Selesai {{ $poll->selesai_pada->locale('id')->diffForHumans() }}
                                </span>
                            @endif
                        </div>

                        {{-- Tombol aksi --}}
                        <div class="d-flex gap-2">
                            @if($selesai)
                                {{-- Polling selesai: hanya lihat hasil --}}
                                <a href="{{ route('polling.hasil', $poll) }}"
                                   class="btn btn-evote-primary btn-sm flex-fill">
                                    <i class="bi bi-bar-chart me-1"></i>Lihat Hasil Akhir
                                </a>
                            @elseif($sudah)
                                {{-- Sudah vote: lihat status + lihat hasil --}}
                                <a href="{{ route('polling.show', $poll) }}"
                                   class="btn btn-sm flex-fill"
                                   style="border:2px solid var(--evote-green);color:var(--evote-green);font-weight:600;">
                                    <i class="bi bi-check-circle me-1"></i>Sudah Vote
                                </a>
                                <a href="{{ route('polling.hasil', $poll) }}"
                                   class="btn btn-sm btn-outline-secondary flex-fill">
                                    <i class="bi bi-bar-chart me-1"></i>Hasil
                                </a>
                            @else
                                {{-- Belum vote: vote sekarang --}}
                                <a href="{{ route('polling.show', $poll) }}"
                                   class="btn btn-evote-primary btn-sm flex-fill">
                                    <i class="bi bi-hand-index-thumb me-2"></i>Vote Sekarang
                                </a>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
