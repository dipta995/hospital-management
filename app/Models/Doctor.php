<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
    //protected $table = 'reefers';


    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
