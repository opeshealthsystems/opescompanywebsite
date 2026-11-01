<?php

namespace App\Contexts\Shared\Application;

/** Carries a business rejection across the transaction boundary so writes roll back. */
final class RejectedWorkflow extends \RuntimeException
{
    public function __construct(public readonly WorkflowResult $result) { parent::__construct('Business operation rejected'); }
}
