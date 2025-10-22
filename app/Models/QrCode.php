<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $fillable = [
        'shift_id', 'code_qr', 'present', 'date', 'start_time', 'end_time', 'status'
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function absens()
    {
        return $this->hasMany(Absen::class);
    }

    public function shiftType()
    {
        return $this->shift->shift_name ?? null;
    }
}
