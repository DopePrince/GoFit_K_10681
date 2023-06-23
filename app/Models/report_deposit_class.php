<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class report_deposit_class extends Model
{
    use HasFactory;

    protected $table = 'report_deposit_classes';
    public $incrementing = false;
    protected $primaryKey = 'NO_STRUK_CLASS';
    protected $fillable = [
        'NO_STRUK_CLASS',
        'ID_MEMBER',
        'ID_PEGAWAI',
        'ID_CLASS',
        'ID_PROMO_CLASS',
        'TANGGAL_TRANSAKSI',
        'TOTAL_PRICE',
        'TOTAL_PACKAGE',
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

    public function class_detail()
    {
        return $this->belongsTo(class_detail::class, 'ID_CLASS', 'ID_CLASS');
    }

    public function promo_class()
    {
        return $this->belongsTo(promo_class::class, 'ID_PROMO_CLASS', 'ID_PROMO_CLASS');
    }

}
