<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    protected $fillable = [
        'user_id', 'shift_id', 'qr_code_id', 'date', 'time', 'hours',
        'present_desc_system', 'present_user_desc', 'present_user_image',
        'status', 'status_desc', 'status_image',
        'lat_location_present', 'lng_location_present',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke shift
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    // Relasi ke qr_code
    public function qrCode()
    {
        return $this->belongsTo(QrCode::class);
    }

    // Ambil tipe absen dari QR Code
    public function presentType()
    {
        return $this->qrCode->present ?? null; // 'in_present' atau 'out_present'
    }
}
