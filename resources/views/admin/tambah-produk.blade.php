@extends('layouts.admin')

@section('title', 'Tambah Produk Baru')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">

    <!-- Breadcrumb & Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i>
                            <a href="/admin/produk" class="hover:text-primary-600 transition-colors">Kelola Produk</a>
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
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Tambah Produk Baru</h1>
            <p class="text-gray-500 mt-1 text-sm">Masukkan detail produk lengkap untuk ditampilkan di katalog.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/produk" class="btn-secondary text-sm h-10 shadow-sm">
                <i class="ph ph-arrow-left ph-bold w-4 h-4"></i> Kembali
            </a>
            <button class="btn-primary text-sm h-10 shadow-md">
                <i class="ph ph-floppy-disk ph-bold w-4 h-4"></i> Simpan Produk
            </button>
        </div>
    </div>

    <!-- Main Content Grid -->
    <form action="{{ route('admin.produk.store') }}" method="POST" class="grid grid-cols-1 xl:grid-cols-3 gap-6" enctype="multipart/form-data">
        @csrf
        
        <!-- Kolom Kiri: Informasi Utama & Harga -->
        <div class="xl:col-span-2 space-y-6">
            
            <!-- Informasi Umum -->
            <div class="card p-6">
                <div class="flex items-center gap-2 mb-6 border-b border-gray-100 pb-4">
                    <i class="ph ph-info ph-fill w-5 h-5 text-primary-600"></i>
                    <h2 class="text-lg font-bold text-gray-900">Informasi Umum</h2>
                </div>
                
                <div class="space-y-5">
                    <div>
                        <label for="nama_produk" class="form-label mb-1.5 block">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_produk" class="form-input w-full" placeholder="Misal: Pupuk NPK Phonska 15-15-15" required>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="kategori" class="form-label mb-1.5 block">Kategori <span class="text-red-500">*</span></label>
                            <select id="kategori" class="form-input w-full bg-gray-50/50" required>
                                <option value="" disabled selected>Pilih Kategori</option>
                                <option value="pupuk">Pupuk</option>
                                <option value="pestisida">Pestisida</option>
                                <option value="benih">Benih & Bibit</option>
                                <option value="alat">Alat Pertanian</option>
                            </select>
                        </div>
                        <div>
                            <label for="merek" class="form-label mb-1.5 block">Merek / Brand <span class="text-gray-400 font-normal ml-1">(Opsional)</span></label>
                            <input type="text" id="merek" class="form-input w-full" placeholder="Misal: Petrokimia Gresik">
                        </div>
                    </div>
                    
                    <div>
                        <label for="deskripsi" class="form-label mb-1.5 block">Deskripsi Produk <span class="text-red-500">*</span></label>
                        <!-- Fake Rich Text Editor Toolbar -->
                        <div class="w-full mb-1 border border-gray-200 rounded-t-xl bg-gray-50 flex items-center p-2 gap-1 overflow-x-auto">
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-text-b ph-bold w-4 h-4"></i></button>
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-text-italic w-4 h-4"></i></button>
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-text-underline w-4 h-4"></i></button>
                            <div class="w-px h-4 bg-gray-300 mx-1"></div>
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-list-bullets w-4 h-4"></i></button>
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-list-numbers w-4 h-4"></i></button>
                        </div>
                        <textarea id="deskripsi" rows="6" class="form-input w-full rounded-t-none resize-y" placeholder="Tuliskan keterangan lengkap produk, manfaat, dan cara penggunaan..." required></textarea>
                        <p class="text-xs text-gray-500 mt-2">Pastikan deskripsi menarik dan menjelaskan detail spesifik produk.</p>
                    </div>
                </div>
            </div>
            
            <!-- Harga & Inventaris -->
            <div class="card p-6">
                <div class="flex items-center gap-2 mb-6 border-b border-gray-100 pb-4">
                    <i class="ph ph-currency-circle-dollar ph-fill w-5 h-5 text-amber-500"></i>
                    <h2 class="text-lg font-bold text-gray-900">Harga & Inventaris</h2>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="harga" class="form-label mb-1.5 block">Harga Jual (Rp) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-medium">Rp</span>
                            </div>
                            <input type="number" id="harga" class="form-input w-full pl-12" placeholder="0" required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="stok" class="form-label mb-1.5 block">Stok Awal <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" id="stok" class="form-input w-full" placeholder="0" required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="berat" class="form-label mb-1.5 block">Berat/Volume (Pengiriman) <span class="text-red-500">*</span></label>
                        <div class="flex">
                            <input type="number" id="berat" class="form-input rounded-r-none border-r-0 w-full" placeholder="0" required>
                            <select class="form-input rounded-l-none bg-gray-50 w-28 shrink-0">
                                <option value="kg">Kg</option>
                                <option value="g">Gram</option>
                                <option value="l">Liter</option>
                                <option value="ml">Ml</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label for="satuan" class="form-label mb-1.5 block">Satuan Jual <span class="text-red-500">*</span></label>
                        <select id="satuan" class="form-input w-full bg-gray-50/50" required>
                            <option value="zak">Zak / Karung</option>
                            <option value="botol">Botol</option>
                            <option value="pcs">Pcs / Buah</option>
                            <option value="paket">Paket</option>
                        </select>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- Kolom Kanan: Media & Setting -->
        <div class="space-y-6">
            
            <!-- Media Produk -->
            <div class="card p-6">
                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-4">
                    <i class="ph ph-image ph-fill w-5 h-5 text-blue-500"></i>
                    <h2 class="text-lg font-bold text-gray-900">Media Produk</h2>
                </div>
                
                <div class="border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center bg-gray-50 hover:bg-primary-50 hover:border-primary-300 group cursor-pointer transition-all">
                    <div class="bg-white w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-3 shadow-md group-hover:scale-110 transition-transform">
                        <i class="ph ph-upload-simple ph-bold w-6 h-6 text-primary-600"></i>
                    </div>
                    <p class="text-sm font-bold text-gray-700 mb-1 group-hover:text-primary-700">Klik untuk unggah foto</p>
                    <p class="text-xs text-gray-500">atau seret file ke sini</p>
                    <div class="mt-4 flex items-center justify-center">
                        <span class="text-[10px] font-medium tracking-wide text-gray-400 uppercase bg-gray-200/50 px-2 py-1 rounded">PNG, JPG, Max 5MB</span>
                    </div>
                </div>
            </div>
            
            <!-- Status Produk -->
            <div class="card p-6">
                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-4">
                    <i class="ph ph-toggle-right ph-fill w-5 h-5 text-[#10b981]"></i>
                    <h2 class="text-lg font-bold text-gray-900">Visibilitas Produk</h2>
                </div>
                
                <label class="inline-flex items-center cursor-pointer bg-gray-50 border border-gray-200 p-4 rounded-xl w-full">
                    <input type="checkbox" value="" class="sr-only peer" checked>
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-100 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                    <div class="ms-4">
                        <span class="block text-sm font-bold text-gray-900">Aktifkan Produk</span>
                        <span class="block text-xs text-gray-500">Produk akan tampil di katalog publik</span>
                    </div>
                </label>
            </div>
            
        </div>
    </form>

</div>
@endsection
