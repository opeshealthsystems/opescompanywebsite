<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id','model_type','model_id','action','old_values','new_values','ip_address','user_agent'];
    protected $casts = ['old_values'=>'array','new_values'=>'array','created_at'=>'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public static function record(string $action, Model $model, ?array $old = null, ?array $new = null): void
    {
        static::create([
            'user_id'    => auth()->id(),
            'model_type' => class_basename($model),
            'model_id'   => $model->getKey(),
            'action'     => $action,
            'old_values' => $old,
            'new_values' => $new ? array_diff_key($new, array_flip(['password','remember_token','token'])) : null,
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 300),
        ]);
    }
}
