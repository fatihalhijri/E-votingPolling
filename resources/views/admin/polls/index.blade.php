<x-admin-layout>
    <x-slot name="title">Kelola Polling</x-slot>
    <x-slot name="breadcrumb">Kelola Polling</x-slot>

    {{-- ===== PAGE HEADER ===== --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Kelola Polling</h4>
            <p class="text-muted mb-0" style="font-size:0.88rem;">
                Buat, edit, dan atur status semua polling yang ada.
            </p>
        </div>
        <a href="{{ route('admin.polls.create') }}" class="btn btn-evote-primary">
            <i class="bi bi-plus-lg me-2"></i>Buat Polling Baru
        </a>
    </div>

    {{-- ===== TABEL DAFTAR POLLING ===== --}}
    <div class="evote-card">
        @if($polls->isEmpty())
            {{-- Empty state jika belum ada polling --}}
            <div class="text-center py-5">
                <div style="font-size:3.5rem;opacity:0.35;">🗳️</div>
                <h6 class="mt-3" style="color:var(--evote-navy);">Belum ada polling</h6>
                <p class="text-muted" style="font-size:0.88rem;">Klik tombol "Buat Polling Baru" untuk memulai.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table evote-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Judul Polling</th>
                            <th style="width:130px">Status</th>
                            <th style="width:180px">Periode</th>
                            <th style="width:100px" class="text-center">Suara</th>
                            <th style="width:200px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($polls as $poll)
                        <tr>
                            {{-- Nomor urut berdasarkan paginasi --}}
                            <td class="text-muted">{{ $loop->iteration + ($polls->currentPage() - 1) * $polls->perPage() }}</td>

                            {{-- Judul + badge status --}}
                            <td>
                                <div class="fw-semibold" style="font-size:0.92rem;">{{ $poll->judul }}</div>
                                @if($poll->deskripsi)
                                    <div class="text-muted mt-1" style="font-size:0.8rem;">
                                        {{ Str::limit($poll->deskripsi, 70) }}
                                    </div>
                                @endif
                            </td>

                            {{-- Badge status dengan dot animasi jika aktif --}}
                            <td>
                                @php $badge = $poll->badgeStatus(); @endphp
                                <span class="badge {{ $badge['class'] }} px-3 py-2 rounded-pill">
                                    @if($poll->status === 'aktif')
                                        <span class="pulse-dot"></span>
                                    @endif
                                    {{ $badge['label'] }}
                                </span>
                            </td>

                            {{-- Periode waktu --}}
                            <td>
                                <div style="font-size:0.8rem;">
                                    <div><i class="bi bi-calendar-check me-1 text-muted"></i>{{ $poll->mulai_pada->format('d M Y') }}</div>
                                    <div><i class="bi bi-calendar-x me-1 text-muted"></i>{{ $poll->selesai_pada->format('d M Y') }}</div>
                                </div>
                            </td>

                            {{-- Total suara --}}
                            <td class="text-center">
                                <span class="fw-bold" style="color:var(--evote-navy);font-size:1.1rem;">
                                    {{ number_format($poll->votes_count) }}
                                </span>
                                <div class="text-muted" style="font-size:0.75rem;">suara</div>
                            </td>

                            {{-- Aksi --}}
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    {{-- Kelola kandidat --}}
                                    <a href="{{ route('admin.polls.candidates.index', $poll) }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Kelola Kandidat">
                                        <i class="bi bi-people"></i>
                                        <span class="d-none d-xl-inline ms-1">Kandidat</span>
                                    </a>

                                    {{-- Edit polling --}}
                                    <a href="{{ route('admin.polls.edit', $poll) }}"
                                       class="btn btn-sm"
                                       style="background:var(--evote-navy);color:#fff;"
                                       title="Edit Polling">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{-- Hapus polling --}}
                                    <button type="button"
                                            class="btn btn-sm btn-danger"
                                            title="Hapus Polling"
                                            onclick="konfirmasiHapus('{{ $poll->judul }}', '{{ route('admin.polls.destroy', $poll) }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                {{-- Tombol ubah status --}}
                                <div class="mt-2">
                                    @if($poll->status === 'draft')
                                        <form method="POST" action="{{ route('admin.polls.updateStatus', $poll) }}" class="d-inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="aktif">
                                            <button class="btn btn-sm" style="background:var(--evote-green);color:#fff;font-size:0.75rem;"
                                                    onclick="return confirm('Buka polling ini? Mahasiswa bisa mulai vote.')">
                                                <i class="bi bi-play-fill"></i> Buka
                                            </button>
                                        </form>
                                    @elseif($poll->status === 'aktif')
                                        <form method="POST" action="{{ route('admin.polls.updateStatus', $poll) }}" class="d-inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="selesai">
                                            <button class="btn btn-sm btn-danger" style="font-size:0.75rem;"
                                                    onclick="return confirm('Tutup polling ini? Mahasiswa tidak bisa vote lagi.')">
                                                <i class="bi bi-stop-fill"></i> Tutup
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted" style="font-size:0.75rem;">Polling selesai</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginasi --}}
            @if($polls->hasPages())
                <div class="p-3 border-top">
                    {{ $polls->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @endif
    </div>

    {{-- ===== MODAL KONFIRMASI HAPUS ===== --}}
    <div class="modal fade" id="modalHapus" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
                <div class="modal-header border-0" style="background:var(--evote-red);">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus
                    </h5>
                </div>
                <div class="modal-body py-4">
                    <p class="mb-1">Anda akan menghapus polling:</p>
                    <p class="fw-bold mb-3" id="namaPollingHapus" style="color:var(--evote-navy);"></p>
                    <div class="alert alert-warning py-2 mb-0" style="font-size:0.85rem;">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Semua kandidat dan suara</strong> di polling ini akan ikut terhapus permanen!
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="formHapus" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Ya, Hapus Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Fungsi konfirmasi hapus — tampilkan modal dengan nama polling
        function konfirmasiHapus(nama, url) {
            document.getElementById('namaPollingHapus').textContent = '"' + nama + '"';
            document.getElementById('formHapus').action = url;
            new bootstrap.Modal(document.getElementById('modalHapus')).show();
        }
    </script>
    @endpush
</x-admin-layout>
