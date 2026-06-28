<x-admin-layout>
    <x-slot name="title">Buat Polling Baru</x-slot>
    <x-slot name="breadcrumb">Buat Polling Baru</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Header --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('admin.polls.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-0">Buat Polling Baru</h4>
                    <p class="text-muted mb-0" style="font-size:0.85rem;">Isi semua informasi polling dengan lengkap.</p>
                </div>
            </div>

            {{-- Form --}}
            <div class="evote-card p-4">
                <form method="POST" action="{{ route('admin.polls.store') }}" id="formBuatPolling">
                    @csrf

                    {{-- Judul Polling --}}
                    <div class="mb-4">
                        <label for="judul" class="form-label fw-semibold">
                            Judul Polling <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="judul"
                               name="judul"
                               value="{{ old('judul') }}"
                               class="form-control @error('judul') is-invalid @enderror"
                               placeholder="Contoh: Pemilihan Ketua BEM 2026"
                               required>
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-4">
                        <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                        <textarea id="deskripsi"
                                  name="deskripsi"
                                  rows="3"
                                  class="form-control @error('deskripsi') is-invalid @enderror"
                                  placeholder="Penjelasan singkat tentang polling ini (opsional)...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="mb-4">
                        <label for="status" class="form-label fw-semibold">
                            Status Awal <span class="text-danger">*</span>
                        </label>
                        <select id="status" name="status"
                                class="form-select @error('status') is-invalid @enderror">
                            <option value="draft"   {{ old('status', 'draft') === 'draft'   ? 'selected' : '' }}>
                                Draft — belum dibuka, mahasiswa tidak bisa vote
                            </option>
                            <option value="aktif"   {{ old('status') === 'aktif'   ? 'selected' : '' }}>
                                Aktif — langsung buka voting
                            </option>
                            <option value="selesai" {{ old('status') === 'selesai' ? 'selected' : '' }}>
                                Selesai — voting ditutup
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            💡 Disarankan: buat dulu sebagai <strong>Draft</strong>, tambahkan kandidat, baru ubah ke <strong>Aktif</strong>.
                        </div>
                    </div>

                    {{-- Periode Waktu --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="mulai_pada" class="form-label fw-semibold">
                                Tanggal & Waktu Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local"
                                   id="mulai_pada"
                                   name="mulai_pada"
                                   value="{{ old('mulai_pada', now()->format('Y-m-d\TH:i')) }}"
                                   class="form-control @error('mulai_pada') is-invalid @enderror"
                                   required>
                            @error('mulai_pada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="selesai_pada" class="form-label fw-semibold">
                                Tanggal & Waktu Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local"
                                   id="selesai_pada"
                                   name="selesai_pada"
                                   value="{{ old('selesai_pada', now()->addDays(7)->format('Y-m-d\TH:i')) }}"
                                   class="form-control @error('selesai_pada') is-invalid @enderror"
                                   required>
                            @error('selesai_pada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Catatan validasi selesai_pada --}}
                    <div class="alert alert-info py-2 mb-4" style="font-size:0.83rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        Tanggal selesai harus <strong>setelah</strong> tanggal mulai. Sistem akan menolak otomatis jika tidak sesuai.
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-evote-primary px-4">
                            <i class="bi bi-check-lg me-2"></i>Simpan Polling
                        </button>
                        <a href="{{ route('admin.polls.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
