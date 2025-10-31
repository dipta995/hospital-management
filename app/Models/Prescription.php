<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'invoice_id',
        'reefer_id',
        'investigation',
        'diagnosis',
    ];


    // A prescription belongs to a doctor
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
    public function doctor()
    {
        return $this->belongsTo(Reefer::class, 'reefer_id');
    }

    // A prescription has many drugs
    public function drugs()
    {
        return $this->hasMany(Drug::class);
    }
}
