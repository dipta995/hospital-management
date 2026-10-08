<?php

namespace App\Services;

use App\Models\Cost;
use App\Models\Invoice;
use App\Models\InvoiceList;
use App\Models\InvoicePayment;
use Illuminate\Support\Facades\DB;

class InvoiceDeletionService
{
    public const AUDIT_RELATIONS = ['invoiceList.product', 'paidAmount', 'costs.category', 'reeferDr', 'reeferBy', 'admin'];

    public function __construct(
        private AuditLogService $audit,
        private SecurityService $security
    ) {
    }

    /**
     * Snapshots the invoice into the trash, then removes it with its lines, payments and costs.
     */
    public function delete(Invoice $invoice, ?string $reason): void
    {
        $oldSnapshot = $this->audit->snapshot($invoice, self::AUDIT_RELATIONS);
        $branchId = (int) $invoice->branch_id;

        DB::transaction(function () use ($invoice, $oldSnapshot, $reason) {
            $this->audit->record('invoice', 'deleted', $invoice, $oldSnapshot, null, $reason);
            InvoiceList::where('invoice_id', $invoice->id)->delete();
            InvoicePayment::where('invoice_id', $invoice->id)->delete();
            Cost::where('invoice_id', $invoice->id)->delete();
            $invoice->delete();
        });

        $this->security->afterInvoiceDeleted($oldSnapshot, $branchId, $reason);
    }
}
