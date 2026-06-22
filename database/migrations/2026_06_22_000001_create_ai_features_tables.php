<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ai_chat_sessions')) {
            Schema::create('ai_chat_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->index();
                $table->unsignedBigInteger('admin_id')->index();
                $table->string('title')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ai_chat_messages')) {
            Schema::create('ai_chat_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('session_id')->index();
                $table->string('role', 20);
                $table->text('content');
                $table->string('source', 20)->default('ai');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('invoice_lists') && !Schema::hasColumn('invoice_lists', 'ai_summary')) {
            Schema::table('invoice_lists', function (Blueprint $table) {
                $table->text('ai_summary')->nullable()->after('test_report');
            });
        }

        if (!Schema::hasTable('ai_insights')) {
            Schema::create('ai_insights', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->index();
                $table->string('type', 50)->index();
                $table->string('context_key')->nullable();
                $table->text('content');
                $table->string('source', 20)->default('ai');
                $table->timestamps();

                $table->index(['branch_id', 'type', 'context_key']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('invoice_lists', 'ai_summary')) {
            Schema::table('invoice_lists', function (Blueprint $table) {
                $table->dropColumn('ai_summary');
            });
        }

        Schema::dropIfExists('ai_chat_messages');
        Schema::dropIfExists('ai_chat_sessions');
        Schema::dropIfExists('ai_insights');
    }
};
