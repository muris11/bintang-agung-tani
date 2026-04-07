/**
 * Alpine.js Router Plugin
 * Navigation helper for Laravel Blade applications with Alpine.js
 *
 * Usage:
 *   document.addEventListener('alpine:init', () => {
 *     Alpine.plugin(router);
 *   });
 */

const router = (Alpine) => {
    Alpine.magic('router', () => {
        return {
            /**
             * Check if current route matches the given pattern
             * @param {string} pattern - Route pattern to match (supports wildcards)
             * @returns {boolean}
             */
            is(pattern) {
                const currentPath = window.location.pathname;
                const normalizedPattern = pattern.replace(/^\/+|\/+$/g, '');
                const normalizedPath = currentPath.replace(/^\/+|\/+$/g, '');

                if (normalizedPattern === normalizedPath) {
                    return true;
                }

                // Handle wildcard patterns
                if (normalizedPattern.includes('*')) {
                    const regexPattern = normalizedPattern
                        .replace(/\*/g, '.*')
                        .replace(/\//g, '\\/');
                    const regex = new RegExp(`^${regexPattern}$`);
                    return regex.test(normalizedPath);
                }

                return false;
            },

            /**
             * Check if current route starts with the given path
             * @param {string} path - Path to check
             * @returns {boolean}
             */
            startsWith(path) {
                const currentPath = window.location.pathname;
                return currentPath.startsWith(path);
            },

            /**
             * Get current route name or path
             * @returns {string}
             */
            current() {
                return window.location.pathname;
            },

            /**
             * Navigate to a URL
             * @param {string} url - URL to navigate to
             * @param {boolean} replace - Whether to replace current history entry
             */
            navigate(url, replace = false) {
                if (replace) {
                    window.location.replace(url);
                } else {
                    window.location.href = url;
                }
            },

            /**
             * Navigate back
             */
            back() {
                window.history.back();
            },

            /**
             * Reload current page
             */
            reload() {
                window.location.reload();
            },

            /**
             * Get active class based on route match
             * @param {string} pattern - Route pattern
             * @param {string} activeClass - Class to return when active
             * @param {string} inactiveClass - Class to return when inactive
             * @returns {string}
             */
            activeClass(pattern, activeClass = 'active', inactiveClass = '') {
                return this.is(pattern) ? activeClass : inactiveClass;
            },

            /**
             * Get query parameter value
             * @param {string} param - Parameter name
             * @returns {string|null}
             */
            query(param) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(param);
            },

            /**
             * Build URL with query parameters
             * @param {string} baseUrl - Base URL
             * @param {Object} params - Query parameters object
             * @returns {string}
             */
            url(baseUrl, params = {}) {
                const url = new URL(baseUrl, window.location.origin);
                Object.entries(params).forEach(([key, value]) => {
                    if (value !== null && value !== undefined && value !== '') {
                        url.searchParams.set(key, value);
                    }
                });
                return url.toString();
            }
        };
    });

    // Add $active magic helper for cleaner syntax
    Alpine.magic('active', (el) => {
        return (pattern, className = 'active') => {
            const currentPath = window.location.pathname;
            const normalizedPattern = pattern.replace(/^\/+|\/+$/g, '');
            const normalizedPath = currentPath.replace(/^\/+|\/+$/g, '');

            let isActive = false;
            if (normalizedPattern.includes('*')) {
                const regexPattern = normalizedPattern
                    .replace(/\*/g, '.*')
                    .replace(/\//g, '\\/');
                const regex = new RegExp(`^${regexPattern}$`);
                isActive = regex.test(normalizedPath);
            } else {
                isActive = normalizedPattern === normalizedPath;
            }

            return isActive ? className : '';
        };
    });
};

// Auto-initialize if Alpine is available
if (typeof window !== 'undefined' && window.Alpine) {
    document.addEventListener('alpine:init', () => {
        window.Alpine.plugin(router);
    });
}

export default router;
