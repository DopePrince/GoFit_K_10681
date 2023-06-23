<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class member extends Model
{
    use HasFactory;

    protected $table = 'members';
    public $incrementing = false;
    protected $hidden = ['PASSWORD'];
    protected $primaryKey = 'ID_MEMBER';
    protected $fillable = [
        'ID_MEMBER',
        'FULL_NAME',
        'GENDER',
        'TANGGAL_LAHIR',
        'PHONE_NUMBER',
        'ADDRESS',
        'EMAIL',
        'PASSWORD',
        'DEPOSIT_REGULAR_AMOUNT',
        'EXPIRE_DATE',
        'STATUS_MEMBERSHIP'
    ];

    public function aktivasi_tahunan()
    {
        return $this->hasMany(report_aktivasi::class, 'NO_STRUK_AKTIVASI', 'id');
    }

    public function report_deposit_regular()
    {
        return $this->hasMany(report_deposit_regular::class, 'NO_STRUK_REGULAR', 'id');
    }

    public function report_deposit_class()
    {
        return $this->hasMany(report_deposit_class::class, 'NO_STRUK_CLASS', 'id');
    }

    public function class_deposit()
    {
        return $this->hasMany(class_deposit::class, 'ID_CLASS_DEPOSIT', 'id');
    }

    public function class_booking()
    {
        return $this->hasMany(class_booking::class, 'ID_CLASS_BOOKING', 'id');
    }

    public function gym_booking()
    {
        return $this->hasMany(gym_booking::class, 'ID_GYM_BOOKING', 'id');
    }
}
