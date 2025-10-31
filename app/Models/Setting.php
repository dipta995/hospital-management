<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = ['branch_id','key', 'value'];

    public static function get($key, $default = null)
    {
        $setting = self::where('branch_id', auth()->user()->branch_id)->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        return self::updateOrCreate(
            ['branch_id' => auth()->user()->branch_id, 'key' => $key],
            ['value' => $value]
        );
    }


    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
