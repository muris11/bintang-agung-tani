@props(['active' => false, 'icon'])

@php
$classes = $active
            ? 'flex items-center gap-3 px-4 py-2.5 rounded-xl bg-primary-700 text-white font-bold transition-all duration-300 shadow-sm translate-x-1'
            : 'flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-primary-50 hover:text-primary-700 font-medium transition-all duration-300 hover:translate-x-1 active:scale-95 group';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($icon))
        <{{ $icon }} class="text-[1.125rem] shrink-0 {{ $active ? 'text-white' : 'text-gray-400 group-hover:text-primary-700 transition-colors duration-300' }}"></{{ $icon }}>
    @endif
    <span class="text-[0.9rem]">{{ $slot }}</span>
</a>
