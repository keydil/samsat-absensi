<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $fillable = [
        'code_qr', 'present', 'date', 'start_time', 'end_time', 'status'
    ];

    public function absens()
    {
        return $this->hasMany(Absen::class);
    }
}
