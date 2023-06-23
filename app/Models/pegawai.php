<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawais';
    public $incrementing = false;
    protected $hidden = ['PASSWORD'];
    protected $primaryKey = 'ID_PEGAWAI';
    protected $fillable = [
        'ID_PEGAWAI',
        'FULL_NAME',
        'GENDER',
        'TANGGAL_LAHIR',
        'PHONE_NUMBER',
        'ADDRESS',
        'EMAIL',
        'PASSWORD',
        'ROLE'
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
}
