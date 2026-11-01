<?php

namespace App\Contexts\Shared\Domain;

final readonly class BusinessEvent
{
    public function __construct(public string $id, public string $type, public ?int $actorId, public array $payload, public int $version = 1) {}
}
