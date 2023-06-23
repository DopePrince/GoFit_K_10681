<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gym_attendance extends Model
{
    use HasFactory;

    protected $table = 'gym_attendances';
    public $incrementing = false;
    protected $primaryKey = 'ID_GYM_ATTENDACE';
    protected $fillable = [
        'ID_GYM_ATTENDANCE',
        'ID_GYM_BOOKING',
        'DATE_TIME',
        'BOOKED_SLOT'
    ];

    public function gym_booking()
    {
        return $this->belongsTo(gym_booking::class, 'ID_GYM_BOOKING', 'ID_GYM_BOOKING');
    }
}
