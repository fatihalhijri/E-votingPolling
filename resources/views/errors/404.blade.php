<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Halaman Tidak Ditemukan · E-Vote Kampus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --evote-navy: #0F2A4A;
            --evote-gold: #D4A017;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0F2A4A 0%, #08172E 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        .error-code {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(6rem, 20vw, 10rem);
            font-weight: 700;
            color: var(--evote-gold);
            line-height: 1;
            text-shadow: 0 0 60px rgba(212,160,23,0.35);
        }
        .error-emoji {
            font-size: 4rem;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-12px); }
        }
        .btn-home {
            background: var(--evote-gold);
            color: var(--evote-navy);
            font-weight: 700;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-size: 0.95rem;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-home:hover {
            background: #e5b520;
            transform: scale(1.04);
            color: var(--evote-navy);
        }
    </style>
</head>
<body>
    <div class="text-center px-4">
        <div class="error-emoji mb-3">🗺️</div>
        <div class="error-code">404</div>
        <h2 class="fw-bold mt-2 mb-2" style="font-family:'Poppins',sans-serif;">Halaman Tidak Ditemukan</h2>
        <p style="color:rgba(255,255,255,0.6);font-size:0.95rem;max-width:420px;margin:0 auto 2rem;">
            Halaman yang Anda cari tidak ada atau telah dipindahkan.
            Mungkin URL salah, atau polling yang Anda tuju sudah dihapus.
        </p>
        <a href="{{ url('/') }}" class="btn-home">
            <i class="bi bi-house me-2"></i>Kembali ke Beranda
        </a>
        @auth
        <div class="mt-3">
            <a href="{{ route('dashboard') }}" style="color:rgba(255,255,255,0.5);font-size:0.85rem;">
                Atau ke Dashboard →
            </a>
        </div>
        @endauth
    </div>
</body>
</html>
