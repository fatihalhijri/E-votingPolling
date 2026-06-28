<x-admin-layout>
    <x-slot name="title">Edit Kandidat</x-slot>
    <x-slot name="breadcrumb">Edit Kandidat</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('admin.polls.candidates.index', $candidate->poll) }}"
                   class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-0">Edit Kandidat</h4>
                    <p class="text-muted mb-0" style="font-size:0.85rem;">
                        Mengubah: <strong>{{ $candidate->nama_kandidat }}</strong>
                    </p>
                </div>
            </div>

            <div class="evote-card p-4">
                {{-- route('candidates.edit') adalah shallow route — tidak perlu {poll} dalam URL --}}
                <form method="POST"
                      action="{{ route('admin.candidates.update', $candidate) }}"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label for="nama_kandidat" class="form-label fw-semibold">
                                Nama Kandidat <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="nama_kandidat"
                                   name="nama_kandidat"
                                   value="{{ old('nama_kandidat', $candidate->nama_kandidat) }}"
                                   class="form-control @error('nama_kandidat') is-invalid @enderror"
                                   required>
                            @error('nama_kandidat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="nomor_urut" class="form-label fw-semibold">
                                No. Urut <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   id="nomor_urut"
                                   name="nomor_urut"
                                   value="{{ old('nomor_urut', $candidate->nomor_urut) }}"
                                   class="form-control @error('nomor_urut') is-invalid @enderror"
                                   min="1" required>
                            @error('nomor_urut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="visi_misi" class="form-label fw-semibold">Visi & Misi</label>
                        <textarea id="visi_misi" name="visi_misi" rows="6"
                                  class="form-control @error('visi_misi') is-invalid @enderror">{{ old('visi_misi', $candidate->visi_misi) }}</textarea>
                        @error('visi_misi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Foto: tampilkan yang ada + opsi ganti --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Foto Kandidat
                            <span class="text-muted fw-normal">(kosongkan jika tidak ingin mengganti)</span>
                        </label>

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img id="fotoPreview"
                                 src="{{ $candidate->urlFoto() }}"
                                 alt="Foto {{ $candidate->nama_kandidat }}"
                                 style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:2px solid var(--evote-border);">
                            <div style="font-size:0.82rem;color:var(--evote-text-muted);">
                                @if($candidate->foto)
                                    Foto saat ini ditampilkan di sebelah kiri.
                                    Upload baru untuk menggantikannya.
                                @else
                                    Kandidat belum punya foto. Upload untuk menambahkan.
                                @endif
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
                            <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.polls.candidates.index', $candidate->poll) }}"
                           class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewFoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => document.getElementById('fotoPreview').src = e.target.result;
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
</x-admin-layout>
