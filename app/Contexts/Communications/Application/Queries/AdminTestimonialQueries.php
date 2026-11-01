<?php

namespace App\Contexts\Communications\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Builder;

final class AdminTestimonialQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void { $u = $input->user(); abort_unless($u?->is_active && $u->hasAnyRole(['super_admin', 'admin']), 403); }
    private function item(Testimonial $t): array { return ['id' => $t->id, 'author_name' => $t->author_name, 'author_title' => $t->author_title, 'author_facility' => $t->author_facility, 'author_country' => $t->author_country, 'body' => $t->body, 'body_fr' => $t->body_fr, 'rating' => $t->rating, 'sort_order' => $t->sort_order, 'is_active' => (bool) $t->is_active]; }
    public function query(WorkflowInput $input): Builder { $this->admin($input); return Testimonial::query()->orderBy('sort_order')->orderBy('id'); }
    public function index(WorkflowInput $input): WorkflowResult { $p = $input->paginate($this->query($input)); return WorkflowResult::payload(['items' => $p->getCollection()->map(fn ($t) => $this->item($t))->all(), 'pagination' => ['current_page' => $p->currentPage(), 'per_page' => $p->perPage(), 'total' => $p->total(), 'last_page' => $p->lastPage()]]); }
    public function show(WorkflowInput $input, Testimonial $testimonial): WorkflowResult { $this->admin($input); return WorkflowResult::payload(['testimonial' => $this->item($testimonial)]); }
}
