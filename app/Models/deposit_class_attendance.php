<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class deposit_class_attendance extends Model
{
    use HasFactory;

    protected $table = 'deposit_class_attendances';
    public $incrementing = false;
    protected $primaryKey = 'ID_DEPOSIT_CLASS_ATTENDANCE';
    protected $fillable = [
        'ID_DEPOSIT_CLASS_ATTENDANCE',
        'ID_CLASS_BOOKING',
        'TANGGAL_TRANSAKSI',
        'SISA_DEPOSIT_CLASS',
        'EXPIRE_DATE'
    ];

    public function class_booking()
    {
        return $this->belongsTo(class_booking::class, 'ID_CLASS_BOOKING', 'ID_CLASS_BOOKING');
    }
}
