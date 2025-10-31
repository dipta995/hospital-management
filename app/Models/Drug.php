<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'name',
        'rule',
        'time',
        'note',
        'duration',
    ];

    // A drug belongs to one prescription
    public function prescription()
    {
        return $this->belongsTo(Prescription::class,'prescription_id');
    }
}
