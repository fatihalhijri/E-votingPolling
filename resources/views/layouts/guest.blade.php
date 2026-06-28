<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Meta SEO --}}
    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'E-Vote Kampus') }}</title>
    <meta name="description" content="Login ke sistem E-Vote Kampus — platform voting digital terpercaya.">

    {{-- Favicon --}}
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🗳️</text></svg>">

    {{-- Google Fonts: Poppins + Inter (stylevoting.md §3) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 CSS via CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ============================================================
           CSS VARIABLES — palet warna E-Vote Kampus (stylevoting.md §2)
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
           LAYOUT HALAMAN AUTH (login/register/forgot-password)
           Desain: background navy gradient, card putih di tengah
        ============================================================ */
        body {
            font-family: 'Inter', sans-serif;
            color: var(--evote-text-main);
            min-height: 100vh;
            /* Gradient background: navy ke navy-dark — kesan resmi & institutional */
            background: linear-gradient(135deg, var(--evote-navy) 0%, var(--evote-navy-dark) 60%, #04101E 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        h1, h2, h3, h4, h5 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }

        /* ============================================================
           LOGO / BRAND DI ATAS CARD AUTH
        ============================================================ */
        .auth-brand {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .auth-brand .brand-icon {
            font-size: 3rem;
            display: block;
            margin-bottom: 0.5rem;
            /* Efek glow halus pada ikon --*/
            filter: drop-shadow(0 0 12px rgba(212,160,23,0.5));
        }

        .auth-brand .brand-name {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: #fff;
            letter-spacing: -0.3px;
        }

        .auth-brand .brand-tagline {
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
        }

        /* ============================================================
           CARD AUTH — card putih bersih dengan bayangan
        ============================================================ */
        .auth-card {
            background: var(--evote-card);
            border-radius: 16px;
            padding: 2rem 2.5rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.08);
        }

        /* ============================================================
           FORM ELEMENTS — override Bootstrap agar sesuai palet warna
        ============================================================ */
        .form-control:focus {
            border-color: var(--evote-navy);
            box-shadow: 0 0 0 3px rgba(15,42,74,0.15);
        }

        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--evote-text-main);
        }

        /* Tombol submit utama */
        .btn-evote-primary {
            background-color: var(--evote-navy);
            border-color: var(--evote-navy);
            color: #fff;
            min-height: 44px; /* aksesibilitas: mudah diklik di HP */
            width: 100%;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 8px;
            transition: background-color 0.2s, transform 0.15s;
        }

        .btn-evote-primary:hover {
            background-color: var(--evote-navy-dark);
            border-color: var(--evote-navy-dark);
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-evote-primary:focus {
            /* Focus ring kuning untuk aksesibilitas (stylevoting.md §6) */
            outline: 3px solid var(--evote-gold);
            outline-offset: 2px;
        }

        /* Link "lupa password", "sudah punya akun", dll. */
        .auth-link {
            color: var(--evote-navy);
            font-weight: 500;
        }

        .auth-link:hover {
            color: var(--evote-navy-dark);
        }

        /* ============================================================
           FOOTER KECIL di bawah card auth
        ============================================================ */
        .auth-footer-note {
            text-align: center;
            margin-top: 1.5rem;
            color: rgba(255,255,255,0.45);
            font-size: 0.8rem;
        }

        /* ============================================================
           INDIKATOR KEAMANAN di bawah card (lock icon)
        ============================================================ */
        .auth-security-note {
            text-align: center;
            margin-top: 1rem;
            color: rgba(255,255,255,0.5);
            font-size: 0.78rem;
        }
    </style>

    @stack('styles')
</head>
<body>

    {{-- ===== BRAND / LOGO ===== --}}
    <div class="auth-brand">
        <span class="brand-icon">🗳️</span>
        <div class="brand-name">E-Vote Kampus</div>
        <div class="brand-tagline">Platform Voting Digital Kampus yang Terpercaya</div>
    </div>

    {{-- ===== CARD AUTH (berisi @slot dari halaman login/register) ===== --}}
    <div class="auth-card">
        {{ $slot }}
    </div>

    {{-- ===== INDIKATOR KEAMANAN ===== --}}
    <div class="auth-security-note">
        <i class="bi bi-shield-lock-fill me-1"></i>
        Koneksi aman · Suara Anda bersifat rahasia · {{ date('Y') }} E-Vote Kampus
    </div>

    {{-- Bootstrap 5 JS Bundle via CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
