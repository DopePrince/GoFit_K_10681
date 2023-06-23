<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class report_aktivasi extends Model
{
    use HasFactory;

    protected $table = 'report_aktivasis';
    public $incrementing = false;
    protected $primaryKey = 'NO_STRUK_AKTIVASI';
    protected $fillable = [
        'NO_STRUK_AKTIVASI',
        'ID_MEMBER',
        'ID_PEGAWAI',
        'TANGGAL_TRANSAKSI',
        'PRICE',
        'EXPIRE_DATE'
    ];

    public function member()
    {
        return $this->belongsTo(member::class, 'ID_MEMBER', 'ID_MEMBER');
    }

    public function pegawai()
    {
        return $this->belongsTo(pegawai::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }
}
