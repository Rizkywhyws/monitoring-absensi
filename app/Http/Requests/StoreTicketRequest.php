<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:30'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,png,jpg,jpeg', 'max:10240'], // 10 MB
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul pekerjaan wajib diisi.',
            'title.max' => 'Judul pekerjaan maksimal 100 karakter.',
            'description.required' => 'Deskripsi pekerjaan wajib diisi.',
            'description.min' => 'Deskripsi pekerjaan minimal 30 karakter.',
            'attachments.*.file' => 'Lampiran harus berupa file.',
            'attachments.*.mimes' => 'Lampiran harus berformat PDF, PNG, atau JPG.',
            'attachments.*.max' => 'Ukuran setiap lampiran maksimal 10 MB.',
            'attachments.*.uploaded' => 'Lampiran gagal diunggah. Pastikan ukuran file tidak melebihi 10 MB.',
        ];
    }
}