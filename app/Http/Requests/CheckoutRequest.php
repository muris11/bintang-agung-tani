<?php

namespace App\Http\Requests;

use App\DTOs\CreateOrderData;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
            'shipping_address' => 'required_without:address_id|string|max:500',
            'shipping_phone' => 'required_without:address_id|string|max:20',
            'notes' => 'nullable|string|max:500',
            'address_id' => 'nullable|exists:addresses,id',
            'shipping_cost' => 'nullable|numeric|min:0',
            'shipping_courier' => 'nullable|string|max:50',
            'shipping_service' => 'nullable|string|max:100',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'shipping_address.required' => 'Alamat pengiriman harus diisi.',
            'shipping_address.string' => 'Alamat pengiriman harus berupa teks.',
            'shipping_address.max' => 'Alamat pengiriman maksimal 500 karakter.',
            'shipping_phone.required' => 'Nomor telepon pengiriman harus diisi.',
            'shipping_phone.string' => 'Nomor telepon pengiriman harus berupa teks.',
            'shipping_phone.max' => 'Nomor telepon pengiriman maksimal 20 karakter.',
            'notes.string' => 'Catatan harus berupa teks.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
            'address_id.exists' => 'Alamat yang dipilih tidak valid.',
        ];
    }

    /**
     * Convert to DTO
     */
    public function toDto(): CreateOrderData
    {
        return CreateOrderData::fromRequest(
            data: $this->validated(),
            userId: auth()->id()
        );
    }
}
