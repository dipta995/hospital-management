<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestReport extends Model
{
    use HasFactory;

    // Link saved report to the specific invoice test (invoice_lists row)
    public function invoiceItem()
    {
        return $this->belongsTo(InvoiceList::class, 'test_report_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
