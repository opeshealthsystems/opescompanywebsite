<?php

namespace App\Contexts\Shared\Infrastructure;

final class DeferredWebsiteMail
{
    public static function deliver(\Illuminate\Database\Eloquent\Model $submission): void
    {
        \Illuminate\Support\Facades\DB::afterCommit(fn () => app(\App\Services\WebsiteFormMailer::class)->deliver($submission));
    }
}
