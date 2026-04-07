<?php

namespace Tests\Browser\Responsive;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthLayoutTest extends DuskTestCase
{
    /**
     * Test that login page loads successfully on mobile viewport
     */
    public function test_auth_form_is_centered_on_mobile()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->resize(375, 812)
                ->pause(500);
            
            // Take screenshot for visual verification
            $browser->screenshot('login-mobile-375x812');
            
            // Assert form is present
            $browser->assertPresent('form');
            
            // Assert form container has responsive classes
            $browser->assertPresent('.max-w-md');
            
            // Assert the page has proper mobile padding classes
            $pageSource = $browser->driver->getPageSource();
            $this->assertStringContainsString('px-4', $pageSource);
            $this->assertStringContainsString('sm:px-6', $pageSource);
            $this->assertStringContainsString('flex-col', $pageSource);
            $this->assertStringContainsString('lg:flex-row', $pageSource);
        });
    }
    
    /**
     * Test that register page loads successfully on mobile viewport
     */
    public function test_register_form_is_centered_on_mobile()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->resize(375, 812)
                ->pause(500);
            
            // Take screenshot for visual verification
            $browser->screenshot('register-mobile-375x812');
            
            // Assert form is present
            $browser->assertPresent('form');
            
            // Assert form container has responsive classes
            $browser->assertPresent('.max-w-md');
        });
    }
    
    /**
     * Test that forms are properly contained on small screens
     */
    public function test_login_form_is_properly_contained_on_small_screens()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->resize(320, 568)
                ->pause(500);
            
            // Take screenshot for visual verification
            $browser->screenshot('login-small-320x568');
            
            // Assert form is present
            $browser->assertPresent('form');
            
            // Verify responsive classes exist in page source
            $pageSource = $browser->driver->getPageSource();
            $this->assertStringContainsString('max-w-md', $pageSource);
        });
    }
}
