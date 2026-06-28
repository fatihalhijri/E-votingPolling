<x-admin-layout>
    <x-slot name="title">Audit Log</x-slot>
    <x-slot name="breadcrumb">Audit Log Voting</x-slot>

    {{-- =====================================================================
         AUDIT LOG — Rekam Jejak Aktivitas Voting
         PRINSIP PENTING: Log ini menampilkan SIAPA yang sudah vote dan KAPAN,
         tapi TIDAK menampilkan kandidat yang dipilih (secret ballot).
    ===================================================================== --}}

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Audit Log Voting</h4>
            <p class="text-muted mb-0" style="font-size:0.85rem;">
                Rekam jejak aktivitas voting.
                <strong class="text-success">Pilihan kandidat tidak ditampilkan</strong> (secret ballot).
            </p>
        </div>
        <div class="badge px-3 py-2" style="background:var(--evote-navy);font-size:0.8rem;">
            <i class="bi bi-shield-lock me-1"></i>Secret Ballot Mode
        </div>
    </div>

    {{-- Filter berdasarkan polling --}}
    <div class="evote-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.audit') }}" class="d-flex gap-3 align-items-end flex-wrap">
            <div class="flex-fill" style="min-width:200px;">
                <label class="form-label fw-semibold mb-1" style="font-size:0.83rem;">
                    Filter Polling
                </label>
                <select name="poll_id" class="form-select form-select-sm">
                    <option value="">— Semua Polling —</option>
                    @foreach($polls as $poll)
                        <option value="{{ $poll->id }}"
                                {{ request('poll_id') == $poll->id ? 'selected' : '' }}>
                            {{ $poll->judul }} ({{ ucfirst($poll->status) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-evote-primary">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                @if(request('poll_id'))
                    <a href="{{ route('admin.audit') }}" class="btn btn-sm btn-outline-secondary">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel audit log --}}
    <div class="evote-card">
        @if($votes->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                Belum ada aktivitas voting yang tercatat.
            </div>
        @else
            <div class="table-responsive">
                <table class="table evote-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Mahasiswa</th>
                            <th>NIM</th>
                            <th>Polling</th>
                            <th style="width:180px">Waktu Vote</th>
                            <th style="width:120px" class="text-center">Pilihan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($votes as $vote)
                        <tr>
                            <td class="text-muted">
                                {{ $vote->id }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                         style="width:30px;height:30px;background:rgba(15,42,74,0.1);color:var(--evote-navy);font-size:0.8rem;flex-shrink:0;">
                                        {{ strtoupper(substr($vote->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="font-size:0.88rem;">
                                            {{ $vote->user->name }}
                                        </div>
                                        <div class="text-muted" style="font-size:0.75rem;">
                                            {{ $vote->user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge" style="background:rgba(15,42,74,0.1);color:var(--evote-navy);font-size:0.78rem;">
                                    {{ $vote->user->nim ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold" style="font-size:0.85rem;color:var(--evote-navy);">
                                    {{ $vote->poll->judul }}
                                </div>
                                @php $badge = $vote->poll->badgeStatus(); @endphp
                                <span class="badge {{ $badge['class'] }}" style="font-size:0.68rem;">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td>
                                <div style="font-size:0.85rem;">
                                    {{ $vote->voted_at->format('d M Y') }}
                                </div>
                                <div class="text-muted" style="font-size:0.78rem;">
                                    {{ $vote->voted_at->format('H:i:s') }} WIB
                                </div>
                                <div style="font-size:0.72rem;color:var(--evote-text-muted);">
                                    {{ $vote->voted_at->diffForHumans() }}
                                </div>
                            </td>

                            {{-- Pilihan kandidat DISEMBUNYIKAN (secret ballot) --}}
                            <td class="text-center">
                                <span class="badge px-3 py-2 rounded-pill"
                                      style="background:rgba(15,42,74,0.08);color:var(--evote-text-muted);font-size:0.75rem;">
                                    <i class="bi bi-shield-lock me-1"></i>Rahasia
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginasi + info jumlah --}}
            <div class="p-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="text-muted" style="font-size:0.82rem;">
                    Menampilkan {{ $votes->firstItem() }}–{{ $votes->lastItem() }}
                    dari {{ number_format($votes->total()) }} entri
                </div>
                {{ $votes->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</x-admin-layout>
