<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Message;


class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'owner_id',
        'join_type',
    ];

    /* --------------------
     | Relationships
     | --------------------
     */

    // members of the conversation
   public function users()
{
    return $this->belongsToMany(User::class)
        ->withPivot(['role', 'last_read_at'])
        ->withTimestamps();
}


    // owner of the channel
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // messages (chat or channel posts)
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /* --------------------
     | Helpers
     | --------------------
     */

    public function isPrivate()
    {
        return $this->type === 'private';
    }

    public function isChannel()
    {
        return $this->type === 'channel';
    }

    /* --------------------
 | Permission helpers
 | --------------------
 */

public function isMember(User $user): bool
{
    return $this->users()
        ->where('user_id', $user->id)
        ->exists();
}

public function roleOf(User $user): ?string
{
    $member = $this->users()
        ->where('user_id', $user->id)
        ->first();

    return $member?->pivot?->role;
}

public function isOwner(User $user): bool
{
    return $this->roleOf($user) === 'owner';
}

public function isAdmin(User $user): bool
{
    return in_array($this->roleOf($user), ['owner', 'admin']);
}
}
