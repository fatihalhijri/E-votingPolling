<x-admin-layout>
    <x-slot name="title">Tambah Kandidat</x-slot>
    <x-slot name="breadcrumb">Tambah Kandidat</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('admin.polls.candidates.index', $poll) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-0">Tambah Kandidat</h4>
                    <p class="text-muted mb-0" style="font-size:0.85rem;">
                        Polling: <strong>{{ $poll->judul }}</strong>
                    </p>
                </div>
            </div>

            <div class="evote-card p-4">
                {{-- enctype="multipart/form-data" WAJIB ada jika form punya upload file --}}
                <form method="POST"
                      action="{{ route('admin.polls.candidates.store', $poll) }}"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3 mb-4">
                        {{-- Nama Kandidat --}}
                        <div class="col-md-8">
                            <label for="nama_kandidat" class="form-label fw-semibold">
                                Nama Kandidat <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="nama_kandidat"
                                   name="nama_kandidat"
                                   value="{{ old('nama_kandidat') }}"
                                   class="form-control @error('nama_kandidat') is-invalid @enderror"
                                   placeholder="Nama lengkap kandidat"
                                   required>
                            @error('nama_kandidat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nomor Urut --}}
                        <div class="col-md-4">
                            <label for="nomor_urut" class="form-label fw-semibold">
                                No. Urut <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   id="nomor_urut"
                                   name="nomor_urut"
                                   value="{{ old('nomor_urut', $poll->candidates()->count() + 1) }}"
                                   class="form-control @error('nomor_urut') is-invalid @enderror"
                                   min="1" required>
                            @error('nomor_urut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Visi Misi --}}
                    <div class="mb-4">
                        <label for="visi_misi" class="form-label fw-semibold">Visi & Misi</label>
                        <textarea id="visi_misi"
                                  name="visi_misi"
                                  rows="6"
                                  class="form-control @error('visi_misi') is-invalid @enderror"
                                  placeholder="Tuliskan visi dan misi kandidat di sini...">{{ old('visi_misi') }}</textarea>
                        @error('visi_misi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Upload Foto --}}
                    <div class="mb-4">
                        <label for="foto" class="form-label fw-semibold">
                            Foto Kandidat
                            <span class="text-muted fw-normal">(opsional, maks. 2MB, format: jpg/png/webp)</span>
                        </label>

                        {{-- Preview foto sebelum upload --}}
                        <div class="mb-3 d-flex align-items-center gap-3">
                            <img id="fotoPreview"
                                 src="https://ui-avatars.com/api/?name=?&background=E2E6EC&color=6B7280&size=80"
                                 alt="Preview"
                                 style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:2px solid var(--evote-border);">
                            <div>
                                <div style="font-size:0.82rem;color:var(--evote-text-muted);">
                                    Jika tidak upload foto, sistem akan menampilkan avatar otomatis.
                                </div>
                            </div>
                        </div>

                        <input type="file"
                               id="foto"
                               name="foto"
                               accept="image/jpg,image/jpeg,image/png,image/webp"
                               class="form-control @error('foto') is-invalid @enderror"
                               onchange="previewFoto(this)">
                        @error('foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-evote-primary px-4">
                            <i class="bi bi-person-check me-2"></i>Simpan Kandidat
                        </button>
                        <a href="{{ route('admin.polls.candidates.index', $poll) }}"
                           class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Preview foto sebelum upload — feedback visual langsung untuk admin
        function previewFoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('fotoPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
</x-admin-layout>
