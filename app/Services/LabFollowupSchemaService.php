<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class LabFollowupSchemaService
{
    public const MIGRATION_PATH = 'database/migrations/2026_06_22_000003_add_lab_followup_columns_to_invoice_lists.php';

    public function getStatus(): array
    {
        return [
            'invoice_lists_note_column' => Schema::hasColumn('invoice_lists', 'note'),
            'invoice_lists_followup_date_column' => Schema::hasColumn('invoice_lists', 'followup_date'),
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
                'message' => 'Lab follow-up schema is already installed.',
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
                ? 'Lab follow-up columns installed successfully.'
                : 'Migration ran but follow-up columns are still missing. Check logs.',
            'status' => $this->getStatus(),
            'output' => Artisan::output(),
        ];
    }
}
