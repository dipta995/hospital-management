<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
    public function customPercent()
    {
        return $this->hasOne(CustomPercent::class, 'category_id', 'id')
            ->where('branch_id', auth()->user()->branch_id);
    }



}
