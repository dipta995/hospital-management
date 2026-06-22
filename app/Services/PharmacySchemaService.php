<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class PharmacySchemaService
{
    public const MIGRATION_PATH = 'database/migrations/2026_06_12_000000_add_status_to_pharmacy_tables.php';

    public function getStatus(): array
    {
        return [
            'pharmacy_products_status' => Schema::hasColumn('pharmacy_products', 'status'),
            'pharmacy_brands_status' => Schema::hasColumn('pharmacy_brands', 'status'),
            'pharmacy_units_status' => Schema::hasColumn('pharmacy_units', 'status'),
        ];
    }

    public function isInstalled(): bool
    {
        $status = $this->getStatus();

        return !in_array(false, $status, true);
    }

    public function install(): array
    {
        if ($this->isInstalled()) {
            return [
                'success' => true,
                'message' => 'Pharmacy status columns are already installed.',
                'status' => $this->getStatus(),
            ];
        }

        Artisan::call('migrate', [
            '--path' => self::MIGRATION_PATH,
            '--force' => true,
        ]);

        return [
            'success' => $this->isInstalled(),
            'message' => $this->isInstalled()
                ? 'Pharmacy status columns added successfully. Existing rows keep default active status.'
                : 'Migration ran but some pharmacy columns are still missing. Check logs.',
            'status' => $this->getStatus(),
            'output' => Artisan::output(),
        ];
    }
}
