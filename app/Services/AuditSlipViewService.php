<?php

namespace App\Services;

use stdClass;

class AuditSlipViewService
{
    public function invoiceSlipFromAudit(?array $values): ?array
    {
        if (empty($values)) {
            return null;
        }

        $doctorName = $values['dr_name']
            ?? data_get($values, 'reefer_dr.name')
            ?? data_get($values, 'reeferDr.name')
            ?? '—';

        $invoice = new stdClass();
        $invoice->invoice_number = $values['invoice_number'] ?? '—';
        $invoice->patient_name = $values['patient_name'] ?? '—';
        $invoice->patient_phone = $values['patient_phone'] ?? '—';
        $invoice->total_amount = (float) ($values['total_amount'] ?? 0);
        $invoice->discount_amount = (float) ($values['discount_amount'] ?? 0);
        $invoice->dr_name = $doctorName;
        $invoice->reeferDr = null;

        $products = [];
        $lines = $values['invoice_list'] ?? $values['invoiceList'] ?? [];

        foreach ($lines as $line) {
            if (!is_array($line)) {
                continue;
            }

            $products[] = [
                'product_name' => data_get($line, 'product.name')
                    ?? $line['product_name']
                    ?? 'Test',
                'price' => (float) ($line['price'] ?? 0),
            ];
        }

        $paid = 0.0;
        $payments = $values['paid_amount'] ?? $values['paidAmount'] ?? [];

        if (is_array($payments)) {
            foreach ($payments as $payment) {
                if (!is_array($payment)) {
                    continue;
                }
                $paid += (float) ($payment['paid_amount'] ?? 0);
            }
        }

        if (isset($values['paid_amount_sum_paid_amount'])) {
            $paid = (float) $values['paid_amount_sum_paid_amount'];
        }

        return [
            'invoice' => $invoice,
            'products' => $products,
            'paid' => $paid,
        ];
    }
}
