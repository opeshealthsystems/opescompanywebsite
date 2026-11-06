<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\PartnerInstitution;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PublicSeoAndAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_login_and_legacy_locale_redirects(): void
    {
        $this->get('/login')->assertOk()->assertDontSee('demo1234')->assertDontSee('Demo logins')
            ->assertSee('images/opes-seal.png', false)->assertSee('noindex, follow');
        $this->get('/admin/login')->assertRedirect('/login');
        foreach (['en', 'fr'] as $locale) {
            $this->get('/'.$locale.'/login')->assertStatus(301)->assertRedirect('/login');
            $this->get('/'.$locale.'/practitioners/register')->assertStatus(301)->assertRedirect('/register');
        }
    }

    public function test_admin_can_reset_password_and_use_shared_login(): void
    {
        Notification::fake();
        Role::findOrCreate('admin', 'web');
        $user = User::factory()->create(['email' => 'admin@opeshealthsystems.com', 'is_active' => true]);
        $user->assignRole('admin');
        $this->post('/forgot-password', ['email' => $user->email])->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPasswordNotification::class);
        $token = Password::createToken($user);
        $this->post('/reset-password', ['email' => $user->email, 'token' => $token,
            'password' => 'Owner-created-test-password!', 'password_confirmation' => 'Owner-created-test-password!'])
            ->assertRedirect('/login');
        $this->assertTrue(Hash::check('Owner-created-test-password!', $user->fresh()->password));
        $this->post('/login', ['email' => $user->email, 'password' => 'Owner-created-test-password!'])->assertRedirect('/admin');
    }

    public function test_inactive_admin_cannot_access_panel(): void
    {
        Role::findOrCreate('admin', 'web');
        $user = User::factory()->create(['is_active' => false]);
        $user->assignRole('admin');
        $this->assertFalse($user->canAccessPanel(Panel::make()->id('admin')));
    }

    public function test_faq_schema_matches_visible_answers_and_has_no_numeric_sla(): void
    {
        foreach (['en', 'fr'] as $locale) {
            $response = $this->get('/'.$locale.'/faq')->assertOk()->assertDontSee('99.9%');
            preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $response->getContent(), $blocks);
            $schema = collect($blocks[1])->map(fn ($json) => json_decode($json, true))->firstWhere('@type', 'FAQPage');
            $this->assertSame('https://schema.org', $schema['@context']);
            $this->assertCount(11, $schema['mainEntity']);
            $response->assertSee($locale === 'fr' ? 'ambition panafricaine' : 'Africa-wide ambition');
            foreach ($schema['mainEntity'] as $answer) {
                $response->assertSee($answer['name'])->assertSee($answer['acceptedAnswer']['text']);
            }
        }
    }

    public function test_titles_are_distinct_and_brand_suffix_is_not_repeated(): void
    {
        $about = $this->get('/en/about')->assertOk()->getContent();
        preg_match('/<title>(.*?)<\/title>/s', $about, $title);
        $this->assertStringNotContainsString('— OPES Health Systems — OPES Health Systems', $title[1]);
        $this->assertSame(1, substr_count($title[1], 'OPES Health Systems'));
        $this->get('/fr/products/opescare')->assertOk()->assertSee('Présentation du logiciel');
        $this->get('/en/blog')->assertOk()->assertSee('Digital health articles and insights')->assertSee('noindex, follow');
        $this->get('/en/blog?search=test')->assertOk()->assertSee('noindex, follow');
    }

    public function test_sitemap_expands_public_landing_pages_and_omits_empty_blog(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertSee('/en/faq', false)->assertSee('/fr/privacy', false)
            ->assertSee('/en/markets/gabon', false)->assertDontSee('/en/blog', false)->assertDontSee('/login', false);
    }

    public function test_www_get_redirects_to_single_production_origin(): void
    {
        $this->app->instance('env', 'production');
        $this->get('https://www.opeshealthsystems.com/en/products?x=1')
            ->assertStatus(301)->assertRedirect('https://opeshealthsystems.com/en/products?x=1');
    }

    public function test_reading_article_does_not_change_editorial_last_modified_date(): void
    {
        $post = BlogPost::factory()->create(['published' => true, 'published_at' => now()->subDay()]);
        $modified = $post->updated_at->toIso8601String();
        $this->travel(2)->minutes();
        $this->get('/en/blog/'.$post->slug)->assertOk();
        $this->assertSame($modified, $post->fresh()->updated_at->toIso8601String());
        $this->assertSame(1, $post->fresh()->views);
    }

    public function test_future_articles_are_not_public(): void
    {
        $post = BlogPost::factory()->create(['published' => true, 'published_at' => now()->addDay()]);
        $this->get('/en/blog/'.$post->slug)->assertNotFound();
        $this->get('/sitemap.xml')->assertDontSee($post->slug);
    }

    public function test_editorial_and_drugtrace_publication_is_idempotent(): void
    {
        $this->seed(\Database\Seeders\PublicEditorialSeeder::class);
        $this->seed(\Database\Seeders\PublicEditorialSeeder::class);
        $this->assertSame(3, BlogPost::published()->count());
        $this->seed(\Database\Seeders\DrugTraceProductSeeder::class);
        $this->seed(\Database\Seeders\DrugTraceProductSeeder::class);
        $this->assertSame(23, \App\Models\Product::publicCatalogue()->count());
        $this->get('/en')->assertOk()->assertSee('/en/blog/planning-a-healthcare-software-implementation', false);
        $this->get('/fr/products/opes-drugtrace')->assertOk()->assertSee('Disponible');
        $this->get('/en/products')->assertOk()->assertSee('Medicine Safety');
        $this->get('/sitemap.xml')->assertOk()->assertSee('/fr/products/opes-drugtrace', false);
    }

    public function test_failed_reset_transport_returns_accurate_feedback(): void
    {
        Password::shouldReceive('sendResetLink')->once()->andThrow(new \Symfony\Component\Mailer\Exception\TransportException('SMTP unavailable'));
        $this->from('/forgot-password')->post('/forgot-password', ['email' => 'admin@opeshealthsystems.com'])
            ->assertRedirect('/forgot-password')->assertSessionHasErrors('email');
    }

    public function test_french_catalogue_cards_have_translations(): void
    {
        $slugs = \App\Models\Product::publicCatalogue()->pluck('slug')->all();
        $this->assertSame([], array_values(array_diff($slugs, array_keys(config('product_copy_fr')))));
        $this->get('/fr/products')->assertOk()->assertSee('Dossier médical électronique')->assertDontSee('Health ID and interoperability for participating');
        $this->get('/fr/blog')->assertOk()->assertSee('Aucun article publié pour le moment');
        $this->get('/fr/compliance')->assertOk()->assertDontSee('Certifié pour')->assertDontSee('99,9');
    }
}
