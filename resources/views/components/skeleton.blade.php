{{-- Skeleton Loader Component --}}
@props([
    'type' => 'text',
    'lines' => 3,
    'class' => ''
])

<div class="animate-pulse {{ $class }}" role="status" aria-label="Loading">
    @switch($type)
        @case('card')
            <div class="bg-white rounded-2xl overflow-hidden border border-gray-100">
                <div class="aspect-[4/5] bg-gray-200"></div>
                <div class="p-4 space-y-3">
                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                    <div class="flex items-center justify-between pt-2">
                        <div class="h-6 bg-gray-200 rounded w-1/3"></div>
                        <div class="h-8 bg-gray-200 rounded-lg w-24"></div>
                    </div>
                </div>
            </div>
            @break
            
        @case('image')
            <div class="aspect-square bg-gray-200 rounded-2xl"></div>
            @break
            
        @case('avatar')
            <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
            @break
            
        @case('product-grid')
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @for($i = 0; $i < 10; $i++)
                    <div class="bg-white rounded-2xl overflow-hidden border border-gray-100">
                        <div class="aspect-[4/5] bg-gray-200"></div>
                        <div class="p-4 space-y-3">
                            <div class="h-4 bg-gray-200 rounded w-full"></div>
                            <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                            <div class="flex items-center justify-between pt-2">
                                <div class="h-5 bg-gray-200 rounded w-1/3"></div>
                                <div class="h-8 bg-gray-200 rounded-lg w-20"></div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
            @break
            
        @case('dashboard-stats')
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @for($i = 0; $i < 4; $i++)
                    <div class="bg-white rounded-xl p-4 border border-gray-100 space-y-3">
                        <div class="h-8 w-8 bg-gray-200 rounded-lg"></div>
                        <div class="h-6 bg-gray-200 rounded w-1/2"></div>
                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    </div>
                @endfor
            </div>
            @break
            
        @case('banner')
            <div class="w-full h-48 md:h-64 bg-gray-200 rounded-2xl"></div>
            @break
            
        @case('text')
        @default
            <div class="space-y-2">
                @for($i = 0; $i < $lines; $i++)
                    <div class="h-4 bg-gray-200 rounded {{ $i === $lines - 1 ? 'w-2/3' : 'w-full' }}"></div>
                @endfor
            </div>
            @break
    @endswitch
    <span class="sr-only">Loading...</span>
</div>
