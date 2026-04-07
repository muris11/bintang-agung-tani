<?php

namespace App\Http\Requests;

use App\DTOs\UpdateOrderStatusData;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|string',
            'notes' => 'nullable|string|max:500',
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
            'status.required' => 'Status pesanan harus diisi.',
            'status.string' => 'Status pesanan harus berupa teks.',
            'notes.string' => 'Catatan harus berupa teks.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ];
    }

    /**
     * Convert to DTO
     */
    public function toDto(): UpdateOrderStatusData
    {
        return UpdateOrderStatusData::fromRequest(
            data: $this->validated(),
            changedBy: auth()->id()
        );
    }
}
