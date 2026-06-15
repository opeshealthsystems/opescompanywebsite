<?php

namespace Tests\Feature;

use Database\Seeders\ProductSeeder;
use Database\Seeders\TestimonialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(ProductSeeder::class);
        $this->seed(TestimonialSeeder::class);
    }

    public function test_english_hero_renders(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('Healthcare Ecosystem')
            ->assertSee('22 integrated software systems')
            ->assertSee('Explore Our Products')
            ->assertSee(url('/en/products'), false)
            ->assertSee('Products'); // nav in English
    }

    public function test_french_hero_renders(): void
    {
        $this->get('/fr')
            ->assertOk()
            ->assertSee('22 systèmes logiciels', false)
            ->assertSee('Découvrir nos produits', false)
            ->assertSee(url('/fr/products'), false)
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
