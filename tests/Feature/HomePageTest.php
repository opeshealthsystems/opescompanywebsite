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
            ->assertSee('22 bilingual, interoperable systems for hospitals, clinics, and health systems across Cameroon, CEMAC, and Africa.')
            ->assertSee('Book a Free Demo')
            ->assertSee(url('/en/contact'), false)
            ->assertSee('Products'); // nav in English
    }

    public function test_french_hero_renders(): void
    {
        $this->get('/fr')
            ->assertOk()
            ->assertSee('Des logiciels au service de la santé africaine', false)
            ->assertSee('22 systèmes bilingues et interopérables pour les hôpitaux, cliniques et systèmes de santé au Cameroun, en CEMAC et en Afrique.', false)
            ->assertSee('Demander une démo gratuite', false)
            ->assertSee(url('/fr/contact'), false)
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
