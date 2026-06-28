<x-guest-layout>
    {{-- Judul form --}}
    <h5 class="mb-1" style="font-family: 'Poppins', sans-serif;">Masuk ke Akun Anda</h5>
    <p class="text-muted mb-4" style="font-size: 0.88rem;">Gunakan email dan password yang terdaftar.</p>

    {{-- Pesan status (misal: setelah reset password) --}}
    @if (session('status'))
        <div class="alert alert-success py-2 mb-3" role="alert" style="font-size: 0.88rem;">
            {{ session('status') }}
        </div>
    @endif

    {{-- Pesan error validasi global --}}
    @if ($errors->any())
        <div class="alert alert-danger py-2 mb-3" role="alert" style="font-size: 0.88rem;">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-envelope" style="color: var(--evote-navy);"></i>
                </span>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control border-start-0 @error('email') is-invalid @enderror"
                    placeholder="nama@kampus.ac.id"
                    required
                    autofocus
                    autocomplete="username"
                    style="border-left: none;"
                >
            </div>
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-lock" style="color: var(--evote-navy);"></i>
                </span>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-control border-start-0 @error('password') is-invalid @enderror"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        {{-- Ingat saya --}}
        <div class="mb-3 form-check">
            <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
            <label for="remember_me" class="form-check-label" style="font-size: 0.88rem;">
                Ingat saya di perangkat ini
            </label>
        </div>

        {{-- Tombol Login --}}
        <button type="submit" class="btn btn-evote-primary mb-3">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
        </button>

        {{-- Link lupa password & daftar --}}
        <div class="d-flex justify-content-between align-items-center" style="font-size: 0.85rem;">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link">
                    Lupa password?
                </a>
            @endif
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="auth-link">
                    Belum punya akun? <strong>Daftar</strong>
                </a>
            @endif
        </div>
    </form>
</x-guest-layout>
