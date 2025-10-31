<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestReport extends Model
{
    use HasFactory;

    public function reportDemo()
    {
        return $this->belongsTo(TestReportDemo::class,'test_report_id' );
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class,'invoice_id' );
    }
}
