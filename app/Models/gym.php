<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gym extends Model
{
    use HasFactory;

    protected $table = 'gyms';
    protected $primaryKey = 'ID_GYM';
    protected $fillable = [
        'ID_GYM',
        'GYM_CAPACITY',
        'DATE',
        'START_TIME',
        'END_TIME'
    ];

    public function gym_booking()
    {
        return $this->hasMany(gym_booking::class, 'ID_GYM_BOOKING', 'id');
    }
}
