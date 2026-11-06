<?php

namespace App\Contexts\Publishing\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;

/** Public descriptions of requestable health service offerings. */
final class PublicServiceOfferingQueries extends WorkflowHandler
{
    public function catalogue(WorkflowInput $input): WorkflowResult
    {
        $locale = $input->locale() === 'fr' ? 'fr' : 'en';
        $items = array_map(static function (array $offering) use ($locale): array {
            foreach (['name', 'summary', 'cta', 'data_boundary'] as $field) {
                $localized = $field.'_fr';
                if ($locale === 'fr' && isset($offering[$localized])) $offering[$field] = $offering[$localized];
                unset($offering[$localized]);
            }
            $offering['contact_url'] = '/'.$locale.'/contact?service='.rawurlencode($offering['id']);

            return $offering;
        }, config('service_offerings', []));

        return WorkflowResult::payload(['locale' => $locale, 'items' => $items]);
    }
}
