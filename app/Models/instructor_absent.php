<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class instructor_absent extends Model
{
    use HasFactory;

    protected $table = 'instructor_absents';
    protected $primaryKey = 'ID_INSTRUCTOR_ABSENT';
    protected $fillable = [
        'ID_INSTRUCTOR_ABSENT',
        'ID_INSTRUCTOR',
        'ID_SUBSTITUTE_INSTRUCTOR',
        'ID_CLASS_ON_RUNNING_DAILY',
        'ABSENT_DATE_TIME',
        'ABSENT_REASON',
        'IS_CONFIRMED'
    ];

    public function instructor()
    {
        return $this->belongsTo(instructor::class, 'ID_INSTRUCTOR, ID_SUBSTITUTE_INSTRUCTOR', 'ID_INSTRUCTOR');
    }

    public function class_on_running_daily()
    {
        return $this->belongsTo(class_on_running_daily::class, 'ID_CLASS_ON_RUNNING_DAILY', 'ID_CLASS_ON_RUNNING_DAILY');
    }
}
