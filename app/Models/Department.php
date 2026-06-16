<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = ['name', 'code', 'head_id', 'parent_id', 'is_active', 'description'];

    protected $casts = ['is_active' => 'boolean'];

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(User::class, 'department_id');
    }

    public static function activeOptions(): array
    {
        return static::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();
    }
}
