<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReagentTrack extends Model
{
    use HasFactory;
    public function purchaseItem()
    {
        return $this->belongsTo(PurchaseItem::class, 'purchase_item_id', 'id');
    }
}
