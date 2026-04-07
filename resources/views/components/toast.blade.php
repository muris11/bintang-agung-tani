{{-- Toast Notification Component with Alpine.js --}}
<div x-data="{ 
    toasts: [],
    add(message, type = 'success') {
        const id = Date.now();
        this.toasts.push({ id, message, type });
        setTimeout(() => this.remove(id), 3000);
    },
    remove(id) {
        this.toasts = this.toasts.filter(t => t.id !== id);
    }
}" 
     @toast.window="add($event.detail.message, $event.detail.type)" 
     class="toast-container"
     x-cloak>
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="true" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             :class="{
                 'toast-success': toast.type === 'success',
                 'toast-error': toast.type === 'error',
                 'toast-warning': toast.type === 'warning'
             }"
             class="toast">
            <i :class="{
                'ph-fill ph-check-circle': toast.type === 'success',
                'ph-fill ph-x-circle': toast.type === 'error',
                'ph-fill ph-warning': toast.type === 'warning'
            }" class="ph text-xl"></i>
            <span x-text="toast.message" class="text-sm font-medium"></span>
            <button @click="remove(toast.id)" class="ml-auto hover:opacity-75 transition-opacity">
                <i class="ph ph-x"></i>
            </button>
        </div>
    </template>
</div>

{{-- Session-based Toast --}}
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('toast', { 
                    detail: { message: "{{ addslashes(session('success')) }}", type: 'success' }
                }));
            }, 100);
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('toast', { 
                    detail: { message: "{{ addslashes(session('error')) }}", type: 'error' }
                }));
            }, 100);
        });
    </script>
@endif

@if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('toast', { 
                    detail: { message: "{{ addslashes(session('warning')) }}", type: 'warning' }
                }));
            }, 100);
        });
    </script>
@endif
