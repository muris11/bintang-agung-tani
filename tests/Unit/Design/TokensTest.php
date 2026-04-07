<?php

namespace Tests\Unit\Design;

use Tests\TestCase;

class TokensTest extends TestCase
{
    /** @test */
    public function design_tokens_css_file_exists()
    {
        $this->assertFileExists(base_path('resources/css/tokens.css'));
    }

    /** @test */
    public function tokens_define_color_primary()
    {
        $content = file_get_contents(base_path('resources/css/tokens.css'));
        $this->assertStringContainsString('--color-primary:', $content);
    }

    /** @test */
    public function tokens_define_color_surface()
    {
        $content = file_get_contents(base_path('resources/css/tokens.css'));
        $this->assertStringContainsString('--color-surface:', $content);
    }

    /** @test */
    public function tokens_define_spacing_scale()
    {
        $content = file_get_contents(base_path('resources/css/tokens.css'));
        $this->assertStringContainsString('--space-', $content);
    }

    /** @test */
    public function tokens_define_shadow_scale()
    {
        $content = file_get_contents(base_path('resources/css/tokens.css'));
        $this->assertStringContainsString('--shadow-sm:', $content);
    }
}
