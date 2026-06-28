<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — ' : '' }}Admin · {{ config('app.name') }}</title>
    <meta name="description" content="Panel admin E-Vote Kampus.">

    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🗳️</text></svg>">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 + Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ============================================================
           CSS VARIABLES — sama persis dengan layout mahasiswa
           (supaya konsisten jika ada shared component)
        ============================================================ */
        :root {
            --evote-navy:       #0F2A4A;
            --evote-navy-dark:  #08172E;
            --evote-gold:       #D4A017;
            --evote-red:        #C8313C;
            --evote-green:      #1F9D55;
            --evote-bg:         #F4F6F9;
            --evote-card:       #FFFFFF;
            --evote-border:     #E2E6EC;
            --evote-text-main:  #1A2233;
            --evote-text-muted: #6B7280;
        }

        /* ============================================================
           TIPOGRAFI
        ============================================================ */
        body {
            font-family: 'Inter', sans-serif;
            color: var(--evote-text-main);
            background: var(--evote-bg);
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }

        /* ============================================================
           ADMIN LAYOUT: Sidebar kiri + konten kanan
           (stylevoting.md §4.2)
        ============================================================ */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ============================================================
           SIDEBAR (stylevoting.md §4.2)
           - Background navy-dark
           - Item aktif: border-left kuning + bg sedikit terang
        ============================================================ */
        .admin-sidebar {
            width: 260px;
            min-width: 260px;
            background: var(--evote-navy-dark);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        /* Brand / Logo di atas sidebar */
        .sidebar-brand {
            padding: 1.5rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand .brand-icon {
            font-size: 1.6rem;
        }

        .sidebar-brand .brand-text {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: #fff;
            font-size: 1rem;
            line-height: 1.2;
        }

        .sidebar-brand .brand-sub {
            font-size: 0.7rem;
            color: var(--evote-gold);
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Menu navigasi sidebar */
        .sidebar-nav {
            padding: 1rem 0;
            flex: 1;
        }

        .sidebar-nav .nav-section-label {
            padding: 0.5rem 1.25rem 0.25rem;
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
        }

        .sidebar-nav .nav-item {
            margin: 2px 0.5rem;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.65rem 0.85rem;
            color: rgba(255,255,255,0.75);
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            text-decoration: none;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,0.07);
            color: #fff;
        }

        /* Item menu yang sedang aktif: border gold + bg terang */
        .sidebar-nav .nav-link.active {
            background: rgba(212,160,23,0.12);
            color: #fff;
            border-left-color: var(--evote-gold);
            font-weight: 600;
        }

        .sidebar-nav .nav-link .nav-icon {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        /* ============================================================
           MAIN CONTENT AREA
        ============================================================ */
        .admin-content {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ============================================================
           TOPBAR (breadcrumb + info admin)
        ============================================================ */
        .admin-topbar {
            background: var(--evote-card);
            border-bottom: 1px solid var(--evote-border);
            padding: 0.85rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 900;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }

        .admin-topbar .page-breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.88rem;
            color: var(--evote-text-muted);
        }

        .admin-topbar .page-breadcrumb .current {
            color: var(--evote-text-main);
            font-weight: 600;
        }

        .admin-topbar .topbar-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .admin-topbar .user-info {
            text-align: right;
            font-size: 0.82rem;
        }

        .admin-topbar .user-info .user-name {
            font-weight: 600;
            color: var(--evote-text-main);
        }

        .admin-topbar .user-info .user-role {
            color: var(--evote-gold);
            font-size: 0.75rem;
        }

        /* ============================================================
           KONTEN UTAMA HALAMAN
        ============================================================ */
        .admin-page-content {
            padding: 1.75rem 1.5rem;
            flex: 1;
        }

        /* ============================================================
           KOMPONEN UI — Button, Card, Table, Badge
        ============================================================ */
        .btn-evote-primary {
            background-color: var(--evote-navy);
            border-color: var(--evote-navy);
            color: #fff;
            min-height: 40px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-evote-primary:hover {
            background-color: var(--evote-navy-dark);
            border-color: var(--evote-navy-dark);
            color: #fff;
        }

        .evote-card {
            background: var(--evote-card);
            border: 1px solid var(--evote-border);
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }

        /* Tabel admin: header navy, baris hover */
        .evote-table thead th {
            background-color: var(--evote-navy);
            color: #fff;
            font-weight: 600;
            font-size: 0.83rem;
            letter-spacing: 0.3px;
            border: none;
            padding: 0.85rem 1rem;
        }

        .evote-table tbody tr {
            transition: background 0.15s;
        }

        .evote-table tbody tr:hover {
            background-color: rgba(15,42,74,0.03);
        }

        .evote-table tbody td {
            vertical-align: middle;
            padding: 0.85rem 1rem;
            font-size: 0.88rem;
            border-color: var(--evote-border);
        }

        /* Badge status polling */
        .badge-aktif   { background-color: var(--evote-green) !important; }
        .badge-draft   { background-color: var(--evote-text-muted) !important; }
        .badge-selesai { background-color: var(--evote-navy) !important; }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(1.4); }
        }
        .pulse-dot {
            display: inline-block; width: 7px; height: 7px;
            border-radius: 50%; background: var(--evote-green);
            animation: pulse-dot 1.5s ease-in-out infinite;
            margin-right: 4px;
        }

        /* ============================================================
           RESPONSIVE: Sidebar jadi hamburger di mobile
        ============================================================ */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-260px);
            }
            .admin-sidebar.open {
                transform: translateX(0);
            }
            .admin-content {
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="admin-wrapper">

    {{-- ===== SIDEBAR (stylevoting.md §4.2) ===== --}}
    <aside class="admin-sidebar" id="adminSidebar">
        {{-- Brand --}}
        <div class="sidebar-brand">
            <span class="brand-icon">🗳️</span>
            <div>
                <div class="brand-text">E-Vote Kampus</div>
                <div class="brand-sub">Panel Admin</div>
            </div>
        </div>

        {{-- Menu Navigasi --}}
        <nav class="sidebar-nav">
            <div class="nav-section-label">Menu Utama</div>

            <div class="nav-item">
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 nav-icon"></i>
                    Dashboard
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.polls.index') }}"
                   class="nav-link {{ request()->routeIs('admin.polls.*') || request()->routeIs('admin.candidates.*') ? 'active' : '' }}">
                    <i class="bi bi-ballot-check nav-icon"></i>
                    Kelola Polling
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.audit') }}"
                   class="nav-link {{ request()->routeIs('admin.audit') ? 'active' : '' }}">
                    <i class="bi bi-journal-text nav-icon"></i>
                    Audit Log
                </a>
            </div>

            <div class="nav-section-label mt-3">Aksi Cepat</div>

            <div class="nav-item">
                <a href="{{ route('admin.polls.create') }}"
                   class="nav-link {{ request()->routeIs('admin.polls.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle nav-icon"></i>
                    Buat Polling Baru
                </a>
            </div>

            <div class="nav-section-label mt-3">Lainnya</div>

            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link">
                    <i class="bi bi-house nav-icon"></i>
                    Halaman Mahasiswa
                </a>
            </div>

            <div class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent"
                            style="color: rgba(255,255,255,0.6);">
                        <i class="bi bi-box-arrow-right nav-icon"></i>
                        Keluar
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    {{-- ===== KONTEN UTAMA ===== --}}
    <div class="admin-content" id="adminContent">

        {{-- Topbar --}}
        <div class="admin-topbar">
            <div class="d-flex align-items-center gap-3">
                {{-- Hamburger untuk mobile --}}
                <button class="btn btn-sm border-0 d-md-none" id="sidebarToggle"
                        style="color: var(--evote-navy);">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="page-breadcrumb">
                    <i class="bi bi-speedometer2 me-1"></i>Admin
                    @isset($breadcrumb)
                        <span>/</span>
                        <span class="current">{{ $breadcrumb }}</span>
                    @endisset
                </div>
            </div>
            <div class="topbar-user">
                <div class="user-info d-none d-sm-block">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">Administrator</div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:36px;height:36px;background:var(--evote-navy);color:#fff;font-weight:700;font-size:0.9rem;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
        </div>

        {{-- Toast Notification --}}
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;">
            @if(session('success'))
                <div class="toast align-items-center text-bg-success border-0 show" role="alert">
                    <div class="d-flex">
                        <div class="toast-body"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="toast align-items-center text-bg-danger border-0 show" role="alert">
                    <div class="d-flex">
                        <div class="toast-body"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            @endif
        </div>

        {{-- Page Content --}}
        <div class="admin-page-content">
            {{ $slot }}
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-hide toast setelah 4 detik
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toast').forEach(function(el) {
            setTimeout(() => bootstrap.Toast.getOrCreateInstance(el).hide(), 4000);
        });
        // Sidebar toggle untuk mobile
        const toggle = document.getElementById('sidebarToggle');
        if (toggle) {
            toggle.addEventListener('click', () => {
                document.getElementById('adminSidebar').classList.toggle('open');
            });
        }
    });
</script>
@stack('scripts')
</body>
</html>
