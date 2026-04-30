<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
use App\Models\Conversation;



class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'status',
        'restricted_until',
        'restriction_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    'email_verified_at' => 'datetime',
    'restricted_until' => 'datetime',
    ];


    /**
     * Attiecība ar feedback (ja nepieciešams)
     */
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

        /**
     * Pārbauda, vai lietotājs ir ierobežots (read-only)
     */
    public function isRestricted(): bool
{
    return $this->restricted_until
        && $this->restricted_until->isFuture();
}

    public function isBanned(): bool
    {
        return $this->status === 'banned';
    }

    /**
     * Atgriež datumu, līdz kuram lietotājs ir ierobežots (UI vajadzībām)
     */
    public function restrictionEndsAt(): ?string
    {
        return $this->restricted_until
            ? $this->restricted_until->format('d.m.Y H:i')
            : null;
    }


    public function liftRestrictionIfExpired(): void
    {
        if (
            $this->status === 'restricted'
            && $this->restricted_until
            && $this->restricted_until->isPast()
        ) {
            $this->update([
                'status' => 'active',
                'restricted_until' => null,
                'restriction_reason' => null,
            ]);
        }
    }

public function conversations()
{
    return $this->belongsToMany(Conversation::class)
        ->withPivot('role')
        ->withTimestamps();
}

protected static function booted()
{
    static::created(function ($user) {

        // -------------------------
        // 1️⃣ Pievieno Community kanālam
        // -------------------------
        $community = \App\Models\Conversation::where('title', 'Community')
            ->where('type', 'channel')
            ->first();

        if ($community) {
            $community->users()->attach($user->id, ['role' => 'member']);
        }

        // -------------------------
        // 2️⃣ Izveido Welcome Bot privāto čatu
        // -------------------------
        $bot = \App\Models\User::where('username', 'welcomebot')->first();

        if (! $bot) {
            return;
        }

        // 🔎 Pārbauda vai jau eksistē private čats starp bot un user
        $existingConversation = \App\Models\Conversation::where('type', 'private')
            ->whereHas('users', function ($q) use ($bot) {
                $q->where('user_id', $bot->id);
            })
            ->whereHas('users', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->first();

        if ($existingConversation) {
            return;
        }

        // 🆕 Izveido jaunu private conversation
        $conversation = \App\Models\Conversation::create([
            'type' => 'private',
            'title' => null,
            'owner_id' => $bot->id,
            'join_type' => 'open', // ⚠️ svarīgi
        ]);

        // 👥 Piesaista abus lietotājus
        $conversation->users()->attach($bot->id, ['role' => 'member']);
        $conversation->users()->attach($user->id, ['role' => 'member']);

        // 💬 Pirmā ziņa no bota
        \App\Models\Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $bot->id,
            'body' => 'Sveiks! 👋 Prieks tevi redzēt Biblio čatā!',
        ]);
    });
}
// Users I follow
public function following()
{
    return $this->belongsToMany(
        User::class,
        'followers',
        'follower_id',
        'following_id'
    )->withTimestamps();
}

// Users following me
public function followers()
{
    return $this->belongsToMany(
        User::class,
        'followers',
        'following_id',
        'follower_id'
    )->withTimestamps();
}





}
