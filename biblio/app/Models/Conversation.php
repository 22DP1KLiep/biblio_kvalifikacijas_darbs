<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'owner_id',
        'join_type',
    ];

    // Sarunas dalībnieki
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role', 'last_read_at'])
            ->withTimestamps();
    }

    // Sarunas īpašnieks
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Sarunas ziņojumi
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Vai privāta saruna
    public function isPrivate()
    {
        return $this->type === 'private';
    }

    // Vai kanāls
    public function isChannel()
    {
        return $this->type === 'channel';
    }

    // Vai lietotājs ir dalībnieks
    public function isMember(User $user): bool
    {
        return $this->users()
            ->where('user_id', $user->id)
            ->exists();
    }

    // Lietotāja loma sarunā
    public function roleOf(User $user): ?string
    {
        $member = $this->users()
            ->where('user_id', $user->id)
            ->first();

        return $member?->pivot?->role;
    }

    // Vai īpašnieks
    public function isOwner(User $user): bool
    {
        return $this->roleOf($user) === 'owner';
    }

    // Vai administrators
    public function isAdmin(User $user): bool
    {
        return in_array(
            $this->roleOf($user),
            ['owner', 'admin']
        );
    }
}