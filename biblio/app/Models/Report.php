<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    // Lauki, kurus atļauts masveidā aizpildīt
    protected $fillable = [
        'comment_id',
        'reported_by',
        'reason',
        'status',
    ];

    // Komentārs, par kuru iesniegta sūdzība
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    // Lietotājs, kurš iesniedza sūdzību
    public function user()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}