<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'full_address' => 'required|string|max:500',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'is_default' => 'nullable|boolean',
            'notes' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'label.required' => 'Label alamat wajib diisi.',
            'label.max' => 'Label alamat maksimal 50 karakter.',
            'recipient_name.required' => 'Nama penerima wajib diisi.',
            'recipient_name.max' => 'Nama penerima maksimal 255 karakter.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'full_address.required' => 'Alamat lengkap wajib diisi.',
            'full_address.max' => 'Alamat lengkap maksimal 500 karakter.',
            'province.required' => 'Provinsi wajib diisi.',
            'province.max' => 'Provinsi maksimal 100 karakter.',
            'city.required' => 'Kota wajib diisi.',
            'city.max' => 'Kota maksimal 100 karakter.',
            'district.max' => 'Kecamatan maksimal 100 karakter.',
            'postal_code.max' => 'Kode pos maksimal 10 karakter.',
            'notes.max' => 'Catatan maksimal 255 karakter.',
        ];
    }
}
