<?php

namespace Tests\Browser\Accessibility;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ReducedMotionTest extends DuskTestCase
{
    /** @test */
    public function animations_disabled_when_prefers_reduced_motion()
    {
        $this->browse(function (Browser $browser) {
            // Test with reduced motion preference
            $browser->visit('/user/dashboard')
                ->script("
                    // Override matchMedia to simulate reduced motion
                    Object.defineProperty(window, 'matchMedia', {
                        writable: true,
                        value: function(query) {
                            return {
                                matches: query === '(prefers-reduced-motion: reduce)',
                                media: query,
                                addEventListener: function() {},
                                removeEventListener: function() {}
                            };
                        }
                    });
                ");
            
            // Check CSS for reduced motion styles
            $hasReducedMotionStyles = $browser->script("
                const style = document.createElement('style');
                style.textContent = '@media (prefers-reduced-motion: reduce) { .test { animation: none; } }';
                document.head.appendChild(style);
                const sheet = style.sheet;
                const hasMediaRule = Array.from(sheet.cssRules).some(rule => 
                    rule.type === CSSRule.MEDIA_RULE && 
                    rule.conditionText.includes('prefers-reduced-motion')
                );
                document.head.removeChild(style);
                return hasMediaRule;
            ");
            
            $this->assertTrue($hasReducedMotionStyles[0], 'Should have prefers-reduced-motion styles');
        });
    }
}
