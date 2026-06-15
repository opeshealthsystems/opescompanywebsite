<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        BlogPost::truncate();

        $articlesPath = base_path('content/articles');
        $files = collect(glob($articlesPath . '/*.md'))->sort()->values();

        // Spread published dates weekly from 2025-08-04 forward
        $startDate = Carbon::parse('2025-08-04');

        foreach ($files as $index => $filePath) {
            $raw = file_get_contents($filePath);

            // --- Extract title from first # heading ---
            preg_match('/^#\s+(.+)$/m', $raw, $titleMatch);
            $title = trim($titleMatch[1] ?? 'Untitled');

            // --- Extract meta description as excerpt ---
            preg_match('/\*\*Meta Description:\*\*\s*(.+)$/m', $raw, $excerptMatch);
            $excerpt = Str::limit(trim($excerptMatch[1] ?? ''), 250, '');

            // --- Slug = filename without .md extension ---
            $slug = pathinfo($filePath, PATHINFO_FILENAME);

            // --- Strip frontmatter block before converting body to HTML ---
            // Removes: # Title\n\n**Meta Description:**...\n\n**Target Keywords:**...\n\n---\n\n
            $body = preg_replace(
                '/^#[^\n]+\n+\*\*Meta Description:\*\*[^\n]+\n+\*\*Target Keywords:\*\*[^\n]+\n+---+\n+/s',
                '',
                $raw,
                1
            );
            $body = Str::markdown($body ?? $raw, ['html_input' => 'allow']);

            // --- Assign category by article number ---
            $num = (int) basename($filePath); // "01-..." → 1, "41-..." → 41
            $category = match (true) {
                $num <= 7  => 'Digital Health in Cameroon',
                $num <= 15 => 'Healthcare Challenges',
                $num <= 23 => 'HMS Solutions',
                $num <= 30 => "Buyer's Guide",
                $num <= 35 => 'AI & Technology',
                $num <= 41 => 'Insights & Case Studies',
                $num <= 53 => 'HMS Solutions',              // Clinical modules & disease programmes (42–53)
                $num <= 65 => "Buyer's Guide",              // Technical & operational buyer content (54–65)
                $num <= 70 => 'Digital Health in Cameroon', // CEMAC country articles (66–70)
                default    => 'Insights & Case Studies',
            };

            BlogPost::create([
                'title'        => $title,
                'slug'         => $slug,
                'excerpt'      => $excerpt,
                'body'         => $body,
                'reading_time' => BlogPost::calculateReadingTime($body),
                'category'     => $category,
                'author'       => 'OPES Health Systems',
                'published'    => true,
                'published_at' => (clone $startDate)->addWeeks($index),
            ]);
        }

        $this->command->info("Seeded {$files->count()} blog posts.");
    }
}
