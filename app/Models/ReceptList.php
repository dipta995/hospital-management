<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceptList extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'user_id',
        'recept_id',
        'service_id',
        'price',
        'discount',
        'amount',
        'branch_id',
    ];
}
