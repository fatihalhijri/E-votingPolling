<x-app-layout>
    <x-slot name="header">
        <h5 class="mb-0 fw-bold" style="color:var(--evote-navy);">{{ $poll->judul }}</h5>
    </x-slot>

    {{-- =====================================================================
         HALAMAN KONFIRMASI SUDAH VOTE
         Tampil setelah mahasiswa berhasil vote, atau saat kembali ke halaman ini.
    ===================================================================== --}}

    <div class="text-center py-4">
        {{-- Animasi centang besar --}}
        <div class="mx-auto mb-4 rounded-circle d-flex align-items-center justify-content-center"
             style="width:100px;height:100px;background:linear-gradient(135deg,#1F9D55,#2ecc71);box-shadow:0 8px 32px rgba(31,157,85,0.35);">
            <i class="bi bi-check-lg text-white" style="font-size:3rem;"></i>
        </div>

        <h4 class="fw-bold mb-2" style="color:var(--evote-navy);">Suara Anda Telah Tercatat!</h4>
        <p class="text-muted mb-4" style="max-width:400px;margin:0 auto;">
            Terima kasih telah berpartisipasi dalam demokrasi kampus.
            Pilihan Anda bersifat rahasia dan telah tersimpan dengan aman.
        </p>

        {{-- Kartu info pilihan --}}
        <div class="evote-card p-4 mx-auto mb-4" style="max-width:400px;text-align:left;">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ $voteUser->candidate->urlFoto() }}"
                     alt="{{ $voteUser->candidate->nama_kandidat }}"
                     style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:3px solid var(--evote-gold);">
                <div>
                    <div class="text-muted" style="font-size:0.78rem;text-transform:uppercase;letter-spacing:0.5px;">
                        Kandidat Pilihan Anda
                    </div>
                    <div class="fw-bold" style="color:var(--evote-navy);font-size:1rem;">
                        No. {{ $voteUser->candidate->nomor_urut }} — {{ $voteUser->candidate->nama_kandidat }}
                    </div>
                    <div class="text-muted" style="font-size:0.78rem;">
                        <i class="bi bi-clock me-1"></i>
                        Vote pada {{ $voteUser->voted_at->format('d M Y, H:i') }} WIB
                    </div>
                </div>
            </div>
        </div>

        {{-- Info kerahasiaan --}}
        <div class="d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill mb-4"
             style="background:rgba(15,42,74,0.06);font-size:0.82rem;color:var(--evote-text-muted);">
            <i class="bi bi-shield-lock-fill" style="color:var(--evote-navy);"></i>
            Pilihan Anda bersifat rahasia — tidak akan ditampilkan ke publik
        </div>

        {{-- Tombol aksi --}}
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('polling.hasil', $poll) }}"
               class="btn btn-evote-primary">
                <i class="bi bi-bar-chart-fill me-2"></i>Lihat Perkembangan Suara
            </a>
            <a href="{{ route('dashboard') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-house me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>

    @push('styles')
    <style>
        @keyframes bounceIn {
            0%   { transform: scale(0); opacity: 0; }
            60%  { transform: scale(1.15); }
            100% { transform: scale(1); opacity: 1; }
        }
        .check-circle { animation: bounceIn 0.5s ease forwards; }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Tambahkan class animasi setelah page load
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('[style*="width:100px"]').classList.add('check-circle');
        });
    </script>
    @endpush
</x-app-layout>
