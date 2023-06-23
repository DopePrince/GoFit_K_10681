<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class class_on_running_daily extends Model
{
    use HasFactory;

    protected $table = 'class_on_running_dailies';
    protected $primaryKey = 'ID_CLASS_ON_RUNNING_DAILY';
    protected $fillable = [
        'ID_CLASS_ON_RUNNING_DAILY',
        'ID_CLASS_ON_RUNNING',
        'ID_INSTRUCTOR',
        'DATE',
        'DAY_NAME',
        'START_CLASS',
        'END_CLASS',
        'STATUS',
        'CLASS_CAPACITY'
    ];

    public function class_on_running()
    {
        return $this->belongsTo(class_on_running::class, 'ID_CLASS_ON_RUNNING', 'ID_CLASS_ON_RUNNING');
    }

    public function instructor()
    {
        return $this->belongsTo(instructor::class, 'ID_INSTRUCTOR', 'ID_INSTRUCTOR');
    }

    public function class_detail()
    {
        return $this->belongsTo(class_detail::class, 'ID_CLASS', 'ID_CLASS');
    }

    public function instructor_absent()
    {
        return $this->hasMany(instructor_absent::class, 'ID_INSTRUCTOR_ABSENT', 'id');
    }

    public function class_booking()
    {
        return $this->hasMany(class_booking::class, 'ID_CLASS_BOOKING', 'id');
    }

    public function instructor_attendance()
    {
        return $this->hasMany(instructor_attendance::class, 'ID_INSTRUCTOR_ATTENDANCE', 'id');
    }
}
