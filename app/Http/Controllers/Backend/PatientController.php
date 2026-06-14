<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\PatientInsightService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function __construct()
    {
        $this->checkGuard();
    }

    public function profile(Request $request, PatientInsightService $patientInsightService)
    {
        $this->checkOwnPermission('users.index');

        $phone = $request->query('phone');
        $userId = $request->query('user_id') ? (int) $request->query('user_id') : null;

        if (!$phone && !$userId) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Select a patient to view profile.');
        }

        $profile = $patientInsightService->profile(
            auth()->user()->branch_id,
            Carbon::now('Asia/Dhaka'),
            $phone,
            $userId
        );

        if (!$profile) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Patient profile not found.');
        }

        return view('backend.pages.patients.profile-360', [
            'profile' => $profile,
            'segmentLabels' => PatientInsightService::segmentLabels(),
        ]);
    }

    public function search(Request $request, PatientInsightService $patientInsightService): JsonResponse
    {
        $this->checkOwnPermission('users.index');

        $query = trim((string) $request->query('q', ''));
        $results = $patientInsightService->search(auth()->user()->branch_id, $query, 8);

        return response()->json([
            'results' => collect($results)->map(function ($row) {
                $params = array_filter([
                    'phone' => $row['phone'] !== '—' ? $row['phone'] : null,
                    'user_id' => $row['user_id'] ?? null,
                ]);

                return array_merge($row, [
                    'profile_url' => route('admin.patients.profile', $params),
                ]);
            })->values(),
        ]);
    }
}
