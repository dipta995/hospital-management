<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class AuditLogService
{
    public function snapshot(Model $model, array $relations = []): array
    {
        if (!empty($relations)) {
            $model->loadMissing($relations);
        }

        return $this->normalize($model->toArray());
    }

    public function record(string $module, string $action, Model $model, ?array $oldValues, ?array $newValues, ?string $reason = null): ?AuditLog
    {
        if (!$this->tableReady()) {
            return null;
        }

        $admin = auth('admin')->user();
        $attributes = [
            'branch_id' => $model->branch_id ?? $admin?->branch_id,
            'admin_id' => $admin?->id,
            'module' => $module,
            'action' => $action,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changes' => $this->changes($oldValues ?? [], $newValues ?? []),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ];

        if ($this->hasReasonColumn()) {
            $reason = trim((string) ($reason ?? request()?->input('audit_reason', '')));
            $attributes['reason'] = $reason !== '' ? mb_substr($reason, 0, 1000) : null;
        }

        return AuditLog::create($attributes);
    }

    /**
     * Cached per database because tenants switch databases per request.
     */
    public function tableReady(): bool
    {
        return $this->schemaFlag('table', fn () => Schema::hasTable('audit_logs'));
    }

    public function hasReasonColumn(): bool
    {
        return $this->tableReady()
            && $this->schemaFlag('reason', fn () => Schema::hasColumn('audit_logs', 'reason'));
    }

    private static array $schemaCache = [];

    private function schemaFlag(string $key, callable $resolver): bool
    {
        $cacheKey = config('database.connections.mysql.database') . '.' . $key;

        return self::$schemaCache[$cacheKey] ??= (bool) $resolver();
    }

    public static function flushSchemaCache(): void
    {
        self::$schemaCache = [];
    }

    public function changes(array $oldValues, array $newValues): array
    {
        $oldFlat = Arr::dot($oldValues);
        $newFlat = Arr::dot($newValues);
        $keys = collect(array_keys($oldFlat))
            ->merge(array_keys($newFlat))
            ->unique()
            ->sort()
            ->values();

        return $keys
            ->filter(fn ($key) => ($oldFlat[$key] ?? null) !== ($newFlat[$key] ?? null))
            ->map(fn ($key) => [
                'field' => $key,
                'old' => $oldFlat[$key] ?? null,
                'new' => $newFlat[$key] ?? null,
                'type' => $this->changeType($key, $oldFlat, $newFlat),
            ])
            ->values()
            ->all();
    }

    private function changeType(string $key, array $oldFlat, array $newFlat): string
    {
        if (!array_key_exists($key, $oldFlat)) {
            return 'added';
        }

        if (!array_key_exists($key, $newFlat)) {
            return 'removed';
        }

        return 'changed';
    }

    private function normalize(array $values): array
    {
        unset($values['created_at'], $values['updated_at']);

        return collect($values)
            ->map(function ($value) {
                if ($value instanceof \DateTimeInterface) {
                    return $value->format('Y-m-d H:i:s');
                }

                if (is_array($value)) {
                    return $this->normalize($value);
                }

                return $value;
            })
            ->all();
    }
}
