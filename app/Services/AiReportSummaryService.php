<?php

namespace App\Services;

use App\Models\InvoiceList;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AiReportSummaryService
{
    public function __construct(
        private AiClientService $aiClient,
        private AiIntelligenceService $intelligence,
    ) {}

    public function summarizeInvoiceLine(int $invoiceListId, int $branchId): array
    {
        $line = InvoiceList::with(['product', 'invoice'])
            ->where('branch_id', $branchId)
            ->findOrFail($invoiceListId);

        $analysis = $this->intelligence->analyzeLabLine($line);

        $cached = $line->ai_summary ?? null;
        if (is_string($cached) && trim($cached) !== '' && Schema::hasColumn('invoice_lists', 'ai_summary')) {
            return array_merge($this->buildPayload($cached, 'cache', $line, $analysis), [
                'from_cache' => true,
            ]);
        }

        $context = $this->buildLineContext($line, $analysis);

        $result = $this->aiClient->complete(
            'You are a medical lab report assistant. Write a concise plain-language summary for hospital staff. Highlight abnormal parameters. Do not diagnose. Under 200 words.',
            $context,
            fn () => $this->fallbackSummary($line, $analysis)
        );

        if (Schema::hasColumn('invoice_lists', 'ai_summary')) {
            $line->ai_summary = $result['content'];
            $line->save();
        }

        return $this->buildPayload($result['content'], $result['source'], $line, $analysis);
    }

    private function buildPayload(string $content, string $source, InvoiceList $line, array $analysis): array
    {
        return [
            'content' => $content,
            'source' => $source,
            'test_name' => $line->product?->name,
            'abnormalities' => $analysis['flags'],
            'flag_count' => $analysis['flag_count'],
            'interpretation' => $analysis['interpretation'],
            'follow_up' => $analysis['follow_up'],
        ];
    }

    private function buildLineContext(InvoiceList $line, array $analysis): string
    {
        $invoice = $line->invoice;
        $reportText = trim(strip_tags((string) $line->test_report));

        $lines = array_filter([
            'Test: '.($line->product?->name ?? 'Unknown'),
            'Patient age: '.($invoice?->patient_age ?? 'N/A'),
            'Patient gender: '.($invoice?->patient_gender ?? 'N/A'),
            'Status: '.$line->status,
            'Flagged parameters: '.$analysis['flag_count'],
        ]);

        foreach ($analysis['flags'] as $flag) {
            $lines[] = sprintf(
                '- %s: %s %s (ref %s) [%s]',
                $flag['parameter'],
                $flag['value'] ?? '—',
                $flag['unit'] ?? '',
                $flag['reference'],
                $flag['status']
            );
        }

        $lines[] = 'Report data:';
        $lines[] = Str::limit($reportText, 3000);

        return implode("\n", $lines);
    }

    private function fallbackSummary(InvoiceList $line, array $analysis): string
    {
        $name = $line->product?->name ?? 'Test';
        $status = $line->status;

        if ($status !== 'Complete' || empty($line->test_report)) {
            return "{$name} — {$status}. Complete the report to generate a summary.";
        }

        $text = "{$name}: {$analysis['interpretation']}";

        if ($analysis['flag_count'] > 0) {
            $flags = collect($analysis['flags'])->take(3)->map(function ($f) {
                return $f['parameter'].' ('.$f['status'].')';
            })->implode(', ');
            $text .= " Flagged: {$flags}.";
        }

        $text .= ' '.$analysis['follow_up'];

        return $text;
    }
}
