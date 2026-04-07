/**
 * Navigation Manager for Alpine.js
 * Handles mobile navigation, dropdowns, scroll behavior, and keyboard accessibility
 */

const Navigation = (() => {
    'use strict';

    /**
     * Navigation state manager
     * @returns {Object} Alpine-compatible state object
     */
    const createState = () => ({
        sidebarOpen: false,
        mobileMenuOpen: false,
        activeDropdown: null,
        scrolled: false,
        lastScrollY: 0,
        hideHeader: false,

        /**
         * Initialize navigation state
         */
        init() {
            this.handleScroll();
            window.addEventListener('scroll', () => this.handleScroll(), { passive: true });
            this.handleResize();
            window.addEventListener('resize', () => this.handleResize());
            this.handleEscape();
        },

        /**
         * Handle scroll events
         */
        handleScroll() {
            const currentScrollY = window.scrollY;
            this.scrolled = currentScrollY > 20;

            // Auto-hide header on scroll down (mobile)
            if (window.innerWidth < 768) {
                if (currentScrollY > this.lastScrollY && currentScrollY > 100) {
                    this.hideHeader = true;
                } else {
                    this.hideHeader = false;
                }
            }

            this.lastScrollY = currentScrollY;
        },

        /**
         * Handle window resize
         */
        handleResize() {
            // Close mobile menus when switching to desktop
            if (window.innerWidth >= 768) {
                this.sidebarOpen = false;
                this.mobileMenuOpen = false;
            }
        },

        /**
         * Handle Escape key
         */
        handleEscape() {
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeAll();
                }
            });
        },

        /**
         * Toggle sidebar
         */
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            this.toggleBodyScroll(this.sidebarOpen);
        },

        /**
         * Close sidebar
         */
        closeSidebar() {
            this.sidebarOpen = false;
            this.toggleBodyScroll(false);
        },

        /**
         * Toggle mobile menu
         */
        toggleMobileMenu() {
            this.mobileMenuOpen = !this.mobileMenuOpen;
        },

        /**
         * Toggle dropdown
         * @param {string} name - Dropdown identifier
         */
        toggleDropdown(name) {
            this.activeDropdown = this.activeDropdown === name ? null : name;
        },

        /**
         * Check if dropdown is open
         * @param {string} name - Dropdown identifier
         * @returns {boolean}
         */
        isDropdownOpen(name) {
            return this.activeDropdown === name;
        },

        /**
         * Close all navigation elements
         */
        closeAll() {
            this.sidebarOpen = false;
            this.mobileMenuOpen = false;
            this.activeDropdown = null;
            this.toggleBodyScroll(false);
        },

        /**
         * Toggle body scroll lock
         * @param {boolean} lock - Whether to lock scroll
         */
        toggleBodyScroll(lock) {
            if (lock) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        },

        /**
         * Smooth scroll to element
         * @param {string} target - Target element selector or ID
         */
        scrollTo(target) {
            const element = document.querySelector(target);
            if (element) {
                const offset = 80; // Header height
                const elementPosition = element.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - offset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        },

        /**
         * Check if nav item is active
         * @param {string} route - Route pattern
         * @returns {boolean}
         */
        isActive(route) {
            const currentPath = window.location.pathname;

            if (route.includes('*')) {
                const baseRoute = route.replace('/*', '');
                return currentPath.startsWith(baseRoute);
            }

            return currentPath === route || currentPath.startsWith(route + '/');
        }
    });

    /**
     * Mobile bottom navigation data
     * @returns {Array} Navigation items
     */
    const getMobileNavItems = (type = 'user') => {
        const common = [
            { label: 'Beranda', icon: 'ph-house', href: '/', exact: true },
        ];

        const userItems = [
            { label: 'Dashboard', icon: 'ph-squares-four', href: '/user/dashboard' },
            { label: 'Produk', icon: 'ph-storefront', href: '/user/produk' },
            { label: 'Keranjang', icon: 'ph-shopping-cart', href: '/user/keranjang', badge: 'cartCount' },
            { label: 'Akun', icon: 'ph-user', href: '/user/profile' },
        ];

        const adminItems = [
            { label: 'Dashboard', icon: 'ph-chart-pie-slice', href: '/admin/dashboard' },
            { label: 'Produk', icon: 'ph-package', href: '/admin/produk' },
            { label: 'Pesanan', icon: 'ph-clipboard-text', href: '/admin/pesanan', badge: 'newOrders' },
            { label: 'Akun', icon: 'ph-user', href: '/admin/profile' },
        ];

        return type === 'admin' ? [...adminItems] : [...userItems];
    };

    /**
     * Initialize navigation for Alpine
     */
    const init = () => {
        document.addEventListener('alpine:init', () => {
            window.Alpine.data('navigation', createState);
        });
    };

    return {
        createState,
        getMobileNavItems,
        init
    };
})();

// Auto-initialize
if (typeof window !== 'undefined') {
    Navigation.init();
}

export default Navigation;
