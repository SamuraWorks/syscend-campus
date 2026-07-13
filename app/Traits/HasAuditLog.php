<?php

namespace App\Traits;

use App\Models\AuditLog;

trait HasAuditLog
{
    public static function bootHasAuditLog(): void
    {
        static::created(function ($model) {
            AuditLog::create([
                'school_id'      => $model->school_id ?? auth()->user()?->school_id,
                'user_id'        => auth()->id(),
                'event'          => 'created',
                'auditable_type' => get_class($model),
                'auditable_id'   => $model->id,
                'old_values'     => [],
                'new_values'     => $model->getAttributes(),
                'ip_address'     => request()->ip(),
            ]);
        });

        static::updated(function ($model) {
            if (method_exists($model, 'isAuditEnabled') && !$model->isAuditEnabled()) {
                return;
            }

            $dirty    = $model->getDirty();
            $original = $model->getOriginal();
            $auditableOld = [];
            $auditableNew = [];

            foreach ($dirty as $key => $value) {
                $auditableOld[$key] = $original[$key] ?? null;
                $auditableNew[$key] = $value;
            }

            if (empty($auditableNew)) return;

            AuditLog::create([
                'school_id'      => $model->school_id ?? auth()->user()?->school_id,
                'user_id'        => auth()->id(),
                'event'          => 'updated',
                'auditable_type' => get_class($model),
                'auditable_id'   => $model->id,
                'old_values'     => $auditableOld,
                'new_values'     => $auditableNew,
                'ip_address'     => request()->ip(),
            ]);
        });

        static::deleted(function ($model) {
            AuditLog::create([
                'school_id'      => $model->school_id ?? auth()->user()?->school_id,
                'user_id'        => auth()->id(),
                'event'          => 'deleted',
                'auditable_type' => get_class($model),
                'auditable_id'   => $model->id,
                'old_values'     => $model->getAttributes(),
                'ip_address'     => request()->ip(),
            ]);
        });
    }
}
