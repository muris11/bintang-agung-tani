import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import 'flowbite';
import './bootstrap';
import router from './router';
import Navigation from './navigation';

// Lazy load ApexCharts only when needed
const loadApexCharts = async () => {
    if (!window.ApexCharts) {
        const module = await import('apexcharts');
        window.ApexCharts = module.default || module;
    }
    return window.ApexCharts;
};

// Expose lazy loader globally
window.loadApexCharts = loadApexCharts;

window.Alpine = Alpine;

// Theme initialization - runs before Alpine starts
(function() {
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    // Apply theme immediately to prevent flash
    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
})();

// Register Alpine.js plugins
Alpine.plugin(collapse);

document.addEventListener('alpine:init', () => {
    Alpine.plugin(router);
    Navigation.init();
    
    // Auto-initialize charts if data-chart attribute present
    Alpine.directive('chart', (el, { expression }, { evaluate }) => {
        const config = evaluate(expression);
        loadApexCharts().then(ApexCharts => {
            const chart = new ApexCharts(el, config);
            chart.render();
        });
    });
});

// Performance monitoring
if (typeof window !== 'undefined' && 'PerformanceObserver' in window) {
    // Monitor Core Web Vitals (LCP and CLS)
    try {
        const vitalsObserver = new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                // Log performance metrics (in production, send to analytics)
                console.log(`[Performance] ${entry.name}: ${entry.value}`);
            }
        });
        vitalsObserver.observe({ entryTypes: ['largest-contentful-paint', 'layout-shift'] });
    } catch (e) {
        // Core Web Vitals monitoring not supported
    }
    
    // Monitor long tasks
    try {
        const longTaskObserver = new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                if (entry.duration > 50) {
                    console.warn(`[Performance] Long task detected: ${entry.duration}ms`);
                }
            }
        });
        longTaskObserver.observe({ entryTypes: ['longtask'] });
    } catch (e) {
        // Long task monitoring not supported
    }
}

// Prefetch on hover for internal links
document.addEventListener('DOMContentLoaded', () => {
    const prefetched = new Set();
    
    document.querySelectorAll('a[href^="/"]').forEach(link => {
        link.addEventListener('mouseenter', () => {
            const url = link.getAttribute('href');
            if (!prefetched.has(url) && !link.hasAttribute('data-no-prefetch')) {
                prefetched.add(url);
                
                // Create prefetch link
                const prefetchLink = document.createElement('link');
                prefetchLink.rel = 'prefetch';
                prefetchLink.href = url;
                document.head.appendChild(prefetchLink);
            }
        }, { passive: true });
    });
});

// Request Idle Callback polyfill
window.requestIdleCallback = window.requestIdleCallback || function(cb) {
    return setTimeout(() => {
        cb({
            didTimeout: false,
            timeRemaining() {
                return Math.max(0, 50 - (Date.now() % 50));
            }
        });
    }, 1);
};

Alpine.start();
