<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\SchemaMigrationRegistryService;
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

    public function installAuditLogSchema(Request $request, SchemaMigrationRegistryService $schemaRegistry)
    {
        return $this->installSchema($request, 'audit_logs', $schemaRegistry);
    }

    public function installHrSchema(Request $request, SchemaMigrationRegistryService $schemaRegistry)
    {
        return $this->installSchema($request, 'hr_schedule', $schemaRegistry);
    }

    public function schemaStatus(SchemaMigrationRegistryService $schemaRegistry)
    {
        if (!canManageSystemSchema(auth('admin')->user())) {
            abort(403, 'Only Super Admin can view schema status.');
        }

        return response()->json([
            'success' => true,
            'modules' => $schemaRegistry->getSummary(),
        ]);
    }

    public function installSchema(Request $request, string $key, SchemaMigrationRegistryService $schemaRegistry)
    {
        if (!canManageSystemSchema(auth('admin')->user())) {
            abort(403, 'Only Super Admin can install database schema updates.');
        }

        $result = $schemaRegistry->install($key);

        if ($request->expectsJson()) {
            return response()->json($result, ($result['success'] ?? false) ? 200 : 422);
        }

        $type = ($result['success'] ?? false) ? 'success' : 'error';

        return back()->with($type, $result['message'] ?? 'Schema update failed.');
    }
}
