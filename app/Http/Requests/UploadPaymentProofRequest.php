<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadPaymentProofRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && ! auth()->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'proof_image' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom error messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'proof_image.required' => 'Bukti pembayaran wajib diunggah.',
            'proof_image.image' => 'File harus berupa gambar.',
            'proof_image.mimes' => 'Format file harus berupa jpeg, png, atau jpg.',
            'proof_image.max' => 'Ukuran file maksimal 5MB.',
            'notes.max' => 'Catatan tidak boleh lebih dari 500 karakter.',
        ];
    }
}
