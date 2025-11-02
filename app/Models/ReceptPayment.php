<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceptPayment extends Model
{
    use HasFactory;

    protected $table = 'recept_payments';
    protected $fillable = [
        'recept_id',
        'branch_id',
        'admin_id',
        'recept_id',
        'paid_amount',
        'creation_date'
    ];
}
