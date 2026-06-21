<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\AuditLogSchemaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SystemMaintenanceController extends Controller
{
    public function __construct()
    {
        $this->checkGuard();
    }

    public function clearCache(Request $request)
    {
        if (!auth('admin')->check() || !auth('admin')->user()->hasRole('Super Admin')) {
            abort(403, 'Only Super Admin can clear application cache.');
        }

        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');

        $message = 'Application cache cleared successfully.';

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    public function installAuditLogSchema(Request $request, AuditLogSchemaService $auditLogSchemaService)
    {
        if (!canAccessAuditLogs(auth('admin')->user())) {
            abort(403, 'Only Super Admin can install audit log schema.');
        }

        $result = $auditLogSchemaService->install();

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        $type = $result['success'] ? 'success' : 'error';

        return back()->with($type, $result['message']);
    }
}
