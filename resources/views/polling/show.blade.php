<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="mb-0 fw-bold" style="color:var(--evote-navy);">{{ $poll->judul }}</h5>
                <small class="text-muted">{{ $poll->sisaWaktu() }}</small>
            </div>
        </div>
    </x-slot>

    {{-- =====================================================================
         HALAMAN VOTING — Form pilih kandidat
         Tampilan premium: kartu kandidat besar + konfirmasi modal sebelum submit
         (stylevoting.md: kartu kandidat dengan efek hover premium)
    ===================================================================== --}}

    {{-- Flash error --}}
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-exclamation-circle-fill"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Info polling --}}
    @if($poll->deskripsi)
        <div class="evote-card p-3 mb-4 d-flex align-items-start gap-3"
             style="border-left:4px solid var(--evote-gold);">
            <i class="bi bi-info-circle-fill fs-5" style="color:var(--evote-gold);flex-shrink:0;margin-top:2px;"></i>
            <p class="mb-0" style="font-size:0.88rem;color:var(--evote-text-muted);">{{ $poll->deskripsi }}</p>
        </div>
    @endif

    {{-- Petunjuk voting --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
             style="width:28px;height:28px;background:var(--evote-navy);color:#fff;font-size:0.8rem;flex-shrink:0;">1</div>
        <span style="font-size:0.88rem;color:var(--evote-text-muted);">Klik kartu kandidat pilihan Anda</span>
        <i class="bi bi-arrow-right text-muted"></i>
        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
             style="width:28px;height:28px;background:var(--evote-navy);color:#fff;font-size:0.8rem;flex-shrink:0;">2</div>
        <span style="font-size:0.88rem;color:var(--evote-text-muted);">Konfirmasi pilihan Anda</span>
        <i class="bi bi-arrow-right text-muted"></i>
        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
             style="width:28px;height:28px;background:var(--evote-green);color:#fff;font-size:0.8rem;flex-shrink:0;">✓</div>
        <span style="font-size:0.88rem;color:var(--evote-text-muted);">Selesai!</span>
    </div>

    {{-- Form voting --}}
    <form method="POST" action="{{ route('polling.vote', $poll) }}" id="formVoting">
        @csrf

        {{-- Grid kartu kandidat —
             KENAPA tidak pakai radio button biasa?
             Karena UI radio button default jelek. Kita sembunyikan input radio,
             lalu buat kartu kustom yang "jadi radio button" saat diklik (JS).
             Ini teknik umum untuk custom form controls yang tetap accessible.
        --}}
        <div class="row g-4 mb-4" id="kandidatGrid">
            @foreach($candidates as $candidate)
            <div class="col-md-6 col-lg-4">
                <label for="kandidat_{{ $candidate->id }}" class="d-block h-100" style="cursor:pointer;">

                    {{-- Input radio tersembunyi — tetap di DOM untuk aksesibilitas dan submit form --}}
                    <input type="radio"
                           name="candidate_id"
                           id="kandidat_{{ $candidate->id }}"
                           value="{{ $candidate->id }}"
                           class="d-none kandidat-radio"
                           required>

                    {{-- Kartu kandidat — berubah style saat radio dipilih (via JS) --}}
                    <div class="evote-card h-100 kandidat-card overflow-hidden"
                         style="transition: all 0.25s ease; cursor: pointer;"
                         data-id="{{ $candidate->id }}">

                        {{-- Foto kandidat --}}
                        <div style="height:220px;overflow:hidden;position:relative;">
                            <img src="{{ $candidate->urlFoto() }}"
                                 alt="Foto {{ $candidate->nama_kandidat }}"
                                 class="w-100 h-100"
                                 style="object-fit:cover;transition: transform 0.3s ease;">

                            {{-- Overlay nomor urut --}}
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                      style="width:40px;height:40px;background:var(--evote-gold);color:var(--evote-navy);font-size:1rem;box-shadow:0 2px 8px rgba(0,0,0,0.2);">
                                    {{ $candidate->nomor_urut }}
                                </span>
                            </div>

                            {{-- Checkmark overlay saat dipilih --}}
                            <div class="position-absolute top-0 end-0 bottom-0 start-0 d-flex align-items-center justify-content-center pilihan-overlay"
                                 style="background:rgba(31,157,85,0.85);display:none!important;transition:all 0.2s;">
                                <div class="text-white text-center">
                                    <i class="bi bi-check-circle-fill" style="font-size:3rem;"></i>
                                    <div class="fw-bold mt-2">Dipilih</div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3">
                            <h6 class="fw-bold mb-1" style="color:var(--evote-navy);">
                                {{ $candidate->nama_kandidat }}
                            </h6>

                            @if($candidate->visi_misi)
                                <p class="text-muted mb-3" style="font-size:0.8rem;line-height:1.5;">
                                    {{ Str::limit($candidate->visi_misi, 80) }}
                                </p>

                                {{-- Tombol baca selengkapnya --}}
                                <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-0"
                                        style="font-size:0.78rem;"
                                        onclick="tampilVisiMisi(event, '{{ addslashes($candidate->nama_kandidat) }}', `{{ addslashes(nl2br(e($candidate->visi_misi))) }}`)">
                                    <i class="bi bi-file-text me-1"></i>Baca Visi Misi Lengkap
                                </button>
                            @endif
                        </div>
                    </div>
                </label>
            </div>
            @endforeach
        </div>

        {{-- Tombol submit — disabled sampai ada kandidat yang dipilih --}}
        <div class="d-flex justify-content-center">
            <button type="button"
                    id="btnKonfirmasi"
                    class="btn btn-evote-primary btn-lg px-5"
                    disabled
                    onclick="bukaModalKonfirmasi()">
                <i class="bi bi-hand-index-thumb me-2"></i>
                <span id="btnKonfirmasiText">Pilih Kandidat Terlebih Dahulu</span>
            </button>
        </div>
    </form>

    {{-- ===== MODAL KONFIRMASI VOTE ===== --}}
    <div class="modal fade" id="modalKonfirmasiVote" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius:20px;overflow:hidden;">
                <div class="modal-header border-0 text-white" style="background:var(--evote-navy);padding:1.5rem 1.5rem 1rem;">
                    <h5 class="modal-title">
                        <i class="bi bi-shield-check me-2"></i>Konfirmasi Pilihan Anda
                    </h5>
                </div>
                <div class="modal-body text-center py-4">
                    {{-- Avatar/foto kandidat terpilih (mini) --}}
                    <img id="konfirmasiAvatar"
                         src=""
                         alt=""
                         style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--evote-gold);margin-bottom:1rem;">

                    <p class="text-muted mb-1" style="font-size:0.88rem;">Anda akan memilih:</p>
                    <h5 class="fw-bold mb-1" style="color:var(--evote-navy);" id="konfirmasiNama">—</h5>
                    <p class="text-muted mb-3" style="font-size:0.82rem;">Nomor Urut: <strong id="konfirmasiNomor">—</strong></p>

                    <div class="alert alert-warning py-2" style="font-size:0.82rem;border-radius:10px;">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Pilihan tidak bisa diubah</strong> setelah dikonfirmasi.
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 gap-2">
                    <button class="btn btn-outline-secondary flex-fill" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left me-1"></i>Kembali & Ubah
                    </button>
                    <button type="button"
                            class="btn btn-evote-primary flex-fill"
                            id="btnSubmitVote"
                            onclick="submitVote()">
                        <i class="bi bi-check-lg me-1"></i>Ya, Konfirmasi Pilihan!
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MODAL VISI MISI ===== --}}
    <div class="modal fade" id="modalVisiMisi" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0" style="border-radius:16px;">
                <div class="modal-header" style="background:var(--evote-navy);color:#fff;border:none;">
                    <h5 class="modal-title" id="modalVisiMisiNama">Visi & Misi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="modalVisiMisiKonten" style="white-space:pre-wrap;font-size:0.9rem;line-height:1.7;"></div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* Kartu kandidat: hover efek angkat + glow */
        .kandidat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(15,42,74,0.15);
        }

        /* Kartu kandidat terpilih: border hijau + glow */
        .kandidat-card.dipilih {
            border: 2.5px solid var(--evote-green) !important;
            box-shadow: 0 0 0 4px rgba(31,157,85,0.15), 0 8px 24px rgba(15,42,74,0.12) !important;
            transform: translateY(-4px);
        }

        /* Overlay checkmark saat dipilih */
        .kandidat-card.dipilih .pilihan-overlay {
            display: flex !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        let kandidatDipilih = null;

        // ================================================================
        // Saat kartu kandidat diklik → aktifkan radio + update UI
        // ================================================================
        document.querySelectorAll('.kandidat-radio').forEach(function(radio) {
            radio.addEventListener('change', function() {
                kandidatDipilih = this.value;

                // Reset semua kartu ke state default
                document.querySelectorAll('.kandidat-card').forEach(c => c.classList.remove('dipilih'));

                // Aktifkan kartu yang dipilih
                this.closest('label').querySelector('.kandidat-card').classList.add('dipilih');

                // Aktifkan tombol konfirmasi
                const btn = document.getElementById('btnKonfirmasi');
                const label = this.closest('label');
                const nama = label.querySelector('h6').textContent.trim();
                const nomor = label.querySelector('.kandidat-card [style*="gold"]').textContent.trim();

                btn.disabled = false;
                btn.style.background = 'var(--evote-green)';
                btn.style.borderColor = 'var(--evote-green)';
                document.getElementById('btnKonfirmasiText').textContent = 'Konfirmasi: ' + nama;

                // Simpan data untuk modal konfirmasi
                btn.dataset.nama = nama;
                btn.dataset.nomor = nomor;
                btn.dataset.foto = label.querySelector('img').src;
            });
        });

        // ================================================================
        // Buka modal konfirmasi dengan data kandidat terpilih
        // ================================================================
        function bukaModalKonfirmasi() {
            const btn = document.getElementById('btnKonfirmasi');
            document.getElementById('konfirmasiNama').textContent = btn.dataset.nama;
            document.getElementById('konfirmasiNomor').textContent = btn.dataset.nomor;
            document.getElementById('konfirmasiAvatar').src = btn.dataset.foto;
            document.getElementById('konfirmasiAvatar').alt = btn.dataset.nama;
            new bootstrap.Modal(document.getElementById('modalKonfirmasiVote')).show();
        }

        // ================================================================
        // Submit form voting setelah konfirmasi
        // ================================================================
        function submitVote() {
            const btn = document.getElementById('btnSubmitVote');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
            document.getElementById('formVoting').submit();
        }

        // ================================================================
        // Tampilkan modal visi-misi kandidat
        // ================================================================
        function tampilVisiMisi(event, nama, konten) {
            event.preventDefault();
            event.stopPropagation(); // Jangan trigger klik kartu (jangan aktifkan radio)
            document.getElementById('modalVisiMisiNama').textContent = 'Visi & Misi: ' + nama;
            // konten sudah di-nl2br, tampilkan sebagai HTML
            document.getElementById('modalVisiMisiKonten').innerHTML = konten;
            new bootstrap.Modal(document.getElementById('modalVisiMisi')).show();
        }
    </script>
    @endpush
</x-app-layout>
