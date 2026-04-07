<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->is_admin;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (empty($this->slug) && $this->name) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products')->ignore($this->product),
            ],
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'min_order' => 'nullable|integer|min:1',
            'max_order' => 'nullable|integer|min:1|gt:min_order',
            'sku' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products')->ignore($this->product),
            ],
            'unit' => 'nullable|string|max:20',
            'weight' => 'nullable|numeric|min:0',
            'images' => 'nullable|array',
            'images.*' => 'url',
            'featured_image' => 'nullable|url',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
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
            'discount_price.lt' => 'Harga diskon harus lebih rendah dari harga normal.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
        ];
    }
}
