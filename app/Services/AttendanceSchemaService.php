<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class AttendanceSchemaService
{
    public const MIGRATION_PATH = 'database/migrations/2026_10_10_000001_create_attendance_shifts.php';

    private static array $installedCache = [];

    public function getStatus(): array
    {
        return [
            'attendance_shifts_table' => Schema::hasTable('attendance_shifts'),
            'employees_shift_id' => Schema::hasColumn('employees', 'shift_id'),
            'attendances_shift_id' => Schema::hasColumn('attendances', 'shift_id'),
            'attendances_source' => Schema::hasColumn('attendances', 'source'),
        ];
    }

    public function isInstalled(): bool
    {
        return !in_array(false, $this->getStatus(), true);
    }

    /**
     * Cached per tenant database because punches and reports check it on every call.
     */
    public static function hasShifts(): bool
    {
        $key = (string) config('database.connections.' . config('database.default') . '.database');

        return self::$installedCache[$key] ??= (new self())->isInstalled();
    }

    public function install(): array
    {
        if ($this->isInstalled()) {
            return [
                'success' => true,
                'message' => 'Attendance shift schema is already installed.',
                'status' => $this->getStatus(),
            ];
        }

        Artisan::call('migrate', [
            '--path' => self::MIGRATION_PATH,
            '--force' => true,
        ]);
        self::$installedCache = [];

        return [
            'success' => $this->isInstalled(),
            'message' => $this->isInstalled()
                ? 'Attendance shift schema installed successfully.'
                : 'Migration ran but some schema items are still missing. Check logs.',
            'status' => $this->getStatus(),
            'output' => Artisan::output(),
        ];
    }
}
