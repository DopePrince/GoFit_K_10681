<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class instructor_attendance extends Model
{
    use HasFactory;

    protected $table = 'instructor_attendances';
    protected $primaryKey = 'ID_INSTRUCTOR_ATTENDANCE';
    protected $fillable = [
        'ID_INSTRUCTOR_ATTENDANCE',
        'ID_INSTRUCTOR',
        'ID_CLASS_ON_RUNNING_DAILY',
        'START_TIME',
        'END_TIME',
        'IS_ATTENDED',
        'LATE_AMOUNT',
        'DATE_TIME_PRESENSI'
    ];

    public function instructor()
    {
        return $this->belongsTo(instructor::class, 'ID_INSTRUCTOR', 'ID_INSTRUCTOR');
    }

    public function class_on_running_daily()
    {
        return $this->belongsTo(class_on_running_daily::class, 'ID_CLASS_ON_RUNNING_DAILY', 'ID_CLASS_ON_RUNNING_DAILY');
    }
}
