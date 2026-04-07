@props(['active' => false, 'icon'])

@php
$classes = $active
    ? 'flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-primary-50 to-primary-100/40 text-primary-700 font-medium transition-all duration-200 border border-primary-100 shadow-subtle'
    : 'flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-primary-600 font-medium transition-all duration-200 border border-transparent hover:border-gray-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} {{ $active ? 'aria-current="page"' : '' }}>
    @if(isset($icon))
        <i class="ph {{ $icon }} w-5 h-5 shrink-0 {{ $active ? 'text-primary-600 ph-bold' : 'text-gray-500' }}" aria-hidden="true"></i>
    @endif
    <span class="text-sm font-medium">{{ $slot }}</span>
</a>
