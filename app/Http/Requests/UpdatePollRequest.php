<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk validasi saat MENGUPDATE polling (method update).
 * Sama dengan StorePollRequest — dipisah agar masing-masing bisa
 * dimodifikasi independen di masa depan jika ada kebutuhan berbeda.
 *
 * LOKASI FILE: app/Http/Requests/UpdatePollRequest.php
 */
class UpdatePollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul'        => 'required|string|max:255',
            'deskripsi'    => 'nullable|string',
            'status'       => 'required|in:draft,aktif,selesai',
            'mulai_pada'   => 'required|date',
            'selesai_pada' => 'required|date|after:mulai_pada',
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required'        => 'Judul polling wajib diisi.',
            'judul.max'             => 'Judul polling maksimal 255 karakter.',
            'status.required'       => 'Status polling wajib dipilih.',
            'status.in'             => 'Status polling tidak valid.',
            'mulai_pada.required'   => 'Tanggal mulai wajib diisi.',
            'mulai_pada.date'       => 'Format tanggal mulai tidak valid.',
            'selesai_pada.required' => 'Tanggal selesai wajib diisi.',
            'selesai_pada.date'     => 'Format tanggal selesai tidak valid.',
            'selesai_pada.after'    => 'Tanggal selesai harus SETELAH tanggal mulai.',
        ];
    }
}
