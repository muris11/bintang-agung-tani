<?php

namespace Tests\Unit\Views;

use Tests\TestCase;

class NavigationTest extends TestCase
{
    public function test_no_hardcoded_user_urls_in_views()
    {
        $viewsPath = resource_path('views/user');
        $files = glob("{$viewsPath}/*.blade.php");
        
        $hardcodedPatterns = [
            'href="/user/',
            "href='/user/",
            'action="/user/',
        ];
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            foreach ($hardcodedPatterns as $pattern) {
                $this->assertStringNotContainsString(
                    $pattern,
                    $content,
                    "File {$file} contains hardcoded URL: {$pattern}"
                );
            }
        }
    }
}
