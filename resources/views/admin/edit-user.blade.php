@extends('layouts.admin')

@section('title', 'Edit User')

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
                                <a href="/admin/users" class="hover:text-primary-600 transition-colors">Kelola User</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i>
                                <span class="text-gray-900 font-medium">Edit User</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Edit User</h1>
                <p class="text-gray-500 mt-1 text-sm">Edit data pengguna yang terdaftar.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="/admin/users" class="btn-secondary text-sm h-10 shadow-sm">
                    <i class="ph ph-arrow-left ph-bold w-4 h-4"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <form action="/admin/users/{{ $user->id }}" method="POST" class="max-w-2xl">
            @csrf
            @method('PUT')

            <div class="card p-6 space-y-6">
                <div class="flex items-center gap-2 mb-2 border-b border-gray-100 pb-4">
                    <i class="ph ph-user ph-fill w-5 h-5 text-primary-600"></i>
                    <h2 class="text-lg font-bold text-gray-900">Informasi User</h2>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-start gap-3">
                        <i class="ph ph-warning-circle w-5 h-5 mt-0.5"></i>
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- User Avatar -->
                <div class="flex items-center gap-4 pb-4 border-b border-gray-100">
                    <img loading="lazy" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                        class="w-16 h-16 rounded-full object-cover ring-2 ring-primary-200">
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500">Bergabung:
                            {{ $user->created_at->locale('id')->isoFormat('D MMMM YYYY') }}</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label for="name" class="form-label mb-1.5 block">Nama Lengkap <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                            class="form-input w-full" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div>
                        <label for="email" class="form-label mb-1.5 block">Alamat Email <span
                                class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            class="form-input w-full" placeholder="nama@email.com" required>
                    </div>

                    <div>
                        <label for="phone" class="form-label mb-1.5 block">No. Telepon <span
                                class="text-gray-400 font-normal ml-1">(Opsional)</span></label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                            class="form-input w-full" placeholder="08xxxxxxxxxx">
                    </div>

                    <div>
                        <label for="address" class="form-label mb-1.5 block">Alamat Ringkas Profil <span
                                class="text-gray-400 font-normal ml-1">(Opsional)</span></label>
                        <textarea id="address" name="address" rows="3" class="form-input w-full resize-y"
                            placeholder="Opsional. Alamat utama user sebaiknya dikelola dari menu alamat user.">{{ old('address', $user->address) }}</textarea>
                        @if ($user->defaultAddress)
                            <p class="text-xs text-gray-500 mt-2">Alamat utama user saat ini:
                                {{ $user->defaultAddress->getCompleteAddressAttribute() }}</p>
                        @endif
                    </div>

                    <div class="border-t border-gray-100 pt-5">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="ph ph-lock-key ph-fill w-4 h-4 text-amber-500"></i>
                            Password Baru
                            <span class="text-xs font-normal text-gray-500 ml-1">(Kosongkan jika tidak ingin
                                mengubah)</span>
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="password" class="form-label mb-1.5 block">Password Baru</label>
                                <input type="password" id="password" name="password" class="form-input w-full"
                                    placeholder="••••••••">
                                <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
                            </div>

                            <div>
                                <label for="password_confirmation" class="form-label mb-1.5 block">Konfirmasi Password
                                    Baru</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-input w-full" placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <a href="/admin/users" class="btn-secondary text-sm shadow-sm">Batal</a>
                <button type="submit" class="btn-primary text-sm shadow-md">
                    <i class="ph ph-floppy-disk ph-bold w-4 h-4"></i> Simpan Perubahan
                </button>
            </div>
        </form>

    </div>
@endsection
