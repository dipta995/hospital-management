<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorRoom extends Model
{
    use HasFactory;

    public function doctor()
    {
        return $this->belongsTo(Reefer::class, 'reefer_id', 'id');
    }
}
