<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Meta SEO --}}
    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'E-Vote Kampus') }}</title>
    <meta name="description" content="Platform voting dan polling kampus yang aman, transparan, dan real-time.">

    {{-- Favicon: kotak suara sederhana via emoji favicon --}}
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🗳️</text></svg>">

    {{-- Google Fonts: Poppins (heading) + Inter (body) — sesuai stylevoting.md bagian 3 --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 CSS via CDN (tanpa npm build process) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ============================================================
           CSS VARIABLES — palet warna E-Vote Kampus (stylevoting.md §2)
           Didefinisikan sekali di sini, dipakai di semua halaman.
        ============================================================ */
        :root {
            --evote-navy:       #0F2A4A;   /* Primary: header, sidebar, tombol utama */
            --evote-navy-dark:  #08172E;   /* Hover state, sidebar aktif */
            --evote-gold:       #D4A017;   /* Accent: badge Aktif, highlight */
            --evote-red:        #C8313C;   /* Danger: hapus, status Ditutup */
            --evote-green:      #1F9D55;   /* Success: vote berhasil, status Aktif */

            --evote-bg:         #F4F6F9;   /* Background halaman */
            --evote-card:       #FFFFFF;   /* Background card */
            --evote-border:     #E2E6EC;
            --evote-text-main:  #1A2233;
            --evote-text-muted: #6B7280;

            /* Warna chart per kandidat (Chart.js) */
            --evote-chart-1: #0F2A4A;
            --evote-chart-2: #D4A017;
            --evote-chart-3: #1F9D55;
            --evote-chart-4: #6D4C9C;
            --evote-chart-5: #C8313C;
        }

        /* ============================================================
           TIPOGRAFI — body Inter, heading Poppins (stylevoting.md §3)
        ============================================================ */
        body {
            font-family: 'Inter', sans-serif;
            color: var(--evote-text-main);
            background: var(--evote-bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, h4, .navbar-brand, .poll-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }

        /* ============================================================
           NAVBAR — warna navy, teks putih (stylevoting.md §4.1)
        ============================================================ */
        .evote-navbar {
            background-color: var(--evote-navy);
            box-shadow: 0 2px 8px rgba(0,0,0,0.18);
        }

        .evote-navbar .navbar-brand {
            color: #fff !important;
            font-size: 1.2rem;
            letter-spacing: -0.3px;
        }

        .evote-navbar .nav-link {
            color: rgba(255,255,255,0.85) !important;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .evote-navbar .nav-link:hover {
            color: var(--evote-gold) !important;
        }

        .evote-navbar .btn-logout {
            border-color: rgba(255,255,255,0.4);
            color: #fff;
            font-size: 0.85rem;
        }

        .evote-navbar .btn-logout:hover {
            background: rgba(255,255,255,0.12);
        }

        /* ============================================================
           KONTEN — max-width 960px centered (stylevoting.md §4.1)
        ============================================================ */
        .evote-main {
            flex: 1;
        }

        .evote-container {
            max-width: 960px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        /* ============================================================
           FOOTER
        ============================================================ */
        .evote-footer {
            background: var(--evote-navy-dark);
            color: rgba(255,255,255,0.6);
            font-size: 0.82rem;
            padding: 1rem 0;
            text-align: center;
        }

        /* ============================================================
           OVERRIDE BOOTSTRAP — tombol utama dengan warna navy
        ============================================================ */
        .btn-evote-primary {
            background-color: var(--evote-navy);
            border-color: var(--evote-navy);
            color: #fff;
            min-height: 44px; /* aksesibilitas: mudah diklik di HP */
            transition: background-color 0.2s, transform 0.15s;
        }

        .btn-evote-primary:hover {
            background-color: var(--evote-navy-dark);
            border-color: var(--evote-navy-dark);
            color: #fff;
            transform: scale(1.02);
        }

        .btn-evote-primary:focus {
            outline: 3px solid var(--evote-gold);
            outline-offset: 2px;
        }

        /* ============================================================
           BADGE STATUS POLLING
        ============================================================ */
        .badge-aktif {
            background-color: var(--evote-green);
            color: #fff;
        }

        .badge-draft {
            background-color: var(--evote-text-muted);
            color: #fff;
        }

        .badge-selesai {
            background-color: var(--evote-navy);
            color: #fff;
        }

        /* Animasi pulse untuk dot "live" (stylevoting.md §4.3 & §5) */
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(1.4); }
        }

        .pulse-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--evote-green);
            animation: pulse-dot 1.5s ease-in-out infinite;
            margin-right: 5px;
        }

        /* ============================================================
           CARD STYLING
        ============================================================ */
        .evote-card {
            border: 1px solid var(--evote-border);
            border-radius: 12px;
            background: var(--evote-card);
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            transition: box-shadow 0.2s;
        }

        .evote-card:hover {
            box-shadow: 0 4px 16px rgba(15,42,74,0.1);
        }

        /* ============================================================
           TOAST NOTIFICATION (stylevoting.md §5 poin 3)
        ============================================================ */
        .toast-container {
            z-index: 9999;
        }
    </style>

    {{-- Slot untuk CSS tambahan per halaman --}}
    @stack('styles')
</head>
<body>

    {{-- ===== NAVBAR (stylevoting.md §4.1) ===== --}}
    <nav class="navbar navbar-expand-md evote-navbar py-2" id="main-navbar">
        <div class="container">
            {{-- Logo + Nama Aplikasi --}}
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <span style="font-size: 1.4rem;">🗳️</span>
                <span>E-Vote Kampus</span>
            </a>

            {{-- Tombol hamburger untuk mobile --}}
            <button class="navbar-toggler border-0" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarMain"
                    aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('dashboard') ? 'fw-semibold text-white' : '' }}"
                           href="{{ route('dashboard') }}">
                            <i class="bi bi-house me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('polling*') ? 'fw-semibold text-white' : '' }}"
                           href="{{ route('polling.index') }}">
                            <i class="bi bi-ballot me-1"></i>Polling Aktif
                        </a>
                    </li>
                    @if(Auth::user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}"
                           style="color:var(--evote-gold) !important;font-weight:600;">
                            <i class="bi bi-gear me-1"></i>Panel Admin
                        </a>
                    </li>
                    @endif
                </ul>

                {{-- Info user + tombol logout --}}
                <div class="d-flex align-items-center gap-3">
                    <span class="text-white-50 small d-none d-md-inline">
                        <i class="bi bi-person-circle me-1"></i>
                        {{ Auth::user()->name }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light btn-logout">
                            <i class="bi bi-box-arrow-right me-1"></i>Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- ===== TOAST NOTIFICATION AREA (stylevoting.md §5) ===== --}}
    <div class="toast-container position-fixed top-0 end-0 p-3">
        @if(session('success'))
            <div class="toast align-items-center text-bg-success border-0 show" role="alert" id="toast-success">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="toast align-items-center text-bg-danger border-0 show" role="alert" id="toast-error">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
    </div>

    {{-- ===== KONTEN UTAMA ===== --}}
    <main class="evote-main">
        {{-- Header halaman (opsional — hanya tampil jika view set x-slot name="header") --}}
        @isset($header)
        <div style="background:var(--evote-card);border-bottom:1px solid var(--evote-border);padding:1rem 0;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
            <div class="evote-container" style="padding-top:0;padding-bottom:0;">
                {{ $header }}
            </div>
        </div>
        @endisset

        <div class="evote-container">
            {{ $slot }}
        </div>
    </main>

    {{-- ===== FOOTER (stylevoting.md §4.1) ===== --}}
    <footer class="evote-footer">
        <div class="container">
            © {{ date('Y') }} E-Vote Kampus &nbsp;·&nbsp;
            <i class="bi bi-lock-fill me-1"></i>Suara Anda bersifat rahasia
        </div>
    </footer>

    {{-- Bootstrap 5 JS Bundle (termasuk Popper) via CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto-hide toast setelah 4 detik (stylevoting.md §5 poin 3)
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toast').forEach(function(toastEl) {
                setTimeout(function() {
                    var toast = bootstrap.Toast.getOrCreateInstance(toastEl);
                    toast.hide();
                }, 4000);
            });
        });
    </script>

    {{-- Slot untuk script tambahan per halaman --}}
    @stack('scripts')
</body>
</html>
