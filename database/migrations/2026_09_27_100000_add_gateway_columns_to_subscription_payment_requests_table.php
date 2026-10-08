<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('subscription_payment_requests')) {
            return;
        }

        Schema::table('subscription_payment_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('subscription_payment_requests', 'gateway')) {
                $table->string('gateway', 30)->nullable()->after('note');
            }
            if (!Schema::hasColumn('subscription_payment_requests', 'invoice_number')) {
                $table->string('invoice_number', 60)->nullable()->unique()->after('gateway');
            }
            if (!Schema::hasColumn('subscription_payment_requests', 'payment_method')) {
                $table->string('payment_method', 40)->nullable()->after('invoice_number');
            }
            if (!Schema::hasColumn('subscription_payment_requests', 'gateway_response')) {
                $table->json('gateway_response')->nullable()->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('subscription_payment_requests')) {
            return;
        }

        Schema::table('subscription_payment_requests', function (Blueprint $table) {
            if (Schema::hasColumn('subscription_payment_requests', 'invoice_number')) {
                $table->dropUnique(['invoice_number']);
            }

            $columns = array_filter(
                ['gateway', 'invoice_number', 'payment_method', 'gateway_response'],
                fn ($column) => Schema::hasColumn('subscription_payment_requests', $column)
            );

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
