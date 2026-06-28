<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Kesalahan Server · E-Vote Kampus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --evote-navy: #0F2A4A; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #0a0a14 100%);
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
            color: #6D4C9C;
            line-height: 1;
            text-shadow: 0 0 60px rgba(109,76,156,0.4);
        }
        .error-emoji {
            font-size: 4rem;
            animation: spin 4s linear infinite;
        }
        @keyframes spin {
            0%, 90%  { transform: rotate(0deg); }
            95%      { transform: rotate(15deg); }
            100%     { transform: rotate(0deg); }
        }
        .btn-home {
            background: #6D4C9C;
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
        .btn-home:hover { background: #5a3d80; transform: scale(1.04); color: #fff; }
    </style>
</head>
<body>
    <div class="text-center px-4">
        <div class="error-emoji mb-3">⚙️</div>
        <div class="error-code">500</div>
        <h2 class="fw-bold mt-2 mb-2" style="font-family:'Poppins',sans-serif;">Kesalahan Server</h2>
        <p style="color:rgba(255,255,255,0.6);font-size:0.95rem;max-width:420px;margin:0 auto 2rem;">
            Terjadi kesalahan pada server kami. Tim teknis sudah dinotifikasi.
            Silakan coba lagi dalam beberapa saat.
        </p>
        <a href="{{ url('/') }}" class="btn-home">
            <i class="bi bi-arrow-repeat me-2"></i>Coba Lagi
        </a>
    </div>
</body>
</html>
