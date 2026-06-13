<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use App\Models\Document;
use App\Models\DocumentTemplate;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $template = DocumentTemplate::findOrFail($data['document_template_id']);

        $variables = $data['variable_values'] ?? [];

        $data['type']             = $template->type;
        $data['issued_by']        = auth()->id();
        $data['reference_number'] = Document::generateReferenceNumber($template->type);
        $data['body_rendered']    = Document::renderTemplate($template, $variables);
        $data['status']           = 'draft';

        if (!empty($data['requires_signature'])) {
            $data['signature_token']             = Str::random(64);
            $data['signature_token_expires_at']  = now()->addDays(30);
            $data['status']                      = 'pending_signature';
        }

        unset($data['variable_values']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
