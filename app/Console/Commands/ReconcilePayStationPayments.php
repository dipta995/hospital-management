<?php

namespace App\Console\Commands;

use App\Models\SubscriptionPaymentRequest;
use App\Services\PayStationPaymentFinalizer;
use App\Services\PaymentGatewayMigrationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReconcilePayStationPayments extends Command
{
    protected $signature = 'subscription:paystation-reconcile
        {--tenant=* : Only these subdomain keys from config/subdomain.php}';

    protected $description = 'Re-check unfinished PayStation subscription payments and auto-approve the paid ones';

    public function handle(): int
    {
        $tenants = config('subdomain.subdomain', []);
        if ($only = $this->option('tenant')) {
            $tenants = array_intersect_key($tenants, array_flip($only));
        }

        $checkedDatabases = [];

        foreach ($tenants as $key => $dbConfig) {
            if (in_array($dbConfig['database'], $checkedDatabases, true)) {
                continue;
            }
            $checkedDatabases[] = $dbConfig['database'];

            try {
                $this->useTenant($dbConfig);
                if (!app(PaymentGatewayMigrationService::class)->isInstalled()) {
                    continue;
                }

                $finalizer = app(PayStationPaymentFinalizer::class);
                $subscriptionIds = SubscriptionPaymentRequest::where('gateway', PayStationPaymentFinalizer::GATEWAY)
                    ->where('status', 'initiated')
                    ->where('created_at', '>=', now()->subDays(3))
                    ->distinct()
                    ->pluck('subscription_id');

                foreach ($subscriptionIds as $subscriptionId) {
                    $finalizer->reconcile((int) $subscriptionId, false);
                    $this->line(sprintf('%s subscription %d: checked', $key, $subscriptionId));
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
        PaymentGatewayMigrationService::flushCache();
    }
}
