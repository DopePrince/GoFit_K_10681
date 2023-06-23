<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class class_booking extends Model
{
    use HasFactory;

    protected $table = 'class_bookings';
    public $incrementing = false;
    protected $primaryKey = 'ID_CLASS_BOOKING';
    protected $fillable = [
        'ID_CLASS_BOOKING',
        'ID_MEMBER',
        'ID_CLASS_ON_RUNNING_DAILY',
        'DATE_TIME',
        'PAYMENT_TYPE',
        'STATUS_PRESENSI'
    ];

    public function member()
    {
        return $this->belongsTo(member::class, 'ID_MEMBER', 'ID_MEMBER');
    }

    public function class_on_running_daily()
    {
        return $this->belongsTo(class_on_running_daily::class, 'ID_CLASS_ON_RUNNING_DAILY', 'ID_CLASS_ON_RUNNING_DAILY');
    }
}
