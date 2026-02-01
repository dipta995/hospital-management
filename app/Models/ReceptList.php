<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceptList extends Model
{
    use HasFactory;


    protected $fillable = [
        'branch_id',
        'user_id',
        'recept_id',
        'service_id',
        'price',
        'discount',
    ];
    public function service(){
        return $this->belongsTo(Service::class);
    }
    public function recept()
    {
        return $this->belongsTo(Recept::class, 'recept_id', 'id');
    } public function services()
    {
        return $this->belongsTo(Service::class);
    }
}
