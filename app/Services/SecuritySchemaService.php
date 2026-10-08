<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class SecuritySchemaService
{
    public const TABLES = ['invoice_cancel_requests', 'cash_closings', 'login_histories'];

    public const MIGRATION_PATH = 'database/migrations/2026_09_30_100000_create_security_tables.php';

    public function __construct(private SecurityPermissionService $permissions)
    {
    }

    public function isInstalled(): bool
    {
        foreach (self::TABLES as $table) {
            if (!Schema::hasTable($table)) {
                return false;
            }
        }

        return Schema::hasTable('audit_logs') && Schema::hasColumn('audit_logs', 'reason');
    }

    public function getStatus(): array
    {
        $status = [];
        foreach (self::TABLES as $table) {
            $status[$table] = Schema::hasTable($table);
        }
        $status['audit_reason_column'] = Schema::hasTable('audit_logs') && Schema::hasColumn('audit_logs', 'reason');
        $status['permissions'] = $this->permissions->status()['ready'];

        return $status;
    }

    public function isFullyInstalled(): bool
    {
        return !in_array(false, $this->getStatus(), true);
    }

    /**
     * Tables first, then permissions (newly created ones go to Owner roles).
     */
    public function install(): array
    {
        if (!$this->isInstalled()) {
            Artisan::call('migrate', ['--path' => self::MIGRATION_PATH, '--force' => true]);
            SecurityService::flushCache();
        }

        $permissionResult = $this->permissions->install();
        $ok = $this->isFullyInstalled();

        return [
            'success' => $ok,
            'message' => $ok
                ? 'Security tables installed. ' . $permissionResult['message']
                : 'Security install incomplete: ' . ($permissionResult['success'] ? 'some tables are still missing (audit_logs table must exist first).' : $permissionResult['message']),
            'status' => $this->getStatus(),
        ];
    }
}
