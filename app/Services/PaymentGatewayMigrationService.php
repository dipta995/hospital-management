<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class PaymentGatewayMigrationService
{
    public const MIGRATION_PATHS = [
        'database/migrations/2026_05_21_100000_create_subscriptions_table.php',
        'database/migrations/2026_05_21_100100_create_subscription_payment_requests_table.php',
        'database/migrations/2026_05_21_000000_add_new_column_to_subscriptions_table.php',
        'database/migrations/2026_05_21_130000_add_payment_amount_to_subscriptions_table.php',
        'database/migrations/2026_05_21_130000_add_admin_id_to_subscription_payment_requests_table.php',
        'database/migrations/2026_09_27_100000_add_gateway_columns_to_subscription_payment_requests_table.php',
        'database/migrations/2026_09_27_110000_add_invoice_columns_to_subscription_payment_requests_table.php',
    ];

    protected static ?bool $installed = null;

    public function getStatus(): array
    {
        $hasSubscriptions = Schema::hasTable('subscriptions');
        $hasRequests = Schema::hasTable('subscription_payment_requests');
        $has = fn (array $columns) => $hasRequests && Schema::hasColumns('subscription_payment_requests', $columns);

        return [
            'subscriptions_table' => $hasSubscriptions,
            'payment_requests_table' => $hasRequests,
            'payment_amount_column' => $hasSubscriptions && Schema::hasColumn('subscriptions', 'payment_amount'),
            'admin_id_column' => $has(['admin_id']),
            'gateway_columns' => $has(['gateway', 'invoice_number', 'payment_method', 'gateway_response']),
            'invoice_columns' => $has(['payer_name', 'payer_email', 'invoice_no', 'invoiced_at', 'sms_sent_at']),
        ];
    }

    public function isInstalled(): bool
    {
        if (static::$installed === null) {
            try {
                static::$installed = !in_array(false, $this->getStatus(), true);
            } catch (\Throwable $e) {
                static::$installed = false;
            }
        }

        return static::$installed;
    }

    public function install(): array
    {
        if ($this->isInstalled()) {
            return [
                'success' => true,
                'message' => 'Payment gateway schema is already installed.',
                'status' => $this->getStatus(),
            ];
        }

        $output = '';
        foreach (self::MIGRATION_PATHS as $path) {
            Artisan::call('migrate', ['--path' => $path, '--force' => true]);
            $output .= Artisan::output();
        }

        static::$installed = null;

        return [
            'success' => $this->isInstalled(),
            'message' => $this->isInstalled()
                ? 'Subscription & payment gateway tables installed successfully.'
                : 'Migration ran but some subscription/payment columns are still missing. Check storage/logs/laravel.log.',
            'status' => $this->getStatus(),
            'output' => $output,
        ];
    }
}
