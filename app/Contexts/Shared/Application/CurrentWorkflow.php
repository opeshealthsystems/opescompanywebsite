<?php

namespace App\Contexts\Shared\Application;

/** Scoped by the dispatcher, restored after nested application calls. */
final class CurrentWorkflow
{
    public ?WorkflowInput $input = null;
    public ?string $operation = null;
    public array $changes = [];
    public array $outboxIds = [];
    public bool $command = false;

    public static function actor(): ?\App\Models\User { return app(self::class)->input?->user(); }
    public static function id(): ?int { return self::actor()?->id; }
}
