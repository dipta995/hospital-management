<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class HrSchemaService
{
    public const MIGRATION_PATHS = [
        'database/migrations/2026_06_08_000001_add_employee_schedule_columns.php',
        'database/migrations/2026_06_08_000002_create_employee_leave_days_table.php',
    ];

    public const RESIGN_MIGRATION_PATH = 'database/migrations/2026_10_09_000001_add_resign_columns_to_employees_table.php';

    private static array $resignColumnsCache = [];

    public function getStatus(): array
    {
        return [
            'weekly_off_days' => Schema::hasColumn('employees', 'weekly_off_days'),
            'working_hours_per_day' => Schema::hasColumn('employees', 'working_hours_per_day'),
            'annual_leave_quota' => Schema::hasColumn('employees', 'annual_leave_quota'),
            'employee_leave_days_table' => Schema::hasTable('employee_leave_days'),
        ];
    }

    public function isInstalled(): bool
    {
        $status = $this->getStatus();

        return !in_array(false, $status, true);
    }

    public function getResignStatus(): array
    {
        return [
            'resigned_at' => Schema::hasColumn('employees', 'resigned_at'),
            'resign_reason' => Schema::hasColumn('employees', 'resign_reason'),
        ];
    }

    public function isResignInstalled(): bool
    {
        return !in_array(false, $this->getResignStatus(), true);
    }

    /**
     * Cached per tenant database because it is checked inside Employee query scopes.
     */
    public static function hasResignColumns(): bool
    {
        $key = (string) config('database.connections.' . config('database.default') . '.database');

        return self::$resignColumnsCache[$key] ??= Schema::hasColumn('employees', 'resigned_at');
    }

    public function installResign(): array
    {
        if ($this->isResignInstalled()) {
            return [
                'success' => true,
                'message' => 'Employee resign columns are already installed.',
                'status' => $this->getResignStatus(),
            ];
        }

        Artisan::call('migrate', [
            '--path' => self::RESIGN_MIGRATION_PATH,
            '--force' => true,
        ]);
        self::$resignColumnsCache = [];

        return [
            'success' => $this->isResignInstalled(),
            'message' => $this->isResignInstalled()
                ? 'Employee resign columns installed successfully.'
                : 'Migration ran but some columns are still missing. Check logs.',
            'status' => $this->getResignStatus(),
            'output' => Artisan::output(),
        ];
    }

    public function canSummarizeAttendance(): bool
    {
        return Schema::hasColumn('employees', 'weekly_off_days')
            && Schema::hasColumn('employees', 'working_hours_per_day');
    }

    public function install(): array
    {
        if ($this->isInstalled()) {
            return [
                'success' => true,
                'message' => 'HR schedule schema is already installed.',
                'status' => $this->getStatus(),
            ];
        }

        foreach (self::MIGRATION_PATHS as $path) {
            Artisan::call('migrate', [
                '--path' => $path,
                '--force' => true,
            ]);
        }

        return [
            'success' => $this->isInstalled(),
            'message' => $this->isInstalled()
                ? 'HR schedule schema installed successfully.'
                : 'Migration ran but some schema items are still missing. Check logs.',
            'status' => $this->getStatus(),
            'output' => Artisan::output(),
        ];
    }
}
