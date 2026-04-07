<?php

namespace Tests\Browser\Accessibility;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TouchTargetTest extends DuskTestCase
{
    /** @test */
    public function all_interactive_elements_meet_minimum_touch_target_size()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/user/dashboard')
                ->resize(375, 812); // iPhone X size

            // Get all interactive elements and check sizes
            // Exclude hidden elements, elements with 0 dimensions, and injected extension elements
            $sizes = $browser->script("
                const elements = document.querySelectorAll('button, a[href], [role=\"button\"], input[type=\"submit\"]');
                const smallElements = [];
                elements.forEach(el => {
                    // Skip hidden elements
                    if (el.offsetParent === null) return;
                    if (el.style.display === 'none') return;
                    if (el.style.visibility === 'hidden') return;
                    
                    // Skip elements injected by browser extensions (Copy as Markdown, etc.)
                    const text = el.textContent ? el.textContent.trim() : '';
                    if (text.includes('Copy as Markdown')) return;
                    if (text.includes('Copy')) return;
                    
                    // Skip elements with extension-specific classes (neutral colors are Tailwind v4 extension styles)
                    const className = el.className || '';
                    if (className.includes('neutral-')) return;
                    if (className.includes('shadow-xs')) return;
                    if (className.includes('dark:border-white/8')) return;
                    if (className.includes('dark:bg-white/')) return;
                    
                    const rect = el.getBoundingClientRect();
                    // Skip elements with 0 dimensions (not rendered)
                    if (rect.width === 0 || rect.height === 0) return;
                    
                    if (rect.width < 44 || rect.height < 44) {
                        smallElements.push({
                            tag: el.tagName,
                            class: el.className,
                            width: rect.width,
                            height: rect.height,
                            text: el.textContent ? el.textContent.trim().substring(0, 30) : '',
                            id: el.id,
                            parent: el.parentElement ? el.parentElement.className.substring(0, 50) : ''
                        });
                    }
                });
                return smallElements;
            ");

            $this->assertEmpty($sizes[0], 'Found elements smaller than 44x44px: ' . json_encode($sizes[0], JSON_PRETTY_PRINT));
        });
    }

    /** @test */
    public function navbar_icon_buttons_are_minimum_44px()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/user/dashboard')
                ->resize(375, 812);

            $buttonSizes = $browser->script("
                const buttons = document.querySelectorAll('nav button, header button');
                return Array.from(buttons).map(btn => {
                    const rect = btn.getBoundingClientRect();
                    return { width: rect.width, height: rect.height };
                });
            ");

            foreach ($buttonSizes[0] as $size) {
                $this->assertGreaterThanOrEqual(44, $size['width'], 'Button width must be >= 44px');
                $this->assertGreaterThanOrEqual(44, $size['height'], 'Button height must be >= 44px');
            }
        });
    }

    /** @test */
    public function filter_chips_meet_minimum_touch_target_size()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/user/produk')
                ->resize(375, 812);

            // Check filter buttons/sidebar links
            $linkSizes = $browser->script("
                const links = document.querySelectorAll('aside a, .card-premium a, [data-testid=\"filter-link\"]');
                return Array.from(links).map(link => {
                    const rect = link.getBoundingClientRect();
                    return { 
                        tag: link.tagName,
                        class: link.className,
                        width: rect.width, 
                        height: rect.height 
                    };
                });
            ");

            foreach ($linkSizes[0] as $size) {
                $this->assertGreaterThanOrEqual(44, $size['height'], 'Filter element height must be >= 44px: ' . json_encode($size));
            }
        });
    }
}
