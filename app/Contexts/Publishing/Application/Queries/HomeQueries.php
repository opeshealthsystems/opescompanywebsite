<?php

namespace App\Contexts\Publishing\Application\Queries;

use App\Models\PractitionerFinding;
use App\Models\Product;
class HomeQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $products = \App\Models\Product::publicCatalogue();
        $testimonials = $workflowInput->collect(\App\Models\PractitionerFinding::where('is_published', true)->whereHas('practitioner', fn($q) => $q->where('email', 'not like', '%@demo.opes')->where('email', 'not like', '%.test'))->whereNotNull('findings_text')->with(['practitioner.practitionerProfile', 'application.program'])->latest());
        $articles = $workflowInput->collect(\App\Models\BlogPost::published()->limit(3));
        $healthServiceOfferings = app(PublicServiceOfferingQueries::class)->catalogue($workflowInput)->data['items'];
        return \App\Contexts\Shared\Application\WorkflowResult::read('homecontroller_index_1', compact('products', 'testimonials', 'articles', 'healthServiceOfferings'));
    }
}
