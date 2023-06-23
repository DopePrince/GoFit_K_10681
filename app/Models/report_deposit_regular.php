<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class report_deposit_regular extends Model
{
    use HasFactory;

    protected $table = 'report_deposit_regulars';
    public $incrementing = false;
    protected $primaryKey = 'NO_STRUK_REGULAR';
    protected $fillable = [
        'NO_STRUK_REGULAR',
        'ID_MEMBER',
        'ID_PEGAWAI',
        'ID_PROMO_REGULAR',
        'TANGGAL_TRANSAKSI',
        'TOPUP_AMOUNT',
        'BONUS',
        'REMAINING_REGULAR',
        'TOTAL_REGULAR'
    ];

    public function member()
    {
        return $this->belongsTo(member::class, 'ID_MEMBER', 'ID_MEMBER');
    }

    public function pegawai()
    {
        return $this->belongsTo(pegawai::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function promo_regular()
    {
        return $this->belongsTo(promo_regular::class, 'ID_PROMO_REGULAR', 'ID_PROMO_REGULAR');
    }
}
