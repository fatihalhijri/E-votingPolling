<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Akses Ditolak · E-Vote Kampus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --evote-navy: #0F2A4A;
            --evote-red:  #C8313C;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #3d0a0e 0%, #1a0304 100%);
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
            color: var(--evote-red);
            line-height: 1;
            text-shadow: 0 0 60px rgba(200,49,60,0.4);
        }
        .error-emoji {
            font-size: 4rem;
            animation: shake 0.6s ease infinite alternate;
        }
        @keyframes shake {
            0%   { transform: rotate(-5deg); }
            100% { transform: rotate(5deg); }
        }
        .btn-home {
            background: var(--evote-red);
            color: #fff;
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
            background: #a82530;
            transform: scale(1.04);
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="text-center px-4">
        <div class="error-emoji mb-3">🔒</div>
        <div class="error-code">403</div>
        <h2 class="fw-bold mt-2 mb-2" style="font-family:'Poppins',sans-serif;">Akses Ditolak</h2>
        <p style="color:rgba(255,255,255,0.6);font-size:0.95rem;max-width:420px;margin:0 auto 2rem;">
            Anda tidak memiliki izin untuk mengakses halaman ini.
            Jika Anda merasa ini adalah kesalahan, silakan hubungi administrator.
        </p>
        <a href="{{ url('/') }}" class="btn-home">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
        @auth
        <div class="mt-3">
            <a href="{{ route('dashboard') }}" style="color:rgba(255,255,255,0.5);font-size:0.85rem;">
                Ke Dashboard →
            </a>
        </div>
        @endauth
    </div>
</body>
</html>
