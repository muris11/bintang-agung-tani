@props([
    'type' => 'spinner',
    'size' => 'md',
    'text' => null,
    'fullScreen' => false,
])

@php
$sizes = [
    'sm' => 'w-4 h-4',
    'md' => 'w-6 h-6',
    'lg' => 'w-8 h-8',
    'xl' => 'w-12 h-12',
];

$textSizes = [
    'sm' => 'text-xs',
    'md' => 'text-sm',
    'lg' => 'text-base',
    'xl' => 'text-lg',
];

$sizeClass = $sizes[$size] ?? $sizes['md'];
$textSizeClass = $textSizes[$size] ?? $textSizes['md'];
@endphp

@if($fullScreen)
<div class="fixed inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center z-50">
@endif

<div class="flex flex-col items-center justify-center gap-3" role="status" aria-label="Loading">
    @if($type === 'spinner')
        <div class="inline-block {{ $sizeClass }} border-2 border-gray-300 border-t-primary-600 rounded-full animate-spin" aria-hidden="true"></div>
    @elseif($type === 'skeleton')
        <div class="space-y-3 w-full max-w-md">
            <div class="skeleton-title"></div>
            <div class="skeleton-text"></div>
            <div class="skeleton-text w-3/4"></div>
        </div>
    @endif
    
    @if($text)
        <span class="text-gray-500 {{ $textSizeClass }}">{{ $text }}</span>
    @else
        <span class="sr-only">Loading...</span>
    @endif
</div>

@if($fullScreen)
</div>
@endif
