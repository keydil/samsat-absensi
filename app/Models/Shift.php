<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'shift_name', 'in_time', 'out_time'
    ];

    public function absens()
    {
        return $this->hasMany(Absen::class);
    }

    public function qrCodes()
    {
        return $this->hasMany(QrCode::class);
    }
}
