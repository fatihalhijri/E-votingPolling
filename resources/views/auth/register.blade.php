<x-guest-layout>
    {{-- Judul form --}}
    <h5 class="mb-1" style="font-family: 'Poppins', sans-serif;">Buat Akun Mahasiswa</h5>
    <p class="text-muted mb-4" style="font-size: 0.88rem;">Daftarkan diri Anda untuk mulai berpartisipasi.</p>

    {{-- Pesan error validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger py-2 mb-3" role="alert" style="font-size: 0.88rem;">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Nama Lengkap --}}
        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-person" style="color: var(--evote-navy);"></i>
                </span>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="form-control border-start-0 @error('name') is-invalid @enderror"
                    placeholder="Nama sesuai KTM"
                    required
                    autofocus
                    autocomplete="name"
                >
            </div>
            @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email Kampus</label>
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
                    autocomplete="username"
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
                    placeholder="Minimal 8 karakter"
                    required
                    autocomplete="new-password"
                >
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-lock-fill" style="color: var(--evote-navy);"></i>
                </span>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    class="form-control border-start-0 @error('password_confirmation') is-invalid @enderror"
                    placeholder="Ulangi password"
                    required
                    autocomplete="new-password"
                >
            </div>
            @error('password_confirmation')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        {{-- Tombol Daftar --}}
        <button type="submit" class="btn btn-evote-primary mb-3">
            <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
        </button>

        {{-- Link sudah punya akun --}}
        <div class="text-center" style="font-size: 0.85rem;">
            <a href="{{ route('login') }}" class="auth-link">
                Sudah punya akun? <strong>Masuk di sini</strong>
            </a>
        </div>
    </form>
</x-guest-layout>
