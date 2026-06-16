<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    protected $fillable = [
        'customer_id','type','product_slug','description','preferred_date','preferred_time',
        'location','status','assigned_technician_id','confirmed_date','confirmed_time',
        'admin_notes','reference_number',
    ];

    protected $casts = [
        'preferred_date'  => 'date',
        'confirmed_date'  => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->reference_number) {
                $year = date('Y');
                $last = static::where('reference_number', 'like', "SVC-{$year}-%")->max('reference_number');
                $next = $last ? ((int) substr($last, -5)) + 1 : 1;
                $model->reference_number = 'SVC-' . $year . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer(): BelongsTo { return $this->belongsTo(User::class, 'customer_id'); }
    public function assignedTechnician(): BelongsTo { return $this->belongsTo(User::class, 'assigned_technician_id'); }

    public static function typeOptions(): array {
        return [
            'installation' => 'Installation',
            'maintenance'  => 'Maintenance',
            'training'     => 'Training',
            'other'        => 'Other',
        ];
    }

    public static function statusOptions(): array {
        return [
            'pending'   => 'Pending',
            'confirmed' => 'Confirmed',
            'assigned'  => 'Assigned',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }
}
