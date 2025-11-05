<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admit extends Model
{
    use HasFactory;


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reefer()
{
    return $this->belongsTo(Reefer::class,'reffer_id','id');
}

}
