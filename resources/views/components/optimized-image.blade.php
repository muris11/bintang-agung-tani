@props([
    'src',
    'alt',
    'class' => '',
    'loading' => 'lazy',
    'width' => null,
    'height' => null,
    'srcset' => null,
    'sizes' => null,
    'objectFit' => 'cover',
    'placeholder' => null,
])

@php
// Determine sizes attribute if not provided
if (!$sizes && $width) {
    $sizes = "(max-width: 640px) 100vw, (max-width: 1024px) 50vw, {$width}px";
}

// Build srcset if array provided
$srcsetAttr = '';
if (is_array($srcset)) {
    $srcsetParts = [];
    foreach ($srcset as $size => $url) {
        $srcsetParts[] = "{$url} {$size}w";
    }
    $srcsetAttr = implode(', ', $srcsetParts);
} elseif ($srcset) {
    $srcsetAttr = $srcset;
}

// Object fit class
$objectFitClass = match($objectFit) {
    'contain' => 'object-contain',
    'fill' => 'object-fill',
    'none' => 'object-none',
    'scale-down' => 'object-scale-down',
    default => 'object-cover',
};

// Placeholder color
$placeholderColor = $placeholder ?? 'bg-gray-100';
@endphp

<div class="relative {{ $placeholderColor }} {{ $class }}" 
     style="{{ $width ? 'max-width: ' . $width . 'px;' : '' }}">
    @if($src)
        <img 
            src="{{ $src }}"
            alt="{{ $alt }}"
            class="w-full h-full {{ $objectFitClass }} transition-opacity duration-300"
            loading="{{ $loading }}"
            decoding="async"
            {{ $width ? 'width=' . $width : '' }}
            {{ $height ? 'height=' . $height : '' }}
            {{ $srcsetAttr ? 'srcset="' . $srcsetAttr . '"' : '' }}
            {{ $sizes ? 'sizes="' . $sizes . '"' : '' }}
            onload="this.classList.add('opacity-100'); this.previousElementSibling?.remove();"
            onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');"
        >
    @endif
    
    {{-- Error placeholder / No image placeholder --}}
    <div class="{{ $src ? 'hidden' : 'flex' }} absolute inset-0 items-center justify-center bg-gray-50">
        <i class="ph ph-image text-gray-300 w-10 h-10"></i>
    </div>
</div>
