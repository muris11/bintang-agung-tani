@php
    use App\Models\Setting;
    
    $authName = auth()->user()->name ?? 'Admin';
    $avatarName = urlencode($authName);
    $storeName = Setting::get('store_name', 'Bintang Agung Tani');
@endphp

<header class="h-16 bg-gradient-to-r from-primary-600 to-primary-700 border-b border-primary-800 sticky top-0 z-50 shadow-md w-full flex items-center justify-between px-4 md:px-6 shrink-0">
    <!-- Left Section: Logo & Toggle -->
    <div class="flex items-center gap-4 md:w-64 shrink-0">
        <!-- Mobile menu button -->
        <button @click="sidebarOpen = !sidebarOpen" type="button"
                class="text-white/80 hover:text-white md:hidden focus:outline-none transition-colors active:scale-95 touch-target"
                aria-label="Toggle navigation menu"
                aria-expanded="false"
                aria-controls="sidebar">
            <i class="ph ph-list w-6 h-6" aria-hidden="true"></i>
        </button>

        <!-- Logo -->
        <a href="/admin/dashboard" class="flex items-center gap-2.5 group">
            @if(file_exists(public_path('images/logo.png')))
                <img loading="lazy" src="/images/logo.png" alt="Logo" class="h-8 md:h-9 object-contain w-auto group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="w-8 h-8 md:w-9 md:h-9 rounded-xl bg-white/20 flex items-center justify-center text-white font-bold text-sm shadow-soft group-hover:shadow-lg transition-all duration-300">
                    <i class="ph ph-plant w-5 h-5"></i>
                </div>
            @endif
            <div class="hidden sm:block">
                <h1 class="text-white font-bold text-sm leading-tight group-hover:text-primary-100 transition-colors">{{ $storeName }}</h1>
                <p class="text-white/70 text-[11px] font-medium">Admin Panel</p>
            </div>
        </a>
    </div>

    <!-- Middle Section: Search -->
    <div class="flex-1 max-w-xl px-4 hidden md:block">
        <div class="relative group">
            <input type="search" placeholder="Cari produk, pesanan, user..."
                   class="w-full bg-white/10 border border-white/20 text-white placeholder-white/60 text-sm rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:bg-white/20 focus:border-white/30 focus:ring-4 focus:ring-white/20 transition-all duration-200 shadow-subtle"
                   aria-label="Search">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="ph ph-magnifying-glass w-4 h-4 text-white/60 group-focus-within:text-white transition-colors"></i>
            </div>
        </div>
    </div>

    <!-- Right Section: Actions & Profile -->
    <div class="flex items-center gap-3 sm:gap-4 shrink-0">
        <!-- Notification -->
        <button type="button" class="relative p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-all touch-target group"
                aria-label="Notifications">
            <i class="ph ph-bell w-5 h-5 group-hover:scale-110 transition-transform"></i>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-400 rounded-full border-2 border-primary-600" aria-hidden="true"></span>
        </button>

        <!-- Message/Inbox -->
        <button type="button" class="relative p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-all touch-target group"
                aria-label="Messages">
            <i class="ph ph-envelope-simple w-5 h-5 group-hover:scale-110 transition-transform"></i>
            <span class="absolute -top-0.5 -right-0.5 text-[10px] font-bold bg-amber-400 text-white w-4 h-4 flex items-center justify-center rounded-full border-2 border-primary-600" aria-hidden="true">16</span>
        </button>

        <!-- Divider -->
        <div class="w-px h-6 bg-white/20 hidden sm:block"></div>

        <!-- Profile Dropdown -->
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button @click="open = !open" class="flex items-center gap-2.5 focus:outline-none hover:bg-white/10 rounded-xl p-1.5 -ml-1.5 transition-colors">
                <img loading="lazy" src="https://ui-avatars.com/api/?name={{ $avatarName }}&background=ffffff&color=059669" alt="{{ $authName }}" class="w-8 h-8 rounded-xl object-cover shadow-subtle ring-2 ring-white/30">
                <span class="text-white font-medium text-sm hidden sm:block">{{ $authName }}</span>
                <i class="ph ph-caret-down w-3.5 h-3.5 text-white/60 hidden sm:block transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 scale-95 -translate-y-2"
                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="transform opacity-0 scale-95 -translate-y-2"
                 class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-floating border border-gray-100 py-1.5 z-50 origin-top-right"
                 style="display: none;">
                <div class="px-4 py-2.5 border-b border-gray-100 mb-1.5">
                    <p class="text-sm font-bold text-gray-900">{{ $authName }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Administrator</p>
                </div>
                <a href="/admin/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary-600 flex items-center gap-2.5 transition-colors">
                    <i class="ph ph-gear w-4 h-4"></i> Pengaturan
                </a>
                <div class="border-t border-gray-100 my-1"></div>
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors">
                        <i class="ph ph-sign-out w-4 h-4"></i> Log out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
