<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    // Lauki, kurus atļauts masveidā aizpildīt
    protected $fillable = [
        'user_id',
        'from_user_id',
        'type',
        'data',
        'is_read'
    ];

    // Automātiski pārveido datu lauku par masīvu
    protected $casts = [
        'data' => 'array',
    ];

    // Lietotājs, kurš izveidoja paziņojumu
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }
}