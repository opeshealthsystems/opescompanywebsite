<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $fillable = [
        'name', 'contact_name', 'email', 'phone', 'address',
        'tax_id', 'is_active', 'notes',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public static function activeOptions(): array
    {
        return static::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();
    }
}
