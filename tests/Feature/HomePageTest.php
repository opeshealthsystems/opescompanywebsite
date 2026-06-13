<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_english_hero_renders(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('Software that powers African healthcare')
            ->assertSee('Products'); // nav in English
    }

    public function test_french_hero_renders(): void
    {
        $this->get('/fr')
            ->assertOk()
            ->assertSee('Des logiciels au service de la santé africaine', false)
            ->assertSee('Produits'); // nav in French
    }

    public function test_homepage_has_hreflang_alternates(): void
    {
        $this->get('/en')
            ->assertSee('hreflang="en"', false)
            ->assertSee('hreflang="fr"', false)
            ->assertSee(url('/fr'), false);
    }
}
