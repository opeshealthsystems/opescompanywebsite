<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        // MySQL refuses to TRUNCATE a table referenced by a foreign key
        // (blog_comments → blog_posts, error 1701); disable FK checks for the reset.
        Schema::withoutForeignKeyConstraints(fn () => BlogPost::truncate());

        $articlesPath = base_path('content/articles');

        // English articles only — "*.fr.md" are French companions loaded into the *_fr columns.
        $files = collect(glob($articlesPath . '/*.md'))
            ->reject(fn ($p) => str_ends_with($p, '.fr.md'))
            ->sort()
            ->values();

        // Spread published dates evenly from 2025-08-04 up to a few days ago, so posts
        // always have realistic past dates (never future-dated, however many articles exist).
        $startDate = Carbon::parse('2025-08-04');
        $endDate   = Carbon::today()->subDays(2);
        $spanDays  = max(1, $startDate->diffInDays($endDate));
        $lastIndex = max(1, $files->count() - 1);

        foreach ($files as $index => $filePath) {
            [$title, $excerpt, $body] = $this->parseArticle(file_get_contents($filePath));

            // --- Slug = filename without .md extension ---
            $slug = pathinfo($filePath, PATHINFO_FILENAME);

            // --- French companion ("NN-slug.fr.md") → title_fr / excerpt_fr / body_fr ---
            $frPath = preg_replace('/\.md$/', '.fr.md', $filePath);
            $titleFr = $excerptFr = $bodyFr = null;
            if (is_file($frPath)) {
                [$titleFr, $excerptFr, $bodyFr] = $this->parseArticle(file_get_contents($frPath));
            }

            // --- Assign category by article number ---
            $num = (int) basename($filePath); // "01-..." → 1, "71-..." → 71
            $category = match (true) {
                $num <= 7  => 'Digital Health in Cameroon',
                $num <= 15 => 'Healthcare Challenges',
                $num <= 23 => 'HMS Solutions',
                $num <= 30 => "Buyer's Guide",
                $num <= 35 => 'AI & Technology',
                $num <= 41 => 'Insights & Case Studies',
                $num <= 53 => 'HMS Solutions',               // Clinical modules & disease programmes (42–53)
                $num <= 65 => "Buyer's Guide",               // Technical & operational buyer content (54–65)
                $num <= 70 => 'Digital Health in Cameroon',  // CEMAC country articles (66–70)
                $num === 71 => 'HMS Solutions',              // Mobile money payments
                $num === 72 => 'Digital Health in Cameroon', // Universal Health Coverage (CSU)
                $num === 73 => 'Healthcare Challenges',      // Counterfeit medicines
                $num === 74 => 'HMS Solutions',              // TB digital case management
                $num === 75 => 'Insights & Case Studies',   // Major hospitals list
                $num === 76 => "Buyer's Guide",              // CENAME / drug supply chain
                $num === 77 => "Buyer's Guide",              // Health insurance explainer
                $num === 78 => 'HMS Solutions',              // Disease surveillance (IDSR)
                $num === 79 => "Buyer's Guide",              // Data-protection law
                $num === 80 => "Buyer's Guide",              // Funding hospital digitisation
                $num === 81 => "Buyer's Guide",              // Open-source vs commercial HMS
                $num === 82 => 'HMS Solutions',              // Faith-based / mission hospitals
                $num === 83 => 'HMS Solutions',              // District hospitals & health centres
                $num === 84 => 'Digital Health in Cameroon', // Patient guide: booking & records
                $num === 85 => "Buyer's Guide",              // ICD-10 / ICD-11 coding standards
                $num === 86 => 'Digital Health in Cameroon', // Govt digitalisation plan + OPES contribution
                default    => 'Insights & Case Studies',
            };

            BlogPost::create([
                'title'        => $title,
                'title_fr'     => $titleFr,
                'slug'         => $slug,
                'excerpt'      => $excerpt,
                'excerpt_fr'   => $excerptFr,
                'body'         => $body,
                'body_fr'      => $bodyFr,
                'reading_time' => BlogPost::calculateReadingTime($body),
                'category'     => $category,
                'author'       => 'OPES Health Systems',
                'published'    => true,
                'published_at' => (clone $startDate)->addDays((int) round($index * $spanDays / $lastIndex)),
            ]);
        }

        $this->command->info("Seeded {$files->count()} blog posts.");
    }

    /**
     * Parse an article markdown file into [title, excerpt, body(html)].
     * Format: "# Title\n\n**Meta Description:** ...\n\n**Target Keywords:** ...\n\n---\n\n<body>".
     */
    private function parseArticle(string $raw): array
    {
        // --- Title from the first # heading ---
        preg_match('/^#\s+(.+)$/m', $raw, $titleMatch);
        $title = trim($titleMatch[1] ?? 'Untitled');

        // --- Excerpt from the meta description ---
        preg_match('/\*\*Meta Description:\*\*\s*(.+)$/m', $raw, $excerptMatch);
        $excerpt = Str::limit(trim($excerptMatch[1] ?? ''), 250, '');

        // --- Strip the frontmatter block before converting the body to HTML ---
        $body = preg_replace(
            '/^#[^\n]+\n+\*\*Meta Description:\*\*[^\n]+\n+\*\*Target Keywords:\*\*[^\n]+\n+---+\n+/s',
            '',
            $raw,
            1
        );
        $body = Str::markdown($body ?? $raw, ['html_input' => 'allow']);

        return [$title, $excerpt, $body];
    }
}
