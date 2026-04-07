{{-- empty-state.blade.php --}}
@props([
    'icon' => 'magnifying-glass',
    'title' => 'Tidak Ada Data',
    'description' => 'Maaf, kami tidak menemukan data yang sesuai.',
    'actionText' => null,
    'actionUrl' => null,
    'actionHref' => null,
    'actionIcon' => 'arrow-counter-clockwise'
])

@php
$url = $actionUrl ?? $actionHref;
@endphp

<div class="col-span-full py-16">
    <div class="flex flex-col items-center justify-center text-center max-w-md mx-auto">
        <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-50 rounded-3xl flex items-center justify-center mb-6 shadow-inner">
            <i class="ph ph-{{ $icon }} text-5xl text-gray-300"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $title }}</h3>
        <p class="text-gray-500 mb-6">{{ $description }}</p>
        @if($actionText && $url)
            <a href="{{ $url }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <i class="ph ph-{{ $actionIcon }} text-lg"></i>
                {{ $actionText }}
            </a>
        @endif
    </div>
</div>
