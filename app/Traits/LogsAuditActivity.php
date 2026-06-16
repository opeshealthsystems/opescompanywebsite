<?php
namespace App\Traits;

use App\Models\AuditLog;

trait LogsAuditActivity
{
    public static function bootLogsAuditActivity(): void
    {
        static::created(function ($model) {
            AuditLog::record('created', $model, null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $old = array_intersect_key($model->getOriginal(), $model->getDirty());
            AuditLog::record('updated', $model, $old, $model->getDirty());
        });

        static::deleted(function ($model) {
            AuditLog::record('deleted', $model, $model->getAttributes(), null);
        });
    }
}
