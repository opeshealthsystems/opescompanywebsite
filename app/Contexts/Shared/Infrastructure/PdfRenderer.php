<?php

namespace App\Contexts\Shared\Infrastructure;

final class PdfRenderer
{
    public static function document(string $template, array $data = []): mixed
    {
        return \Barryvdh\DomPDF\Facade\Pdf::loadView($template, $data);
    }
}
