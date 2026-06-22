<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiInsight extends Model
{
    protected $fillable = [
        'branch_id',
        'type',
        'context_key',
        'content',
        'source',
    ];
}
