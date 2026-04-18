<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
      'name' => 'required|string|max:255',
      'email' => [
        'required',
        'email',
        Rule::unique('users')->ignore(auth()->id()),
      ],
      'phone' => 'nullable|string|max:20',
      'address' => 'nullable|string|max:500',
      'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
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
      'name.required' => 'Nama wajib diisi.',
      'name.max' => 'Nama maksimal 255 karakter.',
      'email.required' => 'Email wajib diisi.',
      'email.email' => 'Format email tidak valid.',
      'email.unique' => 'Email sudah digunakan.',
      'phone.max' => 'Nomor telepon maksimal 20 karakter.',
      'address.max' => 'Alamat maksimal 500 karakter.',
      'profile_photo.image' => 'Foto profil harus berupa gambar.',
      'profile_photo.mimes' => 'Foto profil harus berformat JPG, JPEG, PNG, atau WEBP.',
      'profile_photo.max' => 'Ukuran foto profil maksimal 2 MB.',
    ];
  }
}
