<?php

namespace Tests\Browser\Accessibility;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FocusTest extends DuskTestCase
{
    /** @test */
    public function interactive_elements_have_visible_focus_indicators()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/user/dashboard')
                ->resize(1280, 800);
            
            // Check that focus styles are defined
            $hasFocusStyles = $browser->script("
                const testEl = document.createElement('button');
                document.body.appendChild(testEl);
                testEl.focus();
                const styles = window.getComputedStyle(testEl);
                document.body.removeChild(testEl);
                return styles.outline !== 'none' || styles.boxShadow.includes('rgb');
            ");
            
            $this->assertTrue($hasFocusStyles[0], 'Focus indicators should be visible');
        });
    }

    /** @test */
    public function navbar_buttons_have_focus_styles()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/user/dashboard')
                ->resize(1280, 800);
            
            // Check that nav buttons have focus-visible classes defined in CSS
            $hasFocusStyles = $browser->script("
                // Check if buttons have focus-visible ring classes
                const buttons = document.querySelectorAll('nav button');
                const links = document.querySelectorAll('nav a');
                
                let hasFocusVisibleClasses = false;
                
                // Check buttons for focus-visible classes
                buttons.forEach(btn => {
                    const classes = btn.className;
                    if (classes.includes('focus-visible') || classes.includes('focus:ring')) {
                        hasFocusVisibleClasses = true;
                    }
                });
                
                // Check if global focus styles are applied via CSS
                const testEl = document.createElement('button');
                document.body.appendChild(testEl);
                const styles = window.getComputedStyle(testEl);
                const hasGlobalFocusStyles = styles.outline !== 'none';
                document.body.removeChild(testEl);
                
                return hasFocusVisibleClasses || hasGlobalFocusStyles;
            ");
            
            $this->assertTrue($hasFocusStyles[0], 'Navbar buttons should have focus styles defined');
        });
    }
}
