<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
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

        return view('backend.pages.audit_logs.show', [
            'log' => $auditLog->load('admin'),
        ]);
    }

    private function authorizeAuditLogAccess(): void
    {
        $admin = auth('admin')->user();

        if (!canAccessAuditLogs($admin)) {
            abort(403, 'You are not allowed to view trash records.');
        }
    }
}
