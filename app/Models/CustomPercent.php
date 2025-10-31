<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomPercent extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'refer_id',
        'category_id',
        'percentage',
    ];
    public function branch()
    {
        return $this->belongsTo(Reefer::class, 'refer_id', 'id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
