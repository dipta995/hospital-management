<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    public const UPDATED_AT = null;

    public const EVENT_LOGIN = 'login';
    public const EVENT_FAILED = 'failed';
    public const EVENT_LOGOUT = 'logout';
    public const EVENT_LOCKED = 'locked';

    protected $fillable = [
        'branch_id',
        'admin_id',
        'email',
        'event',
        'ip_address',
        'user_agent',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }
}
