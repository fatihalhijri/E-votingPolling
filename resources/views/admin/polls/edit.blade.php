<x-admin-layout>
    <x-slot name="title">Edit Polling</x-slot>
    <x-slot name="breadcrumb">Edit Polling</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('admin.polls.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-0">Edit Polling</h4>
                    <p class="text-muted mb-0" style="font-size:0.85rem;">
                        Mengubah: <strong>{{ $poll->judul }}</strong>
                    </p>
                </div>
            </div>

            <div class="evote-card p-4">
                {{-- Method spoofing: HTML form hanya kenal GET/POST.
                     Untuk PUT, kita pakai @method('PUT') agar Laravel
                     tahu ini adalah request UPDATE, bukan insert baru. --}}
                <form method="POST" action="{{ route('admin.polls.update', $poll) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="judul" class="form-label fw-semibold">
                            Judul Polling <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="judul"
                               name="judul"
                               value="{{ old('judul', $poll->judul) }}"
                               class="form-control @error('judul') is-invalid @enderror"
                               required>
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"
                                  class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $poll->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label fw-semibold">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select id="status" name="status"
                                class="form-select @error('status') is-invalid @enderror">
                            <option value="draft"   {{ old('status', $poll->status) === 'draft'   ? 'selected' : '' }}>Draft</option>
                            <option value="aktif"   {{ old('status', $poll->status) === 'aktif'   ? 'selected' : '' }}>Aktif</option>
                            <option value="selesai" {{ old('status', $poll->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="mulai_pada" class="form-label fw-semibold">
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local"
                                   id="mulai_pada"
                                   name="mulai_pada"
                                   value="{{ old('mulai_pada', $poll->mulai_pada->format('Y-m-d\TH:i')) }}"
                                   class="form-control @error('mulai_pada') is-invalid @enderror"
                                   required>
                            @error('mulai_pada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="selesai_pada" class="form-label fw-semibold">
                                Tanggal Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local"
                                   id="selesai_pada"
                                   name="selesai_pada"
                                   value="{{ old('selesai_pada', $poll->selesai_pada->format('Y-m-d\TH:i')) }}"
                                   class="form-control @error('selesai_pada') is-invalid @enderror"
                                   required>
                            @error('selesai_pada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Info: sudah berapa suara masuk --}}
                    @if($poll->votes_count ?? $poll->votes()->count() > 0)
                        <div class="alert alert-warning py-2 mb-4" style="font-size:0.83rem;">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Polling ini sudah memiliki <strong>{{ $poll->votes()->count() }} suara</strong>.
                            Hati-hati mengubah status jika voting sedang berlangsung.
                        </div>
                    @endif

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-evote-primary px-4">
                            <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.polls.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
