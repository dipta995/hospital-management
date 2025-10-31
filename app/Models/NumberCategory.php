<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumberCategory extends Model
{
    protected $table = 'number_categories';
    protected $fillable = ['branch_id','name'];
    public function phoneNumbers()
    {
        return $this->hasMany(PhoneNumber::class);
    }
}
