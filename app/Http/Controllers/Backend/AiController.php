<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\AiBusinessAnalyticsService;
use App\Services\AiChatService;
use App\Services\AiHealthExplanationService;
use App\Services\AiReportSummaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct()
    {
        $this->checkGuard();
    }

    public function reportSummary(Request $request, AiReportSummaryService $service): JsonResponse
    {
        $this->checkOwnPermission('ai.reports');

        $request->validate([
            'invoice_list_id' => 'required|integer',
        ]);

        $result = $service->summarizeInvoiceLine(
            (int) $request->invoice_list_id,
            (int) auth()->user()->branch_id
        );

        return response()->json([
            'success' => true,
            'summary' => $result['content'],
            'source' => $result['source'],
            'test_name' => $result['test_name'] ?? null,
            'abnormalities' => $result['abnormalities'] ?? [],
            'flag_count' => $result['flag_count'] ?? 0,
            'interpretation' => $result['interpretation'] ?? null,
            'follow_up' => $result['follow_up'] ?? null,
        ]);
    }

    public function healthExplain(Request $request, AiHealthExplanationService $service): JsonResponse
    {
        $this->checkOwnPermission('ai.health');

        $request->validate([
            'phone' => 'nullable|string',
            'user_id' => 'nullable|integer',
        ]);

        $result = $service->explain(
            (int) auth()->user()->branch_id,
            $request->query('phone') ?? $request->input('phone'),
            $request->query('user_id') ? (int) $request->query('user_id') : ($request->input('user_id') ? (int) $request->input('user_id') : null)
        );

        return response()->json($result, ($result['success'] ?? false) ? 200 : 404);
    }

    public function chat(Request $request, AiChatService $service): JsonResponse
    {
        $this->checkOwnPermission('ai.chat');

        $request->validate([
            'message' => 'required|string|max:2000',
            'session_id' => 'nullable|integer',
        ]);

        $result = $service->chat(
            (int) auth()->user()->branch_id,
            (int) auth('admin')->id(),
            $request->input('message'),
            $request->input('session_id') ? (int) $request->input('session_id') : null
        );

        return response()->json($result, ($result['success'] ?? false) ? 200 : 422);
    }

    public function chatHistory(Request $request, AiChatService $service): JsonResponse
    {
        $this->checkOwnPermission('ai.chat');

        return response()->json(
            $service->history(
                (int) auth()->user()->branch_id,
                (int) auth('admin')->id(),
                $request->query('session_id') ? (int) $request->query('session_id') : null
            )
        );
    }

    public function chatSuggestions(AiChatService $service): JsonResponse
    {
        $this->checkOwnPermission('ai.chat');

        return response()->json([
            'success' => true,
            ...$service->suggestions((int) auth()->user()->branch_id),
        ]);
    }

    public function advancedAnalytics(AiBusinessAnalyticsService $service): JsonResponse
    {
        $this->checkOwnPermission('ai.analytics');

        $branchId = (int) auth()->user()->branch_id;
        $analysis = $service->advanced($branchId);

        return response()->json([
            'success' => true,
            ...$analysis,
        ]);
    }

    public function businessInsights(Request $request, AiBusinessAnalyticsService $service): JsonResponse
    {
        $this->checkOwnPermission('ai.analytics');

        $payload = $service->insights(
            (int) auth()->user()->branch_id,
            $request->boolean('refresh')
        );

        return response()->json([
            'success' => true,
            'insights' => $payload['content'],
            'source' => $payload['source'],
            'generated_at' => $payload['generated_at'],
            'health_score' => $payload['health_score'] ?? null,
            'health_label' => $payload['health_label'] ?? null,
            'kpis' => $payload['kpis'] ?? [],
            'priority_actions' => $payload['priority_actions'] ?? [],
            'insights_list' => $payload['insights'] ?? [],
            'forecasts' => $payload['forecasts'] ?? [],
        ]);
    }
}
