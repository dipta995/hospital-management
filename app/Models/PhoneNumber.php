<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id',
        'number_category_id',
        'name',
        'address',
        'number',
    ];
    public function numberCategory()
    {
        return $this->belongsTo(NumberCategory::class);
    }
}
