<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Services\AuditLogService;
use App\Services\SecurityService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SecurityDailySummary extends Command
{
    protected $signature = 'security:daily-summary
        {--date= : Dhaka date (Y-m-d), defaults to today}
        {--tenant=* : Only these subdomain keys from config/subdomain.php}';

    protected $description = 'SMS each branch owner a summary of the day\'s bills, collection, discounts, edits and deletes';

    public function handle(SecurityService $security): int
    {
        $date = $this->option('date') ?: Carbon::now(SecurityService::TIMEZONE)->toDateString();
        $tenants = config('subdomain.subdomain', []);
        if ($only = $this->option('tenant')) {
            $tenants = array_intersect_key($tenants, array_flip($only));
        }

        foreach ($tenants as $key => $dbConfig) {
            try {
                $this->useTenant($dbConfig);
                if (!Schema::hasTable('branches') || !Schema::hasTable('settings')) {
                    continue;
                }

                foreach (Branch::pluck('id') as $branchId) {
                    $sent = $security->sendDailySummary((int) $branchId, $date);
                    $this->line(sprintf('%s branch %d: %s', $key, $branchId, $sent ? 'sent' : 'skipped'));
                }
            } catch (\Throwable $e) {
                $this->error($key . ': ' . $e->getMessage());
            }
        }

        return self::SUCCESS;
    }

    private function useTenant(array $dbConfig): void
    {
        config([
            'database.connections.mysql.host' => $dbConfig['host'],
            'database.connections.mysql.database' => $dbConfig['database'],
            'database.connections.mysql.username' => $dbConfig['username'],
            'database.connections.mysql.password' => $dbConfig['password'],
        ]);
        DB::purge('mysql');
        DB::reconnect('mysql');
        SecurityService::flushCache();
        AuditLogService::flushSchemaCache();
    }
}
