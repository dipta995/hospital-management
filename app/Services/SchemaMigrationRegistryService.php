<?php

namespace App\Services;

class SchemaMigrationRegistryService
{
    public function __construct(
        private AuditLogSchemaService $auditLogSchemaService,
        private HrSchemaService $hrSchemaService,
        private PharmacySchemaService $pharmacySchemaService,
        private AiSchemaService $aiSchemaService,
        private LabFollowupSchemaService $labFollowupSchemaService,
    ) {}

    public function all(): array
    {
        return [
            $this->auditLogsModule(),
            $this->hrScheduleModule(),
            $this->pharmacyStatusModule(),
            $this->aiFeaturesModule(),
            $this->labFollowupModule(),
        ];
    }

    public function find(string $key): ?array
    {
        foreach ($this->all() as $module) {
            if ($module['key'] === $key) {
                return $module;
            }
        }

        return null;
    }

    public function getSummary(): array
    {
        return array_map(function (array $module) {
            $status = $module['status']();

            return [
                'key' => $module['key'],
                'label' => $module['label'],
                'description' => $module['description'],
                'destructive' => $module['destructive'],
                'installed' => $module['installed'](),
                'status' => $status,
                'status_labels' => $this->formatStatusLabels($module['status_labels'], $status),
            ];
        }, $this->all());
    }

    public function pendingCount(): int
    {
        return count(array_filter($this->getSummary(), fn (array $module) => empty($module['installed'])));
    }

    public function installAllPending(): array
    {
        $results = [];
        $installed = 0;
        $failed = 0;

        foreach ($this->all() as $module) {
            if ($module['destructive'] || $module['installed']()) {
                continue;
            }

            $result = $module['install']();
            $success = (bool) ($result['success'] ?? false);
            $results[] = [
                'key' => $module['key'],
                'label' => $module['label'],
                'success' => $success,
                'message' => $result['message'] ?? ($success ? 'Installed.' : 'Failed.'),
            ];

            if ($success) {
                $installed++;
            } else {
                $failed++;
            }
        }

        if ($installed === 0 && $failed === 0) {
            return [
                'success' => true,
                'message' => 'All schema updates are already installed.',
                'results' => [],
            ];
        }

        return [
            'success' => $failed === 0,
            'message' => $failed === 0
                ? "{$installed} update(s) applied successfully."
                : "{$installed} applied, {$failed} failed. Check details below.",
            'results' => $results,
        ];
    }

    public function install(string $key): array
    {
        $module = $this->find($key);

        if (!$module) {
            return [
                'success' => false,
                'message' => 'Unknown schema module.',
            ];
        }

        if ($module['destructive']) {
            return [
                'success' => false,
                'message' => 'This module cannot be installed from the dashboard.',
            ];
        }

        return $module['install']();
    }

    private function formatStatusLabels(array $labels, array $status): array
    {
        $formatted = [];

        foreach ($labels as $field => $label) {
            $formatted[] = [
                'field' => $field,
                'label' => $label,
                'ok' => (bool) ($status[$field] ?? false),
            ];
        }

        return $formatted;
    }

    private function auditLogsModule(): array
    {
        return [
            'key' => 'audit_logs',
            'label' => 'Audit Logs',
            'description' => 'Protected audit/trash table for tracking changes. Additive only — no data removal.',
            'destructive' => false,
            'status' => fn () => $this->auditLogSchemaService->getStatus(),
            'installed' => fn () => $this->auditLogSchemaService->isInstalled(),
            'install' => fn () => $this->auditLogSchemaService->install(),
            'status_labels' => [
                'audit_logs_table' => 'Audit logs table',
            ],
        ];
    }

    private function hrScheduleModule(): array
    {
        return [
            'key' => 'hr_schedule',
            'label' => 'HR Schedule & Leave',
            'description' => 'Employee schedule columns and leave days table. Adds columns/tables only.',
            'destructive' => false,
            'status' => fn () => $this->hrSchemaService->getStatus(),
            'installed' => fn () => $this->hrSchemaService->isInstalled(),
            'install' => fn () => $this->hrSchemaService->install(),
            'status_labels' => [
                'weekly_off_days' => 'Weekly off days column',
                'working_hours_per_day' => 'Working hours column',
                'annual_leave_quota' => 'Annual leave quota column',
                'employee_leave_days_table' => 'Employee leave days table',
            ],
        ];
    }

    private function pharmacyStatusModule(): array
    {
        return [
            'key' => 'pharmacy_status',
            'label' => 'Pharmacy Status Columns',
            'description' => 'Active/inactive status on pharmacy products, brands, and units. Default: active (1).',
            'destructive' => false,
            'status' => fn () => $this->pharmacySchemaService->getStatus(),
            'installed' => fn () => $this->pharmacySchemaService->isInstalled(),
            'install' => fn () => $this->pharmacySchemaService->install(),
            'status_labels' => [
                'pharmacy_products_status' => 'Products status column',
                'pharmacy_brands_status' => 'Brands status column',
                'pharmacy_units_status' => 'Units status column',
            ],
        ];
    }

    private function aiFeaturesModule(): array
    {
        return [
            'key' => 'ai_features',
            'label' => 'AI Features',
            'description' => 'Chat sessions, AI insights cache, and report summary column. Additive only.',
            'destructive' => false,
            'status' => fn () => $this->aiSchemaService->getStatus(),
            'installed' => fn () => $this->aiSchemaService->isInstalled(),
            'install' => fn () => $this->aiSchemaService->install(),
            'status_labels' => [
                'ai_chat_sessions_table' => 'AI chat sessions table',
                'ai_chat_messages_table' => 'AI chat messages table',
                'ai_insights_table' => 'AI insights cache table',
                'invoice_lists_ai_summary_column' => 'Report summary column',
            ],
        ];
    }

    private function labFollowupModule(): array
    {
        return [
            'key' => 'lab_followup',
            'label' => 'Lab Follow-up Notes',
            'description' => 'Per-test note and follow-up date columns on invoice test lines.',
            'destructive' => false,
            'status' => fn () => $this->labFollowupSchemaService->getStatus(),
            'installed' => fn () => $this->labFollowupSchemaService->isInstalled(),
            'install' => fn () => $this->labFollowupSchemaService->install(),
            'status_labels' => [
                'invoice_lists_note_column' => 'Test line note column',
                'invoice_lists_followup_date_column' => 'Test line follow-up date column',
            ],
        ];
    }
}
