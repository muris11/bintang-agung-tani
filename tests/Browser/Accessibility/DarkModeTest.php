<?php

namespace Tests\Browser\Accessibility;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DarkModeTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Reset theme state before each test.
     */
    protected function resetTheme(Browser $browser): void
    {
        $browser->script("
            localStorage.clear();
            document.documentElement.removeAttribute('data-theme');
        ");
    }

    /** @test */
    public function dark_mode_toggle_exists()
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/user/dashboard');
            
            $this->resetTheme($browser);
            
            $browser->assertPresent('[data-testid="theme-toggle"]');
        });
    }

    /** @test */
    public function clicking_toggle_switches_to_dark_mode()
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/user/dashboard');
            
            // Reset to light mode explicitly
            $this->resetTheme($browser);
            $browser->pause(200);
            
            $browser->click('[data-testid="theme-toggle"]')
                ->pause(500);
            
            // Use JavaScript to check the html element attribute
            $hasDarkTheme = $browser->script("
                return document.documentElement.getAttribute('data-theme') === 'dark';
            ");
            
            $this->assertTrue($hasDarkTheme[0], 'HTML element should have data-theme="dark" attribute');
        });
    }

    /** @test */
    public function theme_persists_after_page_reload()
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/user/dashboard');
            
            // Reset to known state (light mode)
            $this->resetTheme($browser);
            $browser->pause(200);
            
            // Check current state - click until we get to dark mode
            $attempts = 0;
            $maxAttempts = 2;
            
            while ($attempts < $maxAttempts) {
                // Check if we're in dark mode
                $currentTheme = $browser->script("
                    return localStorage.getItem('theme');
                ");
                
                if (($currentTheme[0] ?? null) === 'dark') {
                    break;
                }
                
                // Click toggle to try to get to dark mode
                $browser->click('[data-testid="theme-toggle"]')
                    ->pause(500);
                
                $attempts++;
            }
            
            // Verify we're now in dark mode
            $themeBefore = $browser->script("
                return {
                    localStorage: localStorage.getItem('theme'),
                    dataTheme: document.documentElement.getAttribute('data-theme')
                };
            ");
            
            $this->assertEquals('dark', $themeBefore[0]['localStorage'] ?? null, 'localStorage theme should be dark before refresh');
            $this->assertEquals('dark', $themeBefore[0]['dataTheme'] ?? null, 'data-theme attribute should be dark before refresh');
            
            // Refresh the page
            $browser->refresh()
                ->pause(1500);
            
            // Check localStorage value persisted after reload
            $localStorageValue = $browser->script("
                return localStorage.getItem('theme');
            ");
            $this->assertEquals('dark', $localStorageValue[0], 'localStorage should have theme set to dark after reload');
            
            // Check data-theme attribute was restored after reload
            $hasDarkTheme = $browser->script("
                return document.documentElement.getAttribute('data-theme') === 'dark';
            ");
            
            $this->assertTrue($hasDarkTheme[0], 'HTML element should have data-theme="dark" after reload');
        });
    }
}
