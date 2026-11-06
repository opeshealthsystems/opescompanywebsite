<?php

namespace App\Contexts\Shared\Application;

/** Application data and named outcomes; route/view selection belongs to web presenters. */
final class WorkflowResult
{
    public array $messages = [];
    public array $errors = [];
    public bool $retainInput = false;
    public array $retainedKeys = [];
    public bool $replayed = false;

    private function __construct(
        public readonly string $kind, public array $data = [], public readonly ?string $variant = null,
        public int $status = 200, public readonly ?string $bytes = null, public readonly ?string $filename = null,
    ) {}

    public static function read(string $variant, array $data = []): self { return new self('data', $data, $variant); }
    public static function next(string $variant, mixed $parameters = []): self { return new self('next', is_array($parameters) ? $parameters : [$parameters], $variant); }
    public static function payload(mixed $data = [], int $status = 200): self { return new self('payload', is_array($data) ? $data : ['value' => $data], status: $status); }
    public static function empty(int $status = 204): self { return new self('empty', status: $status); }
    public static function download(string $bytes, string $filename): self { return new self('binary', status: 200, bytes: $bytes, filename: $filename); }
    public function with(string|array $key, mixed $value = null): self
    {
        $values = is_array($key) ? $key : [$key => $value];
        if ($this->kind === 'data') $this->data = array_merge($this->data, $values);
        else $this->messages = array_merge($this->messages, $values);
        return $this;
    }
    public function withErrors(array $errors): self { $this->errors = $errors; $this->status = 422; return $this; }
    public function withInput(): self { $this->retainInput = true; return $this; }
    public function onlyInput(...$keys): self { $this->retainInput = true; $this->retainedKeys = $keys; return $this; }
}
