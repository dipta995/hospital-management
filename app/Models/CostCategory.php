<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCategory extends Model
{
    use HasFactory;
    protected $table = 'cost_categories';
    protected $fillable = ['branch_id','name'];
    public function costs()
    {
        return $this->hasMany(Cost::class);
    }
}
