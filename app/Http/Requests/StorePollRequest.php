<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk validasi saat MEMBUAT polling baru (method store).
 *
 * LOKASI FILE: app/Http/Requests/StorePollRequest.php
 *
 * KENAPA pakai Form Request, bukan $request->validate() di controller?
 * Dengan Form Request:
 * 1. Controller lebih bersih — tidak ada kode validasi panjang di dalamnya
 * 2. Validasi bisa di-reuse kalau ada controller lain yang butuh hal sama
 * 3. Pesan error bisa diatur di satu tempat (method messages())
 * 4. Bisa tambah logika otorisasi (authorize()) untuk keamanan ekstra
 */
class StorePollRequest extends FormRequest
{
    /**
     * Apakah user yang mengirim request ini diizinkan?
     *
     * Kita return true karena otorisasi sudah ditangani oleh middleware 'admin'.
     * Tapi kalau mau, kamu bisa tambah: return $this->user()->isAdmin();
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk setiap field form.
     *
     * Format: 'nama_field' => 'rule1|rule2|rule3'
     * Atau array: 'nama_field' => ['rule1', 'rule2']
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'required' = wajib diisi, 'string' = harus teks, 'max:255' = max 255 karakter
            'judul'       => 'required|string|max:255',

            // 'nullable' = boleh kosong, 'string' = kalau diisi harus teks
            'deskripsi'   => 'nullable|string',

            // 'required' = wajib, 'in:...' = hanya nilai yang disebutkan yang valid
            'status'      => 'required|in:draft,aktif,selesai',

            // 'date' = harus format tanggal/datetime yang valid
            'mulai_pada'  => 'required|date',

            // 'after:mulai_pada' = HARUS setelah nilai 'mulai_pada'
            // Ini adalah contoh custom validation rule bawaan Laravel yang sangat berguna!
            // Kenapa penting? Mencegah admin iseng isi tanggal selesai sebelum mulai,
            // yang akan membuat polling tidak bisa diakses sama sekali.
            'selesai_pada' => 'required|date|after:mulai_pada',
        ];
    }

    /**
     * Pesan error kustom dalam Bahasa Indonesia.
     *
     * Tanpa ini, Laravel akan tampilkan pesan error dalam Bahasa Inggris
     * yang mungkin membingungkan (misal: "The selesai_pada must be after mulai_pada").
     */
    public function messages(): array
    {
        return [
            'judul.required'           => 'Judul polling wajib diisi.',
            'judul.max'                => 'Judul polling maksimal 255 karakter.',
            'status.required'          => 'Status polling wajib dipilih.',
            'status.in'                => 'Status polling tidak valid.',
            'mulai_pada.required'      => 'Tanggal mulai wajib diisi.',
            'mulai_pada.date'          => 'Format tanggal mulai tidak valid.',
            'selesai_pada.required'    => 'Tanggal selesai wajib diisi.',
            'selesai_pada.date'        => 'Format tanggal selesai tidak valid.',
            'selesai_pada.after'       => 'Tanggal selesai harus SETELAH tanggal mulai.',
        ];
    }
}
