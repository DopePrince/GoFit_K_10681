<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class class_on_running extends Model
{
    use HasFactory;

    protected $table = 'class_on_runnings';
    protected $primaryKey = 'ID_CLASS_ON_RUNNING';
    public $incrementing = false;
    protected $fillable = [
        'ID_CLASS_ON_RUNNING',
        'ID_INSTRUCTOR',
        'ID_CLASS',
        'DATE',
        'DAY_NAME',
        'START_CLASS',
        'END_CLASS',
    ];

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

    public function class_on_running_daily()
    {
        return $this->hasMany(class_on_running_daily::class, 'ID_CLASS_ON_RUNNING_DAILY', 'id');
    }
}
