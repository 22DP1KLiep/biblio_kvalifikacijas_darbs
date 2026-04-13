<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Message;



class MessageComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'user_id',
        'body',
    ];

    /* --------------------
     | Relationships
     | --------------------
     */

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function canComment(User $user, Message $message): bool
{
    $conversation = $message->conversation;

    if (! $conversation->isChannel()) {
        return false;
    }

    return $conversation->isMember($user);
}


}
