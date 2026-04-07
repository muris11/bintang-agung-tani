<?php

namespace Tests\Unit\Design;

use Tests\TestCase;

class ShadowTest extends TestCase
{
    /** @test */
    public function no_colored_shadows_in_css()
    {
        $content = file_get_contents(base_path('resources/css/app.css'));
        
        $violations = [];
        
        if (strpos($content, 'shadow-emerald') !== false) {
            $violations[] = "Found 'shadow-emerald' - colored shadows should not be used";
        }
        
        if (strpos($content, 'shadow-primary') !== false) {
            $violations[] = "Found 'shadow-primary' - colored shadows should not be used";
        }
        
        if (preg_match('/box-shadow:.*rgba\([^)]*16.*185.*129/i', $content)) {
            $violations[] = "Found emerald color in box-shadow (rgba with 16, 185, 129)";
        }
        
        $this->assertEmpty($violations, "Found colored shadows:\n" . implode("\n", $violations));
    }

    /** @test */
    public function uses_standard_shadow_tokens_only()
    {
        $content = file_get_contents(base_path('resources/css/tokens.css'));
        
        // Check that shadow tokens exist
        $this->assertStringContainsString('--shadow-sm:', $content);
        $this->assertStringContainsString('--shadow-md:', $content);
        $this->assertStringContainsString('--shadow-lg:', $content);
        
        // Check that colored shadows don't exist in tokens
        $this->assertStringNotContainsString('--shadow-primary:', $content);
        $this->assertStringNotContainsString('--shadow-colored:', $content);
    }

    /** @test */
    public function no_colored_shadows_in_blade_templates()
    {
        $viewsPath = base_path('resources/views');
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($viewsPath)
        );
        
        $violations = [];
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                
                if (preg_match('/shadow-\w+-\d+\/\d+/', $content, $matches)) {
                    // Check if it's a colored shadow (not standard gray/black)
                    if (strpos($matches[0], 'shadow-gray') === false && 
                        strpos($matches[0], 'shadow-black') === false &&
                        strpos($matches[0], 'shadow-white') === false) {
                        $violations[] = "Found colored shadow '{$matches[0]}' in {$file->getPathname()}";
                    }
                }
            }
        }
        
        $this->assertEmpty($violations, "Found colored shadows in views:\n" . implode("\n", $violations));
    }
}
