<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0 fw-bold" style="color:var(--evote-navy);">Semua Polling Aktif</h5>
                <small class="text-muted">Pilih polling untuk memberikan suara Anda.</small>
            </div>
            <span class="badge px-3 py-2" style="background:var(--evote-green);font-size:0.78rem;">
                <span class="pulse-dot"></span>{{ $polls->count() }} Polling Berlangsung
            </span>
        </div>
    </x-slot>

    @if($polls->isEmpty())
        <div class="text-center py-5">
            <div style="font-size:5rem;opacity:0.2;">🗳️</div>
            <h6 class="mt-3 fw-bold" style="color:var(--evote-navy);">Tidak ada polling aktif</h6>
            <p class="text-muted" style="font-size:0.88rem;">Pantau terus — polling baru akan muncul di sini.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($polls as $poll)
            @php $sudah = in_array($poll->id, $sudahVotePollIds); @endphp
            <div class="col-md-6">
                <div class="evote-card h-100"
                     style="border-left: 4px solid {{ $sudah ? 'var(--evote-green)' : 'var(--evote-navy)' }};">
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h6 class="fw-bold mb-0" style="color:var(--evote-navy);">{{ $poll->judul }}</h6>
                            @if($sudah)
                                <span class="badge ms-2" style="background:var(--evote-green);white-space:nowrap;">
                                    <i class="bi bi-check2"></i> Sudah Vote
                                </span>
                            @else
                                <span class="badge ms-2" style="background:var(--evote-navy);white-space:nowrap;">
                                    <span class="pulse-dot"></span>Aktif
                                </span>
                            @endif
                        </div>

                        @if($poll->deskripsi)
                            <p class="text-muted mb-3" style="font-size:0.85rem;">
                                {{ Str::limit($poll->deskripsi, 120) }}
                            </p>
                        @endif

                        <div class="d-flex gap-3 mb-4" style="font-size:0.82rem;color:var(--evote-text-muted);">
                            <span><i class="bi bi-people me-1"></i>{{ $poll->candidates_count }} kandidat</span>
                            <span><i class="bi bi-clock me-1"></i>{{ $poll->sisaWaktu() }}</span>
                        </div>

                        <div class="d-flex gap-2">
                            @if($sudah)
                                <a href="{{ route('polling.show', $poll) }}"
                                   class="btn btn-sm flex-fill"
                                   style="border:2px solid var(--evote-green);color:var(--evote-green);font-weight:600;">
                                    <i class="bi bi-check-circle me-1"></i>Lihat Status
                                </a>
                                <a href="{{ route('polling.hasil', $poll) }}"
                                   class="btn btn-sm btn-outline-secondary flex-fill">
                                    <i class="bi bi-bar-chart me-1"></i>Lihat Hasil
                                </a>
                            @else
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
