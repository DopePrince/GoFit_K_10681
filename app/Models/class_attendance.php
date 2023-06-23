<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class class_attendance extends Model
{
    use HasFactory;

    protected $table = 'class_attendances';
    public $incrementing = false;
    protected $primaryKey = 'ID_CLASS_ATTENDANCE';
    protected $fillable = [
        'ID_CLASS_ATTENDANCE',
        'ID_CLASS_BOOKING',
        'DATE_TIME',
        'SISA_DEPOSIT_REGULAR',
    ];

    public function class_booking()
    {
        return $this->belongsTo(class_booking::class, 'ID_CLASS_BOOKING', 'ID_CLASS_BOOKING');
    }
}
