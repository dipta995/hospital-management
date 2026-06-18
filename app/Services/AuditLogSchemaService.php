<?php

namespace App\Services;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AuditLogSchemaService
{
    public function isInstalled(): bool
    {
        return Schema::hasTable('audit_logs');
    }

    public function getStatus(): array
    {
        return [
            'audit_logs_table' => $this->isInstalled(),
        ];
    }

    public function install(): array
    {
        if ($this->isInstalled()) {
            return [
                'success' => true,
                'message' => 'Audit log table is already installed.',
                'status' => $this->getStatus(),
            ];
        }

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->unsignedBigInteger('admin_id')->nullable()->index();
            $table->string('module')->index();
            $table->string('action')->index();
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable()->index();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('changes')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        return [
            'success' => $this->isInstalled(),
            'message' => $this->isInstalled()
                ? 'Audit log table installed successfully.'
                : 'Audit log table setup failed. Please check database permissions.',
            'status' => $this->getStatus(),
        ];
    }
}
