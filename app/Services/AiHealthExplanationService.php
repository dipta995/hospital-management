<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AiHealthExplanationService
{
    public function __construct(
        private AiClientService $aiClient,
        private PatientInsightService $patientInsightService,
        private AiIntelligenceService $intelligence,
    ) {}

    public function explain(int $branchId, ?string $phone, ?int $userId): array
    {
        $profile = $this->patientInsightService->profile(
            $branchId,
            Carbon::now('Asia/Dhaka'),
            $phone,
            $userId
        );

        if (!$profile) {
            return [
                'success' => false,
                'message' => 'Patient not found.',
            ];
        }

        $risk = $this->intelligence->analyzePatient($profile, $branchId);
        $context = $this->buildPatientContext($branchId, $profile, $risk);
        $patientName = $profile['patient']['name'] ?? 'Patient';

        $result = $this->aiClient->complete(
            'You are a clinical information assistant for hospital staff. Explain the patient health picture in plain language. Include risk factors and care suggestions. Do not diagnose or prescribe. Under 250 words.',
            $context,
            fn () => $this->fallbackExplanation($profile, $risk)
        );

        return [
            'success' => true,
            'patient_name' => $patientName,
            'content' => $result['content'],
            'source' => $result['source'],
            'risk_score' => $risk['risk_score'],
            'risk_level' => $risk['risk_level'],
            'factors' => $risk['factors'],
            'care_plan' => $risk['care_plan'],
        ];
    }

    private function buildPatientContext(int $branchId, array $profile, array $risk): string
    {
        $p = $profile['patient'];
        $stats = $profile['stats'];
        $due = $profile['due'];

        $lines = [
            'Patient: '.($p['name'] ?? 'Unknown'),
            'Age: '.($p['age'] ?? 'N/A').', Gender: '.($p['gender'] ?? 'N/A'),
            'Blood group: '.($p['blood_group'] ?? 'N/A'),
            'Segment: '.($profile['segment_label'] ?? 'N/A'),
            'Risk score: '.$risk['risk_score'].'/100 ('.$risk['risk_level'].')',
            'Total visits: '.($stats['visit_count'] ?? 0),
            'Lifetime spent: '.($stats['total_spent'] ?? 0),
            'Outstanding due: '.($due['total'] ?? 0),
        ];

        foreach ($risk['factors'] as $factor) {
            $lines[] = 'Factor ['.($factor['impact'] ?? '').']: '.($factor['label'] ?? '').' — '.($factor['detail'] ?? '');
        }

        foreach ($risk['care_plan'] as $step) {
            $lines[] = 'Care step: '.$step;
        }

        if (!empty($profile['predictions'])) {
            foreach ($profile['predictions'] as $pred) {
                if (is_array($pred)) {
                    $lines[] = 'Alert: '.($pred['title'] ?? '').' — '.($pred['subtitle'] ?? '');
                } else {
                    $lines[] = 'Prediction: '.$pred;
                }
            }
        }

        $phone = $p['phone'] ?? null;

        if ($phone && $phone !== '—') {
            $recentLabs = Invoice::with(['invoiceList.product'])
                ->where('branch_id', $branchId)
                ->where('patient_phone', $phone)
                ->orderByDesc('creation_date')
                ->limit(3)
                ->get();

            foreach ($recentLabs as $invoice) {
                foreach ($invoice->invoiceList as $item) {
                    if ($item->status === 'Complete' && $item->test_report) {
                        $lines[] = 'Recent test: '.($item->product?->name ?? 'Test').' — '.Str::limit(strip_tags((string) $item->test_report), 200);
                    }
                }

                $rx = Prescription::where('branch_id', $branchId)
                    ->where('invoice_id', $invoice->id)
                    ->first();

                if ($rx) {
                    $lines[] = 'Latest prescription diagnosis: '.Str::limit((string) $rx->diagnosis, 200);
                    $lines[] = 'Investigation: '.Str::limit((string) $rx->investigation, 200);
                    break;
                }
            }
        }

        return implode("\n", $lines);
    }

    private function fallbackExplanation(array $profile, array $risk): string
    {
        $p = $profile['patient'];
        $stats = $profile['stats'];
        $name = $p['name'] ?? 'Patient';
        $visits = $stats['visit_count'] ?? 0;
        $segment = $profile['segment_label'] ?? 'Unknown';

        $text = "{$name} — {$segment}, {$visits} visit(s). Risk score {$risk['risk_score']}/100 ({$risk['risk_level']}). ";

        if (!empty($risk['factors'])) {
            $text .= 'Factors: '.collect($risk['factors'])->pluck('label')->implode(', ').'. ';
        }

        if (!empty($risk['care_plan'])) {
            $text .= 'Recommended: '.implode('; ', $risk['care_plan']).'.';
        }

        return trim($text);
    }
}
