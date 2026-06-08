<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\HrSchemaService;
use Illuminate\Http\Request;

class SystemMaintenanceController extends Controller
{
    public function __construct()
    {
        $this->checkGuard();
    }

    public function installHrSchema(Request $request, HrSchemaService $hrSchemaService)
    {
        if (!auth('admin')->check() || !auth('admin')->user()->hasRole('Super Admin')) {
            abort(403, 'Only Super Admin can install HR schema.');
        }

        $result = $hrSchemaService->install();

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        $type = $result['success'] ? 'success' : 'error';

        return back()->with($type, $result['message']);
    }
}
