<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recept extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'recepts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'admin_id',
        'branch_id',
        'total_amount',
        'discount_amount',
        'created_date'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }   public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function receptPayments()
    {
        return $this->hasMany(ReceptPayment::class, 'recept_id', 'id');
    }
    public function receptList()
    {
        return $this->hasMany(ReceptList::class, 'recept_id', 'id');
    }



}
