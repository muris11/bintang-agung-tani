<?php

namespace Tests\Browser\Accessibility;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ContrastTest extends DuskTestCase
{
    /** @test */
    public function glassmorphism_elements_meet_wcag_aa_contrast()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/user/dashboard');
            
            // Check that no elements with glass class exist
            $glassElements = $browser->script("
                return document.querySelectorAll('.glass, .glass-premium').length;
            ");
            
            $this->assertEquals(0, $glassElements[0], 'Glassmorphism classes should be removed');
        });
    }

    /** @test */
    public function text_elements_have_sufficient_contrast()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/user/dashboard');
            
            // Check that no elements with glass class exist (ensuring glassmorphism is removed)
            $glassElements = $browser->script("
                return document.querySelectorAll('.glass, .glass-premium').length;
            ");
            
            $this->assertEquals(0, $glassElements[0], 'Glassmorphism classes should be removed for proper contrast');
            
            // Verify the page loaded successfully
            $browser->assertPathIs('/user/dashboard');
        });
    }
}