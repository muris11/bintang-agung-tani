<?php

namespace Tests\Unit\Accessibility;

use PHPUnit\Framework\TestCase;

class GlassmorphismRemovedTest extends TestCase
{
    private string $basePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->basePath = __DIR__ . '/../../../resources';
    }

    /** @test */
    public function glassmorphism_classes_removed_from_css()
    {
        $cssFile = $this->basePath . '/css/app.css';
        $cssContent = file_get_contents($cssFile);

        // Assert that old glassmorphism classes are removed (check for class definitions, not comments)
        $this->assertStringNotContainsString(".glass {\n", $cssContent, '.glass class definition should be removed');
        $this->assertStringNotContainsString('.glass-dark {', $cssContent, '.glass-dark class definition should be removed');
        $this->assertStringNotContainsString('.glass-strong {', $cssContent, '.glass-strong class definition should be removed');
        $this->assertStringNotContainsString('.glass-premium {', $cssContent, '.glass-premium class definition should be removed');
        $this->assertStringNotContainsString('backdrop-filter:', $cssContent, 'backdrop-filter should be removed');
        $this->assertStringNotContainsString('backdrop-blur', $cssContent, 'backdrop-blur should be removed');

        // Assert that accessible replacements exist
        $this->assertStringContainsString('/* REMOVED: Glassmorphism classes', $cssContent, 'Comment about glass removal should exist');
        $this->assertStringContainsString('.card-solid', $cssContent, '.card-solid class should exist');
        $this->assertStringContainsString('.navbar-solid', $cssContent, '.navbar-solid class should exist');
        $this->assertStringContainsString('.stat-card', $cssContent, '.stat-card class should exist');
    }

    /** @test */
    public function product_card_has_no_backdrop_blur()
    {
        $bladeFile = $this->basePath . '/views/components/product-card.blade.php';
        $content = file_get_contents($bladeFile);

        // Assert no backdrop-blur in product card
        $this->assertStringNotContainsString('backdrop-blur', $content, 'Product card should not have backdrop-blur');
        $this->assertStringNotContainsString('backdrop-blur-sm', $content, 'Product card should not have backdrop-blur-sm');
    }

    /** @test */
    public function dashboard_has_no_backdrop_blur()
    {
        $bladeFile = $this->basePath . '/views/user/dashboard.blade.php';
        $content = file_get_contents($bladeFile);

        // Assert no backdrop-blur or blur effects in dashboard
        $this->assertStringNotContainsString('backdrop-blur', $content, 'Dashboard should not have backdrop-blur');
        $this->assertStringNotContainsString('blur-3xl', $content, 'Dashboard should not have blur-3xl');
        $this->assertStringNotContainsString('blur-2xl', $content, 'Dashboard should not have blur-2xl');

        // Ensure solid backgrounds are used instead
        $this->assertStringContainsString('bg-emerald-50', $content, 'Should use solid emerald-50 background');
        $this->assertStringContainsString('bg-amber-50', $content, 'Should use solid amber-50 background');
    }
}