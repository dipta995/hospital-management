<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reefer extends Model
{
    use HasFactory;

    public static $typeArray = ['Doctor','Other'];
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
    public function customParcent()
    {
        return $this->hasMany(CustomPercent::class, 'refer_id', 'id');
    }
}
