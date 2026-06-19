<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJadwalKegiatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', Rule::in(['kuliah', 'tugas', 'uts', 'uas', 'organisasi'])],
            'waktu_pelaksanaan' => ['required', 'date'],
            'lokasi_atau_link' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['pending', 'selesai', 'dibatalkan'])],
            'prioritas' => ['required', Rule::in(['rendah', 'sedang', 'tinggi'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'judul' => 'judul',
            'kategori' => 'kategori',
            'waktu_pelaksanaan' => 'waktu pelaksanaan',
            'lokasi_atau_link' => 'lokasi atau link',
            'deskripsi' => 'deskripsi',
            'status' => 'status',
            'prioritas' => 'prioritas',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute tidak boleh lebih dari :max karakter.',
            'date' => ':attribute harus berupa tanggal dan waktu yang valid.',
            'in' => ':attribute yang dipilih tidak valid.',
            'string' => ':attribute harus berupa teks.',
        ];
    }
}
