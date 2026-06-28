<x-admin-layout>
    <x-slot name="title">Kandidat: {{ $poll->judul }}</x-slot>
    <x-slot name="breadcrumb">Kandidat Polling</x-slot>

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.polls.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="mb-0">Daftar Kandidat</h4>
                <p class="text-muted mb-0" style="font-size:0.85rem;">
                    Polling: <strong>{{ $poll->judul }}</strong>
                    &nbsp;·&nbsp;
                    @php $badge = $poll->badgeStatus(); @endphp
                    <span class="badge {{ $badge['class'] }} px-2">{{ $badge['label'] }}</span>
                </p>
            </div>
        </div>
        <a href="{{ route('admin.polls.candidates.create', $poll) }}" class="btn btn-evote-primary">
            <i class="bi bi-person-plus me-2"></i>Tambah Kandidat
        </a>
    </div>

    {{-- Kandidat Cards --}}
    @if($candidates->isEmpty())
        <div class="evote-card p-5 text-center">
            <div style="font-size:3rem;opacity:0.3;">👤</div>
            <h6 class="mt-3" style="color:var(--evote-navy);">Belum ada kandidat</h6>
            <p class="text-muted" style="font-size:0.88rem;">Tambahkan kandidat untuk polling ini.</p>
            <a href="{{ route('admin.polls.candidates.create', $poll) }}" class="btn btn-evote-primary mt-2">
                <i class="bi bi-person-plus me-2"></i>Tambah Kandidat Pertama
            </a>
        </div>
    @else
        {{-- Grid kartu kandidat --}}
        <div class="row g-4 mb-4">
            @foreach($candidates as $candidate)
            <div class="col-md-6 col-lg-4">
                <div class="evote-card h-100 overflow-hidden">

                    {{-- Foto kandidat --}}
                    <div style="height:200px;overflow:hidden;background:var(--evote-bg);">
                        <img src="{{ $candidate->urlFoto() }}"
                             alt="Foto {{ $candidate->nama_kandidat }}"
                             class="w-100 h-100"
                             style="object-fit:cover;">
                    </div>

                    <div class="p-3">
                        {{-- Nomor urut + nama --}}
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                  style="width:32px;height:32px;background:var(--evote-gold);color:var(--evote-navy);font-size:0.85rem;flex-shrink:0;">
                                {{ $candidate->nomor_urut }}
                            </span>
                            <h6 class="mb-0 fw-bold">{{ $candidate->nama_kandidat }}</h6>
                        </div>

                        {{-- Jumlah suara --}}
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-hand-index-thumb text-muted"></i>
                            <span style="font-size:0.85rem;color:var(--evote-text-muted);">
                                {{ number_format($candidate->votes_count) }} suara
                            </span>
                        </div>

                        {{-- Visi-misi preview --}}
                        @if($candidate->visi_misi)
                            <p class="text-muted mb-3" style="font-size:0.8rem;line-height:1.5;">
                                {{ Str::limit($candidate->visi_misi, 100) }}
                            </p>
                        @endif

                        {{-- Tombol aksi --}}
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.candidates.edit', $candidate) }}"
                               class="btn btn-sm btn-evote-primary flex-fill">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="hapusKandidat('{{ $candidate->nama_kandidat }}', '{{ route('admin.candidates.destroy', $candidate) }}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Ringkasan total suara --}}
        <div class="evote-card p-3 d-flex align-items-center gap-3" style="border-left:4px solid var(--evote-navy);">
            <i class="bi bi-bar-chart-fill fs-4" style="color:var(--evote-navy);"></i>
            <div>
                <div class="fw-semibold" style="font-size:0.9rem;">Total suara masuk: {{ $candidates->sum('votes_count') }}</div>
                <div class="text-muted" style="font-size:0.8rem;">dari {{ $candidates->count() }} kandidat</div>
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi Hapus Kandidat --}}
    <div class="modal fade" id="modalHapusKandidat" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
                <div class="modal-header border-0" style="background:var(--evote-red);">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-person-x me-2"></i>Hapus Kandidat
                    </h5>
                </div>
                <div class="modal-body py-4">
                    <p>Anda akan menghapus kandidat: <strong id="namaKandidatHapus"></strong></p>
                    <div class="alert alert-warning py-2 mb-0" style="font-size:0.83rem;">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Semua suara untuk kandidat ini akan ikut terhapus!
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="formHapusKandidat" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function hapusKandidat(nama, url) {
            document.getElementById('namaKandidatHapus').textContent = nama;
            document.getElementById('formHapusKandidat').action = url;
            new bootstrap.Modal(document.getElementById('modalHapusKandidat')).show();
        }
    </script>
    @endpush
</x-admin-layout>
