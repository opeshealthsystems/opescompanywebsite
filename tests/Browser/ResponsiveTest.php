<?php

namespace Tests\Browser;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\PractitionerProgram;
use App\Models\Product;
use App\Models\Survey;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Asserts there is NO horizontal overflow on every key route at common mobile,
 * tablet, and desktop widths. Runs a real headless Chrome via Dusk against the
 * seeded dev database. This is the automated guarantee that pages stay
 * responsive (a new unbreakpointed grid or fixed width will fail here).
 */
class ResponsiveTest extends DuskTestCase
{
    /** Widths: small phone, phone, large phone, tablet, small desktop. */
    private array $widths = [320, 375, 414, 768, 1024];

    /** Visit each URL once, then check overflow at every width. Collects all failures. */
    private function assertUrlsResponsive(Browser $browser, array $urls): void
    {
        $failures = [];

        foreach ($urls as $url) {
            $browser->visit($url)->pause(250);

            foreach ($this->widths as $w) {
                $browser->resize($w, 900)->pause(120);
                $over = (int) $browser->script(
                    'return Math.max(0, document.documentElement.scrollWidth - document.documentElement.clientWidth)'
                )[0];

                if ($over > 2) {
                    $failures[] = "{$url}  @ {$w}px  → +{$over}px";
                }
            }

            $browser->resize(1024, 900);
        }

        $this->assertEmpty(
            $failures,
            "Horizontal overflow detected on " . count($failures) . " page/width combos:\n  " . implode("\n  ", $failures)
        );
    }

    public function test_public_pages_have_no_overflow(): void
    {
        $product = Product::where('is_active', true)->value('slug');
        $course  = Course::where('is_active', true)->value('slug');
        $post    = BlogPost::query()->value('slug');

        $urls = array_values(array_filter([
            '/en', '/en/products',
            $product ? "/en/products/{$product}" : null,
            '/en/solutions', '/en/about', '/en/contact', '/en/pricing',
            '/en/blog', $post ? "/en/blog/{$post}" : null,
            '/en/partnerships', '/en/practitioners', '/en/courses',
            $course ? "/en/courses/{$course}" : null,
            '/en/privacy', '/en/terms', '/en/compliance',
        ]));

        $this->browse(function (Browser $browser) use ($urls) {
            $this->assertUrlsResponsive($browser, $urls);
        });
    }

    public function test_customer_portal_has_no_overflow(): void
    {
        $user = User::where('email', 'customer@demo.opes')->first();
        if (! $user) {
            $this->markTestSkipped('demo customer not seeded');
        }

        $survey = Survey::where('status', 'active')->whereIn('audience', ['customers', 'all'])->value('id');

        $urls = array_values(array_filter([
            '/en/customer/dashboard', '/en/customer/documents', '/en/customer/licenses',
            '/en/customer/invoices', '/en/customer/tickets', '/en/customer/tickets/create',
            '/en/customer/knowledge-base', '/en/customer/surveys',
            $survey ? "/en/customer/surveys/{$survey}" : null,
            '/en/customer/service-requests', '/en/customer/service-requests/create',
            '/en/customer/courses', '/en/customer/certificates', '/en/customer/profile',
        ]));

        $this->browse(function (Browser $browser) use ($user, $urls) {
            $browser->loginAs($user);
            $this->assertUrlsResponsive($browser, $urls);
        });
    }

    public function test_practitioner_portal_has_no_overflow(): void
    {
        $user = User::where('email', 'dr.demo@opes.test')->first();
        if (! $user) {
            $this->markTestSkipped('demo practitioner not seeded');
        }

        $program = PractitionerProgram::where('status', 'open')->value('id');

        $urls = array_values(array_filter([
            '/en/practitioner/dashboard', '/en/practitioner/programs',
            $program ? "/en/practitioner/programs/{$program}" : null,
            '/en/practitioner/applications', '/en/practitioner/surveys',
            '/en/practitioner/suggestions', '/en/practitioner/suggestions/create',
            '/en/practitioner/bug-reports', '/en/practitioner/bug-reports/create',
            '/en/practitioner/courses', '/en/practitioner/certificates', '/en/practitioner/profile',
        ]));

        $this->browse(function (Browser $browser) use ($user, $urls) {
            $browser->loginAs($user);
            $this->assertUrlsResponsive($browser, $urls);
        });
    }

    public function test_tester_portal_has_no_overflow(): void
    {
        $user = User::where('email', 'tester@demo.opes')->first();
        if (! $user) {
            $this->markTestSkipped('demo tester not seeded');
        }

        $urls = ['/en/tester/dashboard', '/en/tester/assignments'];

        $this->browse(function (Browser $browser) use ($user, $urls) {
            $browser->loginAs($user);
            $this->assertUrlsResponsive($browser, $urls);
        });
    }
}
