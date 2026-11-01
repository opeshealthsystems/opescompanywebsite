<?php

namespace App\Contexts\Publishing\Application\Queries;

use Barryvdh\DomPDF\Facade\Pdf;
class BrochureQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function download(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, string $locale, string $slug): \App\Contexts\Shared\Application\WorkflowResult
    {
        $products = array_merge(config('products', []), config('products_specialist', []));
        if (!array_key_exists($slug, $products)) {
            abort(404);
        }
        $product = $products[$slug];
        $company = config('company');
        $pdf = \App\Contexts\Shared\Infrastructure\PdfRenderer::document('brochures.product', compact('product', 'company'))->setPaper('a4', 'portrait')->setOption('isHtml5ParserEnabled', true)->setOption('isRemoteEnabled', false)->setOption('defaultFont', 'DejaVu Sans');
        $filename = 'OPES-' . strtoupper(str_replace(' ', '-', $product['name'])) . '-Brochure.pdf';
        return \App\Contexts\Shared\Application\WorkflowResult::download($pdf->output(), $filename);
    }
}
