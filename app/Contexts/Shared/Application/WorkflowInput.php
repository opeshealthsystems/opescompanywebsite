<?php

namespace App\Contexts\Shared\Application;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

/** Transport-independent input shared by web, API and command-line adapters. */
final class WorkflowInput
{
    public function __construct(
        private array $values = [], private array $parameters = [], private ?User $actor = null,
        private string $language = 'en', private ?string $address = null, private array $uploads = [],
        public readonly bool $api = false, public readonly array $scopes = [], public readonly ?string $consumerId = null,
    ) {}

    public function user(): ?User { return $this->actor; }
    public function ip(): ?string { return $this->address; }
    public function locale(): string { return $this->language; }
    public function route(?string $key = null, mixed $default = null): mixed { return $key === null ? $this->parameters : ($this->parameters[$key] ?? $default); }
    public function input(?string $key = null, mixed $default = null): mixed { return $key === null ? $this->values : data_get($this->values, $key, $default); }
    public function query(?string $key = null, mixed $default = null): mixed { return $this->input($key, $default); }
    public function all(): array { return array_replace_recursive($this->values, $this->uploads); }
    public function only(...$keys): array { return array_intersect_key($this->values, array_flip(is_array($keys[0] ?? null) ? $keys[0] : $keys)); }
    public function except(...$keys): array { return array_diff_key($this->values, array_flip(is_array($keys[0] ?? null) ? $keys[0] : $keys)); }
    public function filled(string $key): bool { return $this->input($key) !== null && $this->input($key) !== '' && $this->input($key) !== []; }
    public function has(string $key): bool { return array_key_exists($key, $this->values); }
    public function boolean(string $key, bool $default = false): bool { return filter_var($this->input($key, $default), FILTER_VALIDATE_BOOLEAN); }
    public function file(string $key): mixed { return data_get($this->uploads, $key); }
    public function hasFile(string $key): bool { return $this->file($key) !== null; }
    public function validate(array $rules, array $messages = [], array $attributes = []): array { return Validator::make($this->all(), $rules, $messages, $attributes)->validate(); }
    public function authorize(string $ability, mixed $arguments): mixed { return Gate::forUser($this->actor)->authorize($ability, $arguments); }
    public function __get(string $key): mixed { return $this->input($key); }
    public function __isset(string $key): bool { return $this->filled($key); }

    public function paginate(mixed $query, int $default = 20): mixed
    {
        $page = $this->validate(['page' => 'sometimes|integer|min:1|max:10000', 'per_page' => 'sometimes|integer|min:1|max:100']);
        return $query->paginate((int) ($page['per_page'] ?? $default), ['*'], 'page', (int) ($page['page'] ?? 1));
    }

    public function collect(mixed $query, mixed ...$arguments): mixed
    {
        if ($this->api && ($query instanceof \Illuminate\Database\Eloquent\Builder || $query instanceof \Illuminate\Database\Query\Builder || $query instanceof \Illuminate\Database\Eloquent\Relations\Relation)) {
            $base = $query instanceof \Illuminate\Database\Query\Builder ? $query : $query->getQuery();
            if ($base instanceof \Illuminate\Database\Eloquent\Builder) $base = $base->getQuery();
            if ($base->limit === null || $base->limit > 100) $query->limit(100);
        }
        return $query->get(...$arguments);
    }
}
