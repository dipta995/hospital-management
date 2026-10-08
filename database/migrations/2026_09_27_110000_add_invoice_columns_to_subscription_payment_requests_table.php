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
            if (!Schema::hasColumn('subscription_payment_requests', 'payer_name')) {
                $table->string('payer_name', 100)->nullable()->after('sender_number');
            }
            if (!Schema::hasColumn('subscription_payment_requests', 'payer_email')) {
                $table->string('payer_email', 150)->nullable()->after('payer_name');
            }
            if (!Schema::hasColumn('subscription_payment_requests', 'invoice_no')) {
                $table->string('invoice_no', 30)->nullable()->unique()->after('invoice_number');
            }
            if (!Schema::hasColumn('subscription_payment_requests', 'invoiced_at')) {
                $table->timestamp('invoiced_at')->nullable()->after('invoice_no');
            }
            if (!Schema::hasColumn('subscription_payment_requests', 'sms_sent_at')) {
                $table->timestamp('sms_sent_at')->nullable()->after('invoiced_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('subscription_payment_requests')) {
            return;
        }

        Schema::table('subscription_payment_requests', function (Blueprint $table) {
            if (Schema::hasColumn('subscription_payment_requests', 'invoice_no')) {
                $table->dropUnique(['invoice_no']);
            }

            $columns = array_filter(
                ['payer_name', 'payer_email', 'invoice_no', 'invoiced_at', 'sms_sent_at'],
                fn ($column) => Schema::hasColumn('subscription_payment_requests', $column)
            );

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
