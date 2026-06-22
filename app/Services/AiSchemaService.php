<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class AiSchemaService
{
    public const MIGRATION_PATH = 'database/migrations/2026_06_22_000001_create_ai_features_tables.php';

    public function getStatus(): array
    {
        return [
            'ai_chat_sessions_table' => Schema::hasTable('ai_chat_sessions'),
            'ai_chat_messages_table' => Schema::hasTable('ai_chat_messages'),
            'ai_insights_table' => Schema::hasTable('ai_insights'),
            'invoice_lists_ai_summary_column' => Schema::hasColumn('invoice_lists', 'ai_summary'),
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
                'message' => 'AI features schema is already installed.',
                'status' => $this->getStatus(),
            ];
        }

        Artisan::call('migrate', [
            '--path' => self::MIGRATION_PATH,
            '--force' => true,
        ]);

        Artisan::call('migrate', [
            '--path' => 'database/migrations/2026_06_22_000002_add_ai_permissions.php',
            '--force' => true,
        ]);

        return [
            'success' => $this->isInstalled(),
            'message' => $this->isInstalled()
                ? 'AI features schema installed successfully. No existing data was removed.'
                : 'Migration ran but some AI schema items are still missing. Check logs.',
            'status' => $this->getStatus(),
            'output' => Artisan::output(),
        ];
    }
}
