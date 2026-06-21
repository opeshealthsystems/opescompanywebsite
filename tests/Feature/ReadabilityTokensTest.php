<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReadabilityTokensTest extends TestCase
{
    private function appCss(): string
    {
        return file_get_contents(base_path('resources/css/app.css'));
    }

    /** SP1-scoped Blade files (public pages + shared public chrome). */
    private function sp1Files(): array
    {
        $files = array_merge(
            glob(base_path('resources/views/pages/*.blade.php')) ?: [],
            glob(base_path('resources/views/pages/markets/*.blade.php')) ?: [],
            [
                base_path('resources/views/components/navbar.blade.php'),
                base_path('resources/views/components/footer.blade.php'),
                base_path('resources/views/components/language-switcher.blade.php'),
                base_path('resources/views/components/layouts/app.blade.php'),
            ],
        );

        return array_values(array_filter($files, 'is_file'));
    }

    public function test_root_tokens_are_defined(): void
    {
        $css = $this->appCss();
        foreach ([
            '--text: #e8edf5', '--text-2: #c2cde0', '--text-muted: #9fb0c9', '--text-faint: #8696b4',
            '--bg: #0F172A', '--border: #243149',
            '--green: #00C896', '--blue: #2f7df0',
            '--fs-2xs: 12px', '--fs-xs: 13px', '--fs-sm: 14px',
        ] as $token) {
            $this->assertStringContainsString($token, $css, "Missing token: {$token}");
        }
    }
}
