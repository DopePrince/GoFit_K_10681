<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class promo_class extends Model
{
    use HasFactory;

    protected $table = 'promo_classes';
    public $incrementing = false;
    protected $primaryKey = 'ID_PROMO_CLASS';
    protected $fillable = [
        'ID_PROMO_CLASS',
        'ID_CLASS',
        'AMOUNT_DEPOSIT',
        'BONUS_PACKAGE',
        'DURATION',
    ];

    public function class_detail()
    {
        return $this->belongsTo(class_detail::class, 'ID_CLASS', 'ID_CLASS');
    }

    public function report_deposit_class()
    {
        return $this->hasMany(report_deposit_class::class, 'NO_STRUK_CLASS', 'id');
    }
}
