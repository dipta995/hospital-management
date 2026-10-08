<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
    }

    public function index(Request $request)
    {
        $this->authorizeAuditLogAccess();

        if (!Schema::hasTable('audit_logs')) {
            return view('backend.pages.audit_logs.index', [
                'logs' => new LengthAwarePaginator([], 0, 30),
                'modules' => ['invoice', 'recept', 'cost'],
                'actions' => ['updated', 'deleted'],
                'tableReady' => false,
            ]);
        }

        $query = AuditLog::with('admin')
            ->where('branch_id', auth()->user()->branch_id);

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('record_id')) {
            $query->where('auditable_id', $request->record_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return view('backend.pages.audit_logs.index', [
            'logs' => $query->latest()->paginate(30)->withQueryString(),
            'modules' => ['invoice', 'recept', 'cost'],
            'actions' => ['updated', 'deleted'],
            'tableReady' => true,
        ]);
    }

    public function show(AuditLog $auditLog)
    {
        $this->authorizeAuditLogAccess();

        if (!Schema::hasTable('audit_logs')) {
            abort(404);
        }

        if ((int) $auditLog->branch_id !== (int) auth()->user()->branch_id) {
            abort(404);
        }

        $slipService = app(\App\Services\AuditSlipViewService::class);
        $invoiceSlipOld = null;
        $invoiceSlipNew = null;

        if ($auditLog->module === 'invoice') {
            $invoiceSlipOld = $slipService->invoiceSlipFromAudit($auditLog->old_values);
            $invoiceSlipNew = $auditLog->action === 'deleted'
                ? null
                : $slipService->invoiceSlipFromAudit($auditLog->new_values);
        }

        return view('backend.pages.audit_logs.show', [
            'log' => $auditLog->load('admin'),
            'invoiceSlipOld' => $invoiceSlipOld,
            'invoiceSlipNew' => $invoiceSlipNew,
        ]);
    }

    public function destroy(AuditLog $auditLog)
    {
        $admin = auth('admin')->user();
        if (!canSecurity('security.audit_delete', $admin)) {
            abort(403, 'Trash রেকর্ড মুছে ফেলার অনুমতি (security.audit_delete) নেই।');
        }
        if ((int) $auditLog->branch_id !== (int) $admin->branch_id) {
            abort(404);
        }

        Log::warning('Trash record deleted', [
            'audit_log_id' => $auditLog->id,
            'module' => $auditLog->module,
            'action' => $auditLog->action,
            'auditable_id' => $auditLog->auditable_id,
            'deleted_by' => $admin->id,
            'deleted_by_name' => $admin->name,
        ]);
        $auditLog->delete();

        return redirect()
            ->route('admin.audit-logs.index', request()->only(['module', 'action', 'record_id', 'start_date', 'end_date', 'page']))
            ->with('success', 'Trash রেকর্ড মুছে ফেলা হয়েছে।');
    }

    private function authorizeAuditLogAccess(): void
    {
        $admin = auth('admin')->user();

        if (!canAccessAuditLogs($admin)) {
            abort(403, 'Trash দেখার অনুমতি (security.audit_logs) নেই।');
        }
    }
}
