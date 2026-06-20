<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_blog_index_accepts_valid_category(): void
    {
        BlogPost::factory()->create(['published' => true, 'category' => 'tech']);
        $response = $this->get('/en/blog?category=tech');
        $response->assertStatus(200);
    }

    public function test_blog_index_rejects_invalid_category(): void
    {
        BlogPost::factory()->create(['published' => true, 'category' => 'tech']);
        $response = $this->get('/en/blog?category=nonexistent_hack');
        $response->assertStatus(422);
    }

    public function test_blog_index_rejects_overlong_search(): void
    {
        $response = $this->get('/en/blog?search=' . str_repeat('a', 256));
        $response->assertStatus(422);
    }

    public function test_blog_index_accepts_valid_search(): void
    {
        $response = $this->get('/en/blog?search=health');
        $response->assertStatus(200);
    }

    public function test_blog_index_loads_without_params(): void
    {
        $response = $this->get('/en/blog');
        $response->assertStatus(200);
    }

    /**
     * The available-categories allowlist is built from a SELECT DISTINCT query.
     * Because the published() scope orders by published_at (a column NOT in the
     * SELECT list), MySQL rejects the query unless the order is cleared. This
     * guards that the distinct-category query executes and includes every
     * published category. (On SQLite the strictness isn't enforced; the dev
     * MySQL behaviour was verified directly via tinker.)
     */
    public function test_blog_index_distinct_categories_allowlist_covers_every_category(): void
    {
        BlogPost::factory()->create(['published' => true, 'category' => 'tech', 'published_at' => now()->subDay()]);
        BlogPost::factory()->create(['published' => true, 'category' => 'health', 'published_at' => now()]);

        // Each category must survive the DISTINCT allowlist (otherwise → 422).
        $this->get('/en/blog?category=tech')->assertStatus(200);
        $this->get('/en/blog?category=health')->assertStatus(200);
        $this->get('/en/blog')->assertStatus(200);
    }
}
