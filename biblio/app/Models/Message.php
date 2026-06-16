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

    // Lauki, kurus atļauts masveidā aizpildīt
    protected $fillable = [
        'conversation_id',
        'user_id',
        'body',
    ];

    /*Relācijas*/

    // Saruna, kurai pieder ziņojums
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    // Lietotājs, kurš nosūtīja ziņojumu
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Komentāri, kas pievienoti kanāla ierakstam
    public function comments()
    {
        return $this->hasMany(MessageComment::class);
    }

    /*Piekļuves pārbaude*/

    // Pārbauda, vai lietotājs drīkst publicēt ziņojumu sarunā
    public static function canSend(User $user, Conversation $conversation): bool
    {
        // Lietotājam jābūt sarunas dalībniekam
        if (!$conversation->isMember($user)) {
            return false;
        }

        // Privātajā sarunā rakstīt drīkst visi dalībnieki
        if ($conversation->isPrivate()) {
            return true;
        }

        // Kanālā publicēt drīkst tikai administrators vai īpašnieks
        return $conversation->isAdmin($user);
    }
}