<?php

namespace App\Services;

use App\Models\AiInsight;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class AiBusinessAnalyticsService
{
    public function __construct(
        private AiClientService $aiClient,
        private AiIntelligenceService $intelligence,
    ) {}

    public function insights(int $branchId, bool $refresh = false): array
    {
        $contextKey = Carbon::now('Asia/Dhaka')->format('Y-m-d-H');
        $cacheKey = "ai_business_insights_v2_{$branchId}_{$contextKey}";

        if (!$refresh) {
            $cached = Cache::get($cacheKey);

            if (is_array($cached)) {
                return $cached;
            }
        }

        $analysis = $this->intelligence->businessAnalysis($branchId);

        $narrative = $this->aiClient->complete(
            'You are a senior hospital operations analyst. Given structured KPIs and priority actions, write a concise executive narrative (3-5 sentences). Focus on revenue, operations risk, and what leadership should do today. No patient names.',
            $this->formatAnalysisPrompt($analysis),
            fn () => $this->fallbackNarrative($analysis)
        );

        $payload = [
            'content' => $narrative['content'],
            'source' => $narrative['source'],
            'generated_at' => Carbon::now('Asia/Dhaka')->format('d M Y h:i A'),
            'health_score' => $analysis['health_score'],
            'health_label' => $analysis['health_label'],
            'kpis' => $analysis['kpis'],
            'priority_actions' => $analysis['priority_actions'],
            'insights' => $analysis['insights'],
            'forecasts' => $analysis['forecasts'],
        ];

        Cache::put($cacheKey, $payload, 3600);

        if (Schema::hasTable('ai_insights')) {
            AiInsight::create([
                'branch_id' => $branchId,
                'type' => 'business_analytics',
                'context_key' => $contextKey,
                'content' => $payload['content'],
                'source' => $payload['source'],
            ]);
        }

        return $payload;
    }

    public function advanced(int $branchId): array
    {
        return $this->intelligence->businessAnalysis($branchId);
    }

    private function formatAnalysisPrompt(array $analysis): string
    {
        $lines = [
            'Health score: '.$analysis['health_score'].'/100 ('.$analysis['health_label'].')',
        ];

        foreach ($analysis['kpis'] as $kpi) {
            $lines[] = ($kpi['label'] ?? '').': '.($kpi['value'] ?? '');
        }

        foreach ($analysis['priority_actions'] as $action) {
            $lines[] = "[{$action['severity']}] {$action['title']}: {$action['detail']}";
        }

        foreach ($analysis['insights'] as $insight) {
            $lines[] = 'Insight: '.$insight;
        }

        return implode("\n", $lines);
    }

    private function fallbackNarrative(array $analysis): string
    {
        $score = $analysis['health_score'];
        $label = $analysis['health_label'];
        $text = "Operations health score: {$score}/100 ({$label}). ";

        if (!empty($analysis['insights'])) {
            $text .= implode(' ', $analysis['insights']).' ';
        }

        $actions = $analysis['priority_actions'];
        if (!empty($actions)) {
            $top = $actions[0];
            $text .= "Top priority: {$top['title']} — {$top['detail']}";
        } else {
            $text .= 'No critical alerts — continue standard monitoring.';
        }

        return trim($text);
    }
}
