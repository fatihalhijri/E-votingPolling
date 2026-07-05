<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                 style="width:44px;height:44px;background:var(--evote-navy);color:var(--evote-gold);font-size:1.1rem;font-family:'Poppins',sans-serif;flex-shrink:0;">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div>
                <h5 class="mb-0 fw-bold" style="color:var(--evote-navy);">Pengaturan Akun</h5>
                <small class="text-muted">{{ Auth::user()->email }}</small>
            </div>
        </div>
    </x-slot>

    {{-- =====================================================================
         HALAMAN PROFIL MAHASISWA
         Berisi: update info profil, ganti password, hapus akun
    ===================================================================== --}}

    <div class="d-flex flex-column gap-4">

        {{-- ===== KARTU INFO PROFIL ===== --}}
        <div class="evote-card p-4">
            <h6 class="fw-bold mb-3 pb-2 border-bottom" style="color:var(--evote-navy);">
                <i class="bi bi-person-circle me-2"></i>Informasi Profil
            </h6>

            {{-- Tampilkan error validasi --}}
            @if ($errors->userForm->any())
                <div class="alert alert-danger py-2 mb-3" style="font-size:0.85rem;">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->userForm->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Pesan sukses update --}}
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success py-2 mb-3" style="font-size:0.85rem;">
                    <i class="bi bi-check-circle me-2"></i>Profil berhasil diperbarui.
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf @method('patch')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}"
                               class="form-control @error('name') is-invalid @enderror"
                               required autocomplete="name">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                               class="form-control @error('email') is-invalid @enderror"
                               required autocomplete="email">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Field NIM (read-only, tidak bisa diubah) --}}
                    @if(Auth::user()->nim)
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">NIM</label>
                        <input type="text" value="{{ Auth::user()->nim }}"
                               class="form-control bg-light" readonly
                               title="NIM tidak dapat diubah">
                        <div class="form-text" style="font-size:0.75rem;">NIM tidak dapat diubah.</div>
                    </div>
                    @endif
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-evote-primary">
                        <i class="bi bi-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- ===== GANTI PASSWORD ===== --}}
        <div class="evote-card p-4">
            <h6 class="fw-bold mb-3 pb-2 border-bottom" style="color:var(--evote-navy);">
                <i class="bi bi-lock me-2"></i>Ganti Password
            </h6>

            @if (session('status') === 'password-updated')
                <div class="alert alert-success py-2 mb-3" style="font-size:0.85rem;">
                    <i class="bi bi-check-circle me-2"></i>Password berhasil diperbarui.
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf @method('put')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">Password Saat Ini</label>
                        <input type="password" name="current_password"
                               class="form-control @error('current_password','updatePassword') is-invalid @enderror"
                               autocomplete="current-password">
                        @error('current_password','updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">Password Baru</label>
                        <input type="password" name="password"
                               class="form-control @error('password','updatePassword') is-invalid @enderror"
                               autocomplete="new-password">
                        @error('password','updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation"
                               class="form-control"
                               autocomplete="new-password">
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-evote-primary">
                        <i class="bi bi-shield-lock me-2"></i>Perbarui Password
                    </button>
                </div>
            </form>
        </div>

        {{-- ===== HAPUS AKUN (DANGER ZONE) ===== --}}
        <div class="evote-card p-4" style="border-color:var(--evote-red);">
            <h6 class="fw-bold mb-1 pb-2 border-bottom" style="color:var(--evote-red);">
                <i class="bi bi-exclamation-triangle me-2"></i>Hapus Akun
            </h6>
            <p class="text-muted mb-3" style="font-size:0.85rem;">
                Setelah akun dihapus, semua data tidak dapat dikembalikan. Harap pastikan Anda yakin.
            </p>

            {{-- Tombol trigger modal konfirmasi --}}
            <button type="button" class="btn btn-danger btn-sm"
                    data-bs-toggle="modal" data-bs-target="#modalHapusAkun">
                <i class="bi bi-trash me-2"></i>Hapus Akun Saya
            </button>

            {{-- Modal konfirmasi hapus akun --}}
            <div class="modal fade" id="modalHapusAkun" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius:16px;border:none;overflow:hidden;">
                        <div class="modal-header" style="background:var(--evote-red);color:#fff;border:none;">
                            <h6 class="modal-title fw-bold">
                                <i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus Akun
                            </h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <p style="font-size:0.9rem;">
                                Masukkan password Anda untuk mengkonfirmasi penghapusan akun.
                                Tindakan ini <strong>tidak dapat dibatalkan</strong>.
                            </p>
                            <form method="POST" action="{{ route('profile.destroy') }}" id="formHapusAkun">
                                @csrf @method('delete')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Password</label>
                                    <input type="password" name="password"
                                           class="form-control @error('password','userDeletion') is-invalid @enderror"
                                           placeholder="Masukkan password Anda"
                                           autocomplete="current-password">
                                    @error('password','userDeletion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary flex-fill"
                                            data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger flex-fill">
                                        Ya, Hapus Akun
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
