<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class class_deposit extends Model
{
    use HasFactory;

    protected $table = 'class_deposits';
    protected $primaryKey = 'ID_CLASS_DEPOSIT';
    protected $fillable = [
        'ID_CLASS_DEPOSIT',
        'ID_MEMBER',
        'ID_CLASS',
        'CLASS_AMOUNT',
        'EXPIRE_DATE'
    ];

    public function member()
    {
        return $this->belongsTo(member::class, 'ID_MEMBER', 'ID_MEMBER');
    }

    public function class_detail()
    {
        return $this->belongsTo(class_detail::class, 'ID_CLASS', 'ID_CLASS');
    }

    public function report_deposit_class()
    {
        return $this->hasMany(report_deposit_class::class, 'NO_STRUK_CLASS', 'id');
    }

}
