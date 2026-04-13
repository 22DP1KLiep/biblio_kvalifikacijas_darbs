<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MessageComment;
use App\Models\User;
use App\Models\Conversation;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'body',
    ];

    /* --------------------
     | Relationships
     | --------------------
     */

    // which conversation this message belongs to
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    // who wrote the message
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // comments under a channel post
    public function comments()
{
    return $this->hasMany(MessageComment::class);
}

public static function canSend(User $user, Conversation $conversation): bool
{
    if (! $conversation->isMember($user)) {
        return false;
    }

    // private chat → both users can send
    if ($conversation->isPrivate()) {
        return true;
    }

    // channel → only admin or owner can post
    return $conversation->isAdmin($user);
}

}
