<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorSerial extends Model
{
    use HasFactory;
    protected $table = 'doctor_serials';
    protected $fillable = [
        'branch_id',
        'reefer_id',
        'patient_name',
        'patient_age_year',
        'patient_phone',
        'patient_email',
        'patient_gender',
        'patient_blood_group',
        'patient_address',
        'serial_number',
        'amount',
        'date',
        'remarks',
        'status',
    ];
    public static $statusArray=['Pending','Checking','Complete','Rejected'];
    public function doctor()
    {
        return $this->belongsTo(Reefer::class, 'reefer_id', 'id');
    }
}
