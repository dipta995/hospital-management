<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceptPayment extends Model
{
    use HasFactory;

    protected $table = 'recept_payments';
    protected $fillable = [
        'admit_id',
        'branch_id',
        'admin_id',
        'paid_amount',
        'creation_date'
    ];
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function admit()
    {
        return $this->belongsTo(Admit::class, 'admit_id', 'id');
    }

}
