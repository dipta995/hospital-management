<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceList extends Model
{
    use HasFactory;

    protected $table = 'invoice_lists';
    protected $fillable = [
        'branch_id',
        'invoice_id',
        'admin_id',
        'product_id',
        'price',
        'discount_price',
        'refer_fee',
        'test_report',
        'document',
        'status',
    ];
    public static $statusArray = ['Pending', 'Processing', 'Complete', 'Rejected'];
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
