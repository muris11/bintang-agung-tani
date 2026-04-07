@extends('layouts.admin')

@section('title', 'Tambah Kategori Baru')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a></li>
                    <li>
                        <div class="flex items-center">
                            <i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i>
                            <a href="/admin/kategori" class="hover:text-primary-600 transition-colors">Kelola Kategori</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i>
                            <span class="text-gray-900 font-medium">Tambah Baru</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Tambah Kategori Baru</h1>
            <p class="text-sm text-gray-500 mt-1">Buat kategori produk baru untuk katalog toko.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-primary-50/50 to-transparent">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center">
                    <i class="ph ph-plus ph-duotone w-6 h-6"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Informasi Kategori</h2>
                    <p class="text-sm text-gray-500">Isi detail kategori baru</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.kategori.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Nama Kategori -->
            <div>
                <label for="name" class="form-label block mb-2">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name') }}"
                       placeholder="Contoh: Herbisida"
                       class="form-input w-full @error('name') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ikon Kategori - Dropdown -->
            <div x-data="{ open: false, selected: '{{ old('icon', 'ph-tag') }}', search: '' }" 
                 @click.away="open = false">
                <label for="icon" class="form-label block mb-2">
                    Ikon Kategori <span class="text-gray-400 font-normal ml-1">(Opsional)</span>
                </label>
                
                <div class="relative">
                    <button type="button" 
                            @click="open = !open"
                            class="form-input w-full flex items-center gap-3 text-left bg-white hover:bg-gray-50 transition-colors @error('icon') border-red-500 @enderror">
                        <div class="w-10 h-10 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                            <i :class="'ph ' + selected + ' ph-duotone w-5 h-5'"></i>
                        </div>
                        <span class="flex-1 text-gray-700" x-text="selected || 'Pilih ikon'"></span>
                        <i class="ph ph-caret-down text-gray-400"></i>
                    </button>
                    
<div x-show="open" x-cloak
                          x-transition:enter="transition ease-out duration-200"
                          x-transition:enter-start="opacity-0 translate-y-1"
                          x-transition:enter-end="opacity-100 translate-y-0"
                          x-transition:leave="transition ease-in duration-150"
                          x-transition:leave-start="opacity-100 translate-y-0"
                          x-transition:leave-end="opacity-0 translate-y-1"
                          class="absolute z-50 mt-2 w-full bg-white rounded-xl shadow-xl border border-gray-200 max-h-80 overflow-hidden">
                        
                        <div class="p-3 border-b border-gray-100 bg-gray-50">
                        <!-- Search Box -->
                        <div class="p-3 border-b border-gray-100 bg-gray-50">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="ph ph-magnifying-glass w-5 h-5 text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       x-model="search"
                                       placeholder="Cari ikon..."
                                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
                            </div>
                        </div>
                        </div>
                        
                        <div class="overflow-y-auto max-h-64 p-3">
                            <div class="grid grid-cols-6 gap-2">
                                @php
                                    $icons = [
                                        'ph-plant', 'ph-seedling', 'ph-tree', 'ph-flower', 'ph-carrot', 'ph-apple',
                                        'ph-orange', 'ph-lemon', 'ph-corn', 'ph-grains', 'ph-grains-slash', 'ph-bowl',
                                        'ph-shopping-cart', 'ph-shopping-bag', 'ph-storefront', 'ph-package', 'ph-truck', 'ph-scales',
                                        'ph-drop', 'ph-drop-half', 'ph-sun', 'ph-cloud-sun', 'ph-cloud-rain', 'ph-thermometer',
                                        'ph-flask', 'ph-test-tube', 'ph-microscope', 'ph-atom', 'ph-molecule', 'ph-dna',
                                        'ph-bug', 'ph-butterfly', 'ph-spider', 'ph-ant', 'ph-egg', 'ph-fish',
                                        'ph-paw', 'ph-cow', 'ph-horse', 'ph-chicken', 'ph-pig', 'ph-sheep',
                                        'ph-house', 'ph-barn', 'ph-fence', 'ph-shovel', 'ph-rake', 'ph-watering-can',
                                        'ph-sprout', 'ph-leaf', 'ph-planting', 'ph-harvest', 'ph-wheat', 'ph-rice',
                                        'ph-tag', 'ph-tags', 'ph-bookmark', 'ph-bookmark-simple', 'ph-flag', 'ph-flag-banner',
                                        'ph-star', 'ph-heart', 'ph-thumbs-up', 'ph-check-circle', 'ph-warning-circle', 'ph-info',
                                        'ph-trash', 'ph-pencil', 'ph-pencil-simple', 'ph-eraser', 'ph-plus', 'ph-minus',
                                        'ph-magnifying-glass', 'ph-magnifying-glass-plus', 'ph-magnifying-glass-minus', 'ph-list', 'ph-grid-four', 'ph-squares-four',
                                    ];
                                @endphp
                                
                                @foreach($icons as $icon)
                                    <button type="button"
                                            @click="selected = '{{ $icon }}'; open = false"
                                            x-show="!search || '{{ $icon }}'.toLowerCase().includes(search.toLowerCase())"
                                            class="flex flex-col items-center justify-center p-2 rounded-lg hover:bg-primary-50 transition-colors group"
                                            :class="{ 'bg-primary-100 ring-2 ring-primary-500': selected === '{{ $icon }}' }">
                                        <i class="ph {{ $icon }} ph-duotone w-6 h-6 text-gray-600 group-hover:text-primary-600 mb-1"></i>
                                        <span class="text-[10px] text-gray-500 truncate w-full text-center">{{ str_replace('ph-', '', $icon) }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="icon" x-model="selected" value="{{ old('icon', 'ph-tag') }}">
                
                @error('icon')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="form-label block mb-2">
                    Deskripsi Singkat <span class="text-gray-400 font-normal ml-1">(Opsional)</span>
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="4"
                          placeholder="Tuliskan keterangan untuk kategori ini..."
                          class="form-input w-full resize-none @error('description') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.kategori.index') }}" class="btn-secondary justify-center text-center">
                    <i class="ph ph-arrow-left w-4 h-4"></i>
                    Batal
                </a>
                <button type="submit" class="btn-primary shadow-md justify-center">
                    <i class="ph ph-check w-4 h-4"></i>
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>

</div>
@endsection