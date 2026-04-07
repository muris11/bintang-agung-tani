@extends('layouts.admin')

@section('title', 'Edit Metode Pembayaran')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i></div></li>
                    <li><a href="{{ route('admin.payment-methods.index') }}" class="hover:text-primary-600 transition-colors">Metode Pembayaran</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i></div></li>
                    <li class="text-gray-900 font-medium">Edit Metode</li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Edit Metode Pembayaran</h1>
            <p class="text-sm text-gray-500 mt-1">Ubah informasi metode pembayaran <strong>{{ $paymentMethod->name }}</strong>.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.payment-methods.index') }}" class="btn-secondary flex items-center gap-2 h-10 px-4">
                <i class="ph ph-arrow-left w-4 h-4"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card p-0 overflow-hidden border-primary-100">
        <div class="bg-gradient-to-r from-primary-50/40 to-primary-50/10 px-6 py-4 border-b border-primary-100 flex items-center gap-2">
            <i class="ph ph-pencil-simple w-5 h-5 text-primary-600 ph-fill"></i>
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Form Edit Metode Pembayaran</h2>
        </div>
        
        <form action="{{ route('admin.payment-methods.update', $paymentMethod) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Metode -->
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-semibold text-gray-900">
                        Nama Metode <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" 
                           class="form-input w-full @error('name') border-red-500 @enderror" 
                           value="{{ old('name', $paymentMethod->name) }}" required placeholder="Contoh: Transfer Bank BCA">
                    @error('name')
                        <p class="text-red-600 text-sm flex items-center gap-1">
                            <i class="ph ph-warning-circle w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Nama Bank -->
                <div class="space-y-2">
                    <label for="bank_name" class="block text-sm font-semibold text-gray-900">
                        Nama Bank <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="bank_name" id="bank_name" 
                           class="form-input w-full @error('bank_name') border-red-500 @enderror" 
                           value="{{ old('bank_name', $paymentMethod->bank_name) }}" required placeholder="Contoh: BCA">
                    @error('bank_name')
                        <p class="text-red-600 text-sm flex items-center gap-1">
                            <i class="ph ph-warning-circle w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Nomor Rekening -->
                <div class="space-y-2">
                    <label for="account_number" class="block text-sm font-semibold text-gray-900">
                        Nomor Rekening <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="account_number" id="account_number" 
                           class="form-input w-full @error('account_number') border-red-500 @enderror font-mono" 
                           value="{{ old('account_number', $paymentMethod->account_number) }}" required placeholder="1234567890">
                    @error('account_number')
                        <p class="text-red-600 text-sm flex items-center gap-1">
                            <i class="ph ph-warning-circle w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Nama Pemilik Rekening -->
                <div class="space-y-2">
                    <label for="account_name" class="block text-sm font-semibold text-gray-900">
                        Nama Pemilik Rekening <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="account_name" id="account_name" 
                           class="form-input w-full @error('account_name') border-red-500 @enderror" 
                           value="{{ old('account_name', $paymentMethod->account_name) }}" required placeholder="PT Bintang Agung">
                    @error('account_name')
                        <p class="text-red-600 text-sm flex items-center gap-1">
                            <i class="ph ph-warning-circle w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Logo Bank -->
                <div class="space-y-2">
                    <label for="logo" class="block text-sm font-semibold text-gray-900">
                        Logo Bank
                    </label>
                    @if($paymentMethod->logo)
                        <div class="mb-3 p-4 bg-gray-50 rounded-xl border border-gray-200 inline-block">
                            <img src="{{ Storage::url($paymentMethod->logo) }}" alt="Logo {{ $paymentMethod->bank_name }}" class="h-12 object-contain">
                            <p class="text-xs text-gray-500 mt-2 text-center">Logo saat ini</p>
                        </div>
                    @endif
                    <input type="file" name="logo" id="logo" 
                           class="form-input w-full @error('logo') border-red-500 @enderror" 
                           accept="image/*">
                    <p class="text-xs text-gray-500">Kosongkan jika tidak ingin mengubah logo. Format: JPG, PNG, SVG. Max: 2MB</p>
                    @error('logo')
                        <p class="text-red-600 text-sm flex items-center gap-1">
                            <i class="ph ph-warning-circle w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Urutan -->
                <div class="space-y-2">
                    <label for="sort_order" class="block text-sm font-semibold text-gray-900">
                        Urutan
                    </label>
                    <input type="number" name="sort_order" id="sort_order" 
                           class="form-input w-full @error('sort_order') border-red-500 @enderror" 
                           value="{{ old('sort_order', $paymentMethod->sort_order) }}" min="0" placeholder="0">
                    <p class="text-xs text-gray-500">Semakin kecil angka, semakin atas posisinya</p>
                    @error('sort_order')
                        <p class="text-red-600 text-sm flex items-center gap-1">
                            <i class="ph ph-warning-circle w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Instruksi Pembayaran -->
                <div class="space-y-2 md:col-span-2">
                    <label for="instructions" class="block text-sm font-semibold text-gray-900">
                        Instruksi Pembayaran
                    </label>
                    <textarea name="instructions" id="instructions" rows="4" 
                              class="form-input w-full @error('instructions') border-red-500 @enderror" 
                              placeholder="Masukkan instruksi pembayaran untuk pelanggan...">{{ old('instructions', $paymentMethod->instructions) }}</textarea>
                    @error('instructions')
                        <p class="text-red-600 text-sm flex items-center gap-1">
                            <i class="ph ph-warning-circle w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Status Aktif -->
                <div class="space-y-2 md:col-span-2">
                    <label class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="is_active" value="1" class="w-5 h-5 text-primary-600 rounded border-gray-300 focus:ring-primary-500" {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }}>
                        <div>
                            <span class="font-semibold text-gray-900 block">Aktifkan Metode Pembayaran</span>
                            <span class="text-sm text-gray-500">Metode pembayaran akan tersedia untuk pelanggan</span>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="submit" class="btn-primary flex items-center justify-center gap-2 h-12 px-6 shadow-lg">
                    <i class="ph ph-floppy-disk w-5 h-5"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.payment-methods.index') }}" class="btn-secondary flex items-center justify-center gap-2 h-12 px-6">
                    <i class="ph ph-x-circle w-5 h-5"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection