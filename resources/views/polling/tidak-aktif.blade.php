<x-app-layout>
    <x-slot name="header">
        <h5 class="mb-0 fw-bold" style="color:var(--evote-navy);">{{ $poll->judul }}</h5>
    </x-slot>

    <div class="text-center py-5">
        <div style="font-size:4rem;opacity:0.3;">⏱️</div>
        <h5 class="mt-3 fw-bold" style="color:var(--evote-navy);">Polling Ini Tidak Sedang Aktif</h5>
        <p class="text-muted" style="font-size:0.88rem;max-width:400px;margin:0 auto 1.5rem;">
            @if($poll->status === 'draft')
                Polling ini belum dibuka oleh admin.
            @elseif($poll->status === 'selesai')
                Polling ini sudah ditutup. Terima kasih atas partisipasi Anda.
            @else
                Waktu voting sudah berakhir atau belum dimulai.
            @endif
        </p>
        @if($poll->status === 'selesai')
            <a href="{{ route('polling.hasil', $poll) }}"
               class="btn btn-evote-primary me-2">
                <i class="bi bi-bar-chart me-2"></i>Lihat Hasil Akhir
            </a>
        @endif
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-house me-2"></i>Kembali
        </a>
    </div>
</x-app-layout>
