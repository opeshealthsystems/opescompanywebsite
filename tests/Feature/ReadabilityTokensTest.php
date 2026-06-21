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

    public function test_app_css_has_no_faint_text_hexes(): void
    {
        $css = $this->appCss();
        foreach (['#475569', '#64748b', '#94a3b8'] as $hex) {
            $this->assertStringNotContainsString($hex, $css, "Faint hex still in app.css: {$hex}");
        }
    }

    public function test_app_css_has_no_sub_13_font_sizes(): void
    {
        $css = $this->appCss();
        foreach (['font-size: 9px', 'font-size: 9.5px', 'font-size: 10px', 'font-size: 11px', 'font-size: 12px', 'font-size: 12.5px'] as $bad) {
            $this->assertStringNotContainsString($bad, $css, "Tiny size still in app.css: {$bad}");
        }
    }
}
