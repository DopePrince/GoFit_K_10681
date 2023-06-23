<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class class_detail extends Model
{
    use HasFactory;

    protected $table = 'class_details';
    protected $primaryKey = 'ID_CLASS';
    public $incrementing = false;
    protected $keyType = 'integer';
    protected $fillable = [
        'ID_CLASS',
        'CLASS_NAME',
        'PRICE'
    ];

    public function class_on_running()
    {
        return $this->hasMany(class_on_running::class, 'ID_CLASS_ON_RUNNING', 'id');
    }

    public function class_on_running_daily()
    {
        return $this->hasMany(class_on_running_daily::class, 'ID_CLASS_ON_RUNNING_DAILY', 'id');
    }

    public function promo_class()
    {
        return $this->hasMany(promo_class::class, 'ID_PROMO_CLASS', 'id');
    }

    public function report_deposit_class()
    {
        return $this->hasMany(report_deposit_class::class, 'NO_STRUK_CLASS', 'id');
    }

    public function class_deposit()
    {
        return $this->hasMany(class_deposit::class, 'ID_CLASS_DEPOSIT', 'id');
    }
}
