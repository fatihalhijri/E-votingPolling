<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk validasi saat membuat/mengupdate kandidat.
 *
 * LOKASI FILE: app/Http/Requests/StoreCandidateRequest.php
 */
class StoreCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kandidat' => 'required|string|max:255',

            // 'integer' = harus angka bulat, 'min:1' = minimal 1
            'nomor_urut'    => 'required|integer|min:1',

            'visi_misi'     => 'nullable|string',

            // Validasi file foto:
            // 'nullable' = boleh tidak upload foto
            // 'image'    = harus file gambar (jpg, jpeg, png, gif, svg, webp)
            // 'mimes'    = hanya ekstensi ini yang diizinkan (lebih spesifik dari 'image')
            // 'max:2048' = maksimal 2MB (2048 KB)
            //
            // KENAPA validasi file di sini penting?
            // Mencegah user upload file berbahaya (misal: .php disguised sebagai gambar).
            // Laravel otomatis cek MIME type asli file, bukan hanya ekstensinya.
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kandidat.required' => 'Nama kandidat wajib diisi.',
            'nama_kandidat.max'      => 'Nama kandidat maksimal 255 karakter.',
            'nomor_urut.required'    => 'Nomor urut kandidat wajib diisi.',
            'nomor_urut.integer'     => 'Nomor urut harus berupa angka.',
            'nomor_urut.min'         => 'Nomor urut minimal 1.',
            'foto.image'             => 'File harus berupa gambar.',
            'foto.mimes'             => 'Format foto harus jpg, jpeg, png, atau webp.',
            'foto.max'               => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
