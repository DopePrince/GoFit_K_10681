<?php

namespace App\Models;

use COM;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class instructor extends Model
{
    use HasFactory;

    protected $table = 'instructors';
    public $incrementing = false;
    protected $hidden = ['PASSWORD'];
    protected $primaryKey = 'ID_INSTRUCTOR';
    protected $fillable = [
        'ID_INSTRUCTOR',
        'FULL_NAME',
        'GENDER',
        'TANGGAL_LAHIR',
        'PHONE_NUMBER',
        'ADDRESS',
        'EMAIL',
        'PASSWORD',
        'LATE_AMOUNT'
    ];

    public function class_on_running()
    {
        return $this->hasMany(class_on_running::class, 'ID_CLASS_ON_RUNNING', 'id');
    }

    public function class_on_running_daily()
    {
        return $this->hasMany(class_on_running_daily::class, 'ID_CLASS_ON_RUNNING_DAILY', 'id');
    }

    public function instructor_absent()
    {
        return $this->hasMany(instructor_absent::class, 'ID_INSTRUCTOR_ABSENT', 'id');
    }

    public function instructor_attendance()
    {
        return $this->hasMany(instructor_attendance::class, 'ID_INSTRUCTOR_ATTENDANCE', 'id');
    }
}
