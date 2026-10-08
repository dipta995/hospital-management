<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Additive only: every table/column is guarded so it can run on databases
     * where some of these already exist. Nothing is dropped or modified.
     */
    public function up(): void
    {
        if (!Schema::hasTable('invoice_cancel_requests')) {
            Schema::create('invoice_cancel_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->unsignedBigInteger('invoice_id')->index();
                $table->string('invoice_number')->nullable();
                $table->string('patient_name')->nullable();
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->decimal('paid_amount', 12, 2)->default(0);
                $table->unsignedBigInteger('requested_by')->nullable()->index();
                $table->text('reason');
                $table->string('status', 20)->default('pending')->index();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('review_note')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cash_closings')) {
            Schema::create('cash_closings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->unsignedBigInteger('admin_id')->index();
                $table->date('closing_date')->index();
                $table->unsignedInteger('payment_count')->default(0);
                $table->decimal('system_amount', 12, 2)->default(0);
                $table->decimal('counted_amount', 12, 2)->default(0);
                $table->decimal('difference', 12, 2)->default(0);
                $table->json('breakdown')->nullable();
                $table->text('note')->nullable();
                $table->string('status', 20)->default('closed')->index();
                $table->unsignedBigInteger('verified_by')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->text('verify_note')->nullable();
                $table->timestamps();
                $table->unique(['branch_id', 'admin_id', 'closing_date'], 'cash_closings_unique_day');
            });
        }

        if (!Schema::hasTable('login_histories')) {
            Schema::create('login_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->unsignedBigInteger('admin_id')->nullable()->index();
                $table->string('email')->nullable()->index();
                $table->string('event', 20)->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('created_at')->nullable()->index();
            });
        }

        if (Schema::hasTable('audit_logs') && !Schema::hasColumn('audit_logs', 'reason')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                $table->text('reason')->nullable()->after('changes');
            });
        }
    }

    public function down(): void
    {
        // Intentionally empty: security history must never be dropped by a rollback.
    }
};
