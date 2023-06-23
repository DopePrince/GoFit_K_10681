<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gym_booking extends Model
{
    use HasFactory;

    protected $table = 'gym_bookings';
    public $incrementing = false;
    protected $primaryKey = 'ID_GYM_BOOKING';
    protected $fillable = [
        'ID_GYM_BOOKING',
        'ID_MEMBER',
        'ID_GYM',
        'DATE_TIME_BOOKING',
        'DATE_TIME_PRESENSI'
    ];

    public function member()
    {
        return $this->belongsTo(member::class, 'ID_MEMBER', 'ID_MEMBER');
    }

    public function gym()
    {
        return $this->belongsTo(gym::class, 'ID_GYM', 'ID_GYM');
    }

    public function gym_attendance()
    {
        return $this->hasMany(gym_attendance::class, 'ID_GYM_ATTENDANCE', 'id');
    }
}
