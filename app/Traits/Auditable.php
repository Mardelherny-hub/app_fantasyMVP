<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    /**
     * Boot the auditable trait for a model.
     */
    protected static function bootAuditable(): void
    {
        // Registrar cuando se crea un modelo
        static::created(function ($model) {
            static::audit($model, 'created', [], $model->getAttributes());
        });

        // Registrar cuando se actualiza un modelo
        static::updated(function ($model) {
            static::audit($model, 'updated', $model->getOriginal(), $model->getAttributes());
        });

        // Registrar cuando se elimina un modelo
        static::deleted(function ($model) {
            static::audit($model, 'deleted', $model->getAttributes(), []);
        });

        // Si usas soft deletes
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                static::audit($model, 'restored', [], $model->getAttributes());
            });
        }
    }

    /**
     * Create an audit log entry.
     *
     * @param mixed $model
     * @param string $action
     * @param array $oldValues
     * @param array $newValues
     * @return void
     */
    protected static function audit($model, string $action, array $oldValues, array $newValues): void
    {
        // Filtrar campos sensibles que no queremos auditar
        $hidden = $model->getHidden();
        $oldValues = array_diff_key($oldValues, array_flip($hidden));
        $newValues = array_diff_key($newValues, array_flip($hidden));

        // Solo guardar los campos que realmente cambiaron
        if ($action === 'updated') {
            $changes = [];
            foreach ($newValues as $key => $value) {
                if (!array_key_exists($key, $oldValues) || $oldValues[$key] !== $value) {
                    $changes[$key] = $value;
                }
            }
            // Si no hay cambios reales, no crear log
            if (empty($changes)) {
                return;
            }
            $newValues = $changes;
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'old_values' => !empty($oldValues) ? $oldValues : null,
            'new_values' => !empty($newValues) ? $newValues : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get all audit logs for this model.
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable')->orderBy('created_at', 'desc');
    }
}