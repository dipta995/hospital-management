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
    return $this->belongsTo(Reefer::class,'refer_id','id');
}    public function drreefer()
{
    return $this->belongsTo(Reefer::class,'dr_refer_id','id');
}

    public function bedCabin()
    {
        return $this->belongsTo(BedCabin::class, 'bed_cabin_id');
    }

}
