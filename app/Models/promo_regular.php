<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class promo_regular extends Model
{
    use HasFactory;

    protected $table = 'promo_regulars';
    public $incrementing = false;
    protected $primaryKey = 'ID_PROMO_REGULAR';
    protected $fillable = [
        'ID_PROMO_REGULAR',
        'TOPUP_AMOUNT',
        'BONUS_REGULAR',
        'MIN_DEPOSIT'
    ];

    public function report_deposit_regular()
    {
        return $this->hasMany(report_deposit_regular::class, 'NO_STRUK_REGULAR', 'id');
    }
}
