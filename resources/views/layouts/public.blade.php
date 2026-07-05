<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="E-Vote Kampus — Platform voting dan polling kampus yang aman, transparan, dan real-time.">
    <title>@yield('title', 'Beranda') · E-Vote Kampus</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --evote-navy:      #0F2A4A;
            --evote-navy-dark: #08172E;
            --evote-gold:      #D4A017;
            --evote-green:     #198754;
            --evote-red:       #C8313C;
            --evote-bg:        #F4F6FA;
            --evote-border:    #E2E8F0;
            --evote-text-muted:#64748B;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--evote-bg);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
        }

        /* ===== NAVBAR ===== */
        .public-navbar {
            background: linear-gradient(135deg, var(--evote-navy) 0%, var(--evote-navy-dark) 100%);
            padding: 0.75rem 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .brand-text {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.2rem;
            color: #fff;
            text-decoration: none;
        }
        .brand-text span { color: var(--evote-gold); }

        /* ===== HERO ===== */
        .hero-section {
            background: linear-gradient(135deg, var(--evote-navy) 0%, #1a3a5c 100%);
            padding: 4rem 0 3rem;
            color: #fff;
        }
        .hero-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: clamp(1.7rem, 4vw, 2.6rem);
            line-height: 1.2;
        }
        .hero-title span { color: var(--evote-gold); }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.9);
            padding: 5px 14px;
            border-radius: 50px;
            font-size: 0.8rem;
        }
        .stat-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.85);
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.8rem;
        }
        .pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--evote-green);
            display: inline-block;
            animation: pulse 1.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity:1; transform:scale(1); }
            50%       { opacity:0.6; transform:scale(1.3); }
        }

        /* ===== CARD ===== */
        .evote-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--evote-border);
            box-shadow: 0 1px 8px rgba(15,42,74,0.06);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .evote-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(15,42,74,0.12);
        }

        /* ===== BUTTONS ===== */
        .btn-evote-primary {
            background: var(--evote-navy);
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .btn-evote-primary:hover { background:#1a3a5c; color:#fff; transform:scale(1.02); }

        .btn-guest-vote {
            background: var(--evote-navy);
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            transition: all 0.2s;
            text-decoration: none;
            display: block;
            text-align: center;
            padding: 0.4rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5;
        }
        .btn-guest-vote:hover { background:#1a3a5c; color:#fff; transform:scale(1.02); }

        /* ===== FOOTER ===== */
        footer {
            background: var(--evote-navy-dark);
            color: rgba(255,255,255,0.5);
            font-size: 0.8rem;
            padding: 1.25rem 0;
            margin-top: auto;
        }

        .section-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: var(--evote-navy);
            font-size: 1.35rem;
        }

        /* Dropdown user override */
        .dropdown-menu { border-radius: 12px !important; }
    </style>
</head>
<body>

    {{-- ===================================================================
         NAVBAR PUBLIK — adaptif: guest vs user login
    =================================================================== --}}
    <nav class="public-navbar">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <a href="{{ url('/') }}" class="brand-text">🗳️ E-Vote <span>Kampus</span></a>

                @guest
                    <div class="d-flex gap-2">
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-sm fw-bold"
                           style="background:var(--evote-gold);color:var(--evote-navy);">
                            <i class="bi bi-person-plus me-1"></i>Daftar
                        </a>
                    </div>
                @endguest

                @auth
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light d-none d-md-flex align-items-center gap-1">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-light dropdown-toggle d-flex align-items-center gap-2"
                                    data-bs-toggle="dropdown">
                                <span class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                                      style="width:26px;height:26px;background:var(--evote-gold);color:var(--evote-navy);font-size:0.7rem;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                                <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" style="min-width:180px;">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="fw-semibold" style="font-size:0.85rem;color:var(--evote-navy);">{{ Auth::user()->name }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">{{ Auth::user()->email }}</div>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('profile.edit') }}" style="font-size:0.88rem;">
                                        <i class="bi bi-person-gear me-2"></i>Pengaturan Akun
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2"
                                                style="font-size:0.88rem;color:var(--evote-red);">
                                            <i class="bi bi-box-arrow-right me-2"></i>Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Konten halaman --}}
    @yield('content')

    <footer>
        <div class="container text-center">
            © {{ date('Y') }} E-Vote Kampus ·
            <i class="bi bi-shield-lock mx-1"></i>Suara Anda bersifat rahasia
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
