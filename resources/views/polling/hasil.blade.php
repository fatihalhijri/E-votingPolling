<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="mb-0 fw-bold" style="color:var(--evote-navy);">Hasil Polling</h5>
                <small class="text-muted">{{ $poll->judul }}</small>
            </div>
            @if($poll->status === 'aktif')
                <span class="badge ms-1" style="background:var(--evote-green);font-size:0.72rem;animation:fadeIn 0.3s;">
                    <span class="pulse-dot"></span>Live
                </span>
            @endif
        </div>
    </x-slot>

    {{-- =====================================================================
         FASE 5: HASIL REAL-TIME
         Menggunakan Chart.js (via CDN) untuk visualisasi:
         1. Donut Chart   → komposisi suara per kandidat
         2. Progress Bar  → persentase per kandidat (animated)
         3. Auto-refresh  → update data setiap 30 detik (polling aktif)
    ===================================================================== --}}

    {{-- Header stat total suara --}}
    <div class="evote-card p-4 mb-4 text-center"
         style="background:linear-gradient(135deg,var(--evote-navy),var(--evote-navy-dark));color:#fff;border:none;">
        <div id="totalSuaraDisplay"
             style="font-size:3rem;font-weight:800;font-family:'Poppins',sans-serif;color:var(--evote-gold);line-height:1;">
            {{ number_format($totalSuara) }}
        </div>
        <div style="font-size:0.9rem;opacity:0.75;margin-top:4px;">Total Suara Masuk</div>
        <div class="mt-2" style="font-size:0.8rem;opacity:0.6;">
            @if($poll->status === 'aktif')
                <i class="bi bi-arrow-repeat me-1"></i>Update otomatis setiap 30 detik
                &nbsp;·&nbsp;
                <i class="bi bi-clock me-1"></i>{{ $poll->sisaWaktu() }}
            @else
                <i class="bi bi-flag-fill me-1"></i>Polling telah selesai — hasil final
            @endif
        </div>
    </div>

    {{-- Peringatan jika belum vote --}}
    @if(!$sudahVote && $poll->sedangAktif())
        <div class="alert mb-4 d-flex align-items-center gap-3"
             style="background:rgba(212,160,23,0.1);border:1.5px solid var(--evote-gold);border-radius:12px;">
            <i class="bi bi-exclamation-triangle-fill fs-5" style="color:var(--evote-gold);"></i>
            <div class="flex-fill">
                <div class="fw-semibold" style="color:var(--evote-navy);">Anda belum memberikan suara</div>
                <div style="font-size:0.83rem;color:var(--evote-text-muted);">Ayo berpartisipasi sebelum polling berakhir!</div>
            </div>
            <a href="{{ route('polling.show', $poll) }}" class="btn btn-sm btn-evote-primary">
                <i class="bi bi-hand-index-thumb me-1"></i>Vote Sekarang
            </a>
        </div>
    @endif

    {{-- ===== MAIN CONTENT: Chart + Detail ===== --}}
    <div class="row g-4">

        {{-- Donut Chart (kiri) --}}
        <div class="col-lg-5">
            <div class="evote-card p-4 h-100 d-flex flex-column align-items-center justify-content-center">
                <h6 class="fw-bold mb-3 text-center" style="color:var(--evote-navy);">
                    <i class="bi bi-pie-chart me-2"></i>Distribusi Suara
                </h6>
                <div style="position:relative;width:100%;max-width:280px;">
                    <canvas id="donutChart" style="width:100%;"></canvas>
                    {{-- Label total di tengah donut --}}
                    <div id="chartCenter"
                         style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;">
                        <div style="font-size:1.6rem;font-weight:800;font-family:'Poppins',sans-serif;color:var(--evote-navy);"
                             id="chartCenterNum">{{ $totalSuara }}</div>
                        <div style="font-size:0.7rem;color:var(--evote-text-muted);">suara</div>
                    </div>
                </div>

                {{-- Legenda chart --}}
                <div class="mt-4 w-100" id="chartLegend">
                    @foreach($candidates as $i => $c)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-circle flex-shrink-0"
                             style="width:12px;height:12px;background:{{ ['#0F2A4A','#D4A017','#1F9D55','#6D4C9C','#C8313C'][$i % 5] }};"></div>
                        <span style="font-size:0.82rem;color:var(--evote-text-muted);">
                            No. {{ $c->nomor_urut }} {{ $c->nama_kandidat }}
                        </span>
                        <span class="ms-auto fw-semibold" style="font-size:0.82rem;color:var(--evote-navy);">
                            {{ $c->persentase }}%
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Bar detail per kandidat (kanan) --}}
        <div class="col-lg-7">
            <div class="d-flex flex-column gap-3">
                @foreach($candidates as $index => $candidate)
                @php $isLeading = $index === 0 && $totalSuara > 0; @endphp
                <div class="evote-card p-3"
                     style="{{ $isLeading ? 'border:2px solid var(--evote-gold);' : '' }}">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        {{-- Foto --}}
                        <div class="position-relative flex-shrink-0">
                            <img src="{{ $candidate->urlFoto() }}"
                                 alt="{{ $candidate->nama_kandidat }}"
                                 style="width:52px;height:52px;border-radius:50%;object-fit:cover;
                                        border:3px solid {{ $isLeading ? 'var(--evote-gold)' : 'var(--evote-border)' }};">
                            <span class="position-absolute bottom-0 end-0 rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                  style="width:20px;height:20px;background:var(--evote-navy);color:#fff;font-size:0.62rem;border:2px solid #fff;">
                                {{ $candidate->nomor_urut }}
                            </span>
                        </div>

                        <div class="flex-fill">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold" style="font-size:0.92rem;color:var(--evote-navy);">
                                    {{ $candidate->nama_kandidat }}
                                </span>
                                @if($isLeading)
                                    <span class="badge" style="background:var(--evote-gold);color:var(--evote-navy);font-size:0.68rem;">
                                        👑 Terdepan
                                    </span>
                                @endif
                            </div>
                            <div style="font-size:0.8rem;color:var(--evote-text-muted);">
                                {{ number_format($candidate->votes_count) }} suara
                            </div>
                        </div>

                        {{-- Persentase --}}
                        <div class="text-end flex-shrink-0">
                            <div class="fw-bold" style="font-size:1.5rem;font-family:'Poppins',sans-serif;color:var(--evote-navy);">
                                <span class="persen-val" data-target="{{ $candidate->persentase }}">0</span>%
                            </div>
                        </div>
                    </div>

                    {{-- Progress bar animasi --}}
                    <div class="progress" style="height:10px;border-radius:99px;background:rgba(15,42,74,0.08);">
                        <div class="progress-bar progress-bar-striped"
                             role="progressbar"
                             style="width:0%;border-radius:99px;transition:width 1.2s ease;
                                    background:{{ $isLeading
                                        ? 'linear-gradient(90deg,var(--evote-gold),#f5c518)'
                                        : 'linear-gradient(90deg,var(--evote-navy),#1a3d6b)' }};"
                             data-target="{{ $candidate->persentase }}">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tombol bawah --}}
    <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-house me-1"></i>Dashboard
        </a>
        @if($sudahVote || !$poll->sedangAktif())
            <button class="btn btn-outline-secondary" id="btnRefresh" onclick="refreshData()">
                <i class="bi bi-arrow-repeat me-1"></i>Refresh Sekarang
            </button>
        @endif
    </div>

    @push('styles')
    <style>
        @keyframes countUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .persen-val { animation: countUp 0.5s ease; }
    </style>
    @endpush

    @push('scripts')
    {{-- Chart.js via CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        // ================================================================
        // DATA DARI PHP → JavaScript
        // json_encode() mengonversi PHP array ke JSON yang bisa dibaca JS.
        // Kita pisahkan data di sini agar chart dan refresh mudah diupdate.
        // ================================================================
        const pollId    = {{ $poll->id }};
        const isAktif   = {{ $poll->status === 'aktif' ? 'true' : 'false' }};
        const hasilUrl  = '{{ route("polling.hasil", $poll) }}';

        const warna = ['#0F2A4A','#D4A017','#1F9D55','#6D4C9C','#C8313C'];

        let labels      = @json($candidates->pluck('nama_kandidat'));
        let suaraData   = @json($candidates->pluck('votes_count'));
        let totalSuara  = {{ $totalSuara }};

        // ================================================================
        // INISIALISASI DONUT CHART
        // ================================================================
        const ctx = document.getElementById('donutChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: suaraData.length > 0 && suaraData.some(v => v > 0)
                        ? suaraData
                        : [1], // tampilkan donut abu-abu jika belum ada suara
                    backgroundColor: suaraData.some(v => v > 0)
                        ? warna.slice(0, labels.length)
                        : ['#E2E6EC'],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 8,
                }]
            },
            options: {
                cutout: '72%',         // donut (bukan pie penuh)
                responsive: true,
                plugins: {
                    legend: { display: false }, // kita buat legenda sendiri
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                if (!suaraData.some(v => v > 0)) return 'Belum ada suara';
                                const persen = totalSuara > 0
                                    ? ((ctx.raw / totalSuara) * 100).toFixed(1) : 0;
                                return ` ${ctx.label}: ${ctx.raw} suara (${persen}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    duration: 800,
                }
            }
        });

        // ================================================================
        // ANIMASI PROGRESS BAR & COUNTER PERSENTASE
        // ================================================================
        function animasiProgressBar() {
            document.querySelectorAll('.progress-bar[data-target]').forEach(bar => {
                bar.style.width = bar.dataset.target + '%';
            });

            // Animasi angka persen (count-up effect)
            document.querySelectorAll('.persen-val').forEach(el => {
                const target = parseFloat(el.dataset.target);
                let current = 0;
                const step = target / 40; // 40 frame
                const interval = setInterval(() => {
                    current = Math.min(current + step, target);
                    el.textContent = current.toFixed(1);
                    if (current >= target) clearInterval(interval);
                }, 25);
            });
        }

        // Delay animasi agar bisa terlihat saat load
        setTimeout(animasiProgressBar, 300);

        // ================================================================
        // AUTO-REFRESH DATA (hanya jika polling masih aktif)
        // Cara kerja: setiap 30 detik, halaman di-reload.
        // Pendekatan sederhana ini sudah cukup untuk "real-time" di skala kampus.
        // ================================================================
        let countdown = 30;
        let refreshTimer;

        if (isAktif) {
            refreshTimer = setInterval(function() {
                countdown--;
                if (countdown <= 0) {
                    // Reload halaman untuk ambil data terbaru dari server
                    window.location.reload();
                }
            }, 1000);
        }

        function refreshData() {
            const btn = document.getElementById('btnRefresh');
            if (btn) {
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memuat...';
                btn.disabled = true;
            }
            window.location.reload();
        }
    </script>
    @endpush
</x-app-layout>
