<?php

namespace Tests\Feature;

use Tests\TestCase;

class SealPaletteTest extends TestCase
{
    private function luminance(string $hex): float
    {
        $rgb = array_map(function ($value) {
            $n = hexdec($value) / 255;
            return $n <= .04045 ? $n / 12.92 : (($n + .055) / 1.055) ** 2.4;
        }, str_split(ltrim($hex, '#'), 2));
        return $rgb[0] * .2126 + $rgb[1] * .7152 + $rgb[2] * .0722;
    }

    public function test_seal_combinations_meet_text_and_control_contrast(): void
    {
        foreach ([['#FFFFFF', '#315F7D', 4.5], ['#FFFFFF', '#244A63', 4.5],
            ['#A7B8CC', '#16354F', 4.5], ['#9DB1C7', '#16354F', 4.5],
            ['#69D5F5', '#16354F', 4.5], ['#7890AA', '#16354F', 3],
            ['#102A43', '#FFFFFF', 4.5], ['#425A70', '#F3F6FA', 4.5], ['#075985', '#FFFFFF', 4.5]] as [$fg, $bg, $minimum]) {
            $a = $this->luminance($fg); $b = $this->luminance($bg);
            $this->assertGreaterThanOrEqual($minimum, (max($a, $b) + .05) / (min($a, $b) + .05));
        }
    }

    public function test_app_controls_have_no_gradient_or_shadow_decorations(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));
        $this->assertDoesNotMatchRegularExpression('/(?:linear|radial)-gradient\(/i', $css);
        $this->assertDoesNotMatchRegularExpression('/(?:box-shadow|text-shadow)\s*:(?!\s*none\b)[^;{}]+/i', $css);
        $this->assertStringNotContainsString('-webkit-text-fill-color: transparent', $css);
        $this->assertStringContainsString("@import './seal-tokens.css'", $css);
    }
}
