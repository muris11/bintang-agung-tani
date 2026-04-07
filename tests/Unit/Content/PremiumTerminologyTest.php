<?php

namespace Tests\Unit\Content;

use Tests\TestCase;

class PremiumTerminologyTest extends TestCase
{
    /** @test */
    public function no_premium_terminology_in_blade_templates()
    {
        $viewsPath = base_path('resources/views');
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($viewsPath)
        );
        
        $violations = [];
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                
                // Check for "Premium" in various cases
                if (stripos($content, 'Premium Experience') !== false) {
                    $violations[] = "Found 'Premium Experience' in {$file->getPathname()}";
                }
                if (stripos($content, 'Upgrade to Premium') !== false) {
                    $violations[] = "Found 'Upgrade to Premium' in {$file->getPathname()}";
                }
                if (preg_match('/class="[^"]*card-premium[^"]*"/', $content)) {
                    $violations[] = "Found '.card-premium' class in {$file->getPathname()}";
                }
                if (preg_match('/class="[^"]*glass-premium[^"]*"/', $content)) {
                    $violations[] = "Found '.glass-premium' class in {$file->getPathname()}";
                }
            }
        }
        
        $this->assertEmpty($violations, "Found Premium terminology:\n" . implode("\n", $violations));
    }

    /** @test */
    public function no_premium_class_names_in_css()
    {
        $cssPath = base_path('resources/css/app.css');
        $content = file_get_contents($cssPath);
        
        $violations = [];
        
        if (strpos($content, '.card-premium') !== false) {
            $violations[] = "Found '.card-premium' class - use semantic naming instead";
        }
        
        if (strpos($content, '.glass-premium') !== false) {
            $violations[] = "Found '.glass-premium' class - already removed";
        }
        
        if (strpos($content, '.input-premium') !== false) {
            $violations[] = "Found '.input-premium' class - use semantic naming instead";
        }
        
        if (strpos($content, '.shadow-premium') !== false) {
            $violations[] = "Found '.shadow-premium' class - should be renamed";
        }
        
        $this->assertEmpty($violations, "Found Premium class names:\n" . implode("\n", $violations));
    }
}
