<?php

namespace App\Contexts\Enquiries\Application\Queries;

use App\Models\DemoRequest;
use App\Models\Product;
use App\Models\User;
use Filament\Notifications\Notification;
class DemoRequestQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $products = \App\Models\Product::publicCatalogue()->sortBy('name');
        return \App\Contexts\Shared\Application\WorkflowResult::read('demorequestcontroller_show_1', compact('products'));
    }
}
