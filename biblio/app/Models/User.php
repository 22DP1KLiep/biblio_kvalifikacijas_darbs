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

    /*Masveidā aizpildāmie lauki*/

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'status',
        'restricted_until',
        'restriction_reason',
    ];

    /*Slēptie lauki*/

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*Automātiskā datu tipu pārveidošana*/

    protected $casts = [
        'email_verified_at' => 'datetime',
        'restricted_until' => 'datetime',
    ];

    /*Relācijas*/

    // Lietotāja atsauksmes
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    // Lietotāja izveidotās mapes
    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    // Sarunas, kurās lietotājs piedalās
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    // Lietotāji, kuriem  sekoju
    public function following()
    {
        return $this->belongsToMany(
            User::class,
            'followers',
            'follower_id',
            'following_id'
        )->withTimestamps();
    }

    // Lietotāji, kuri seko 
    public function followers()
    {
        return $this->belongsToMany(
            User::class,
            'followers',
            'following_id',
            'follower_id'
        )->withTimestamps();
    }

    /*Ierobežojumu pārvaldība*/

    // Pārbauda, vai lietotājam ir aktīvs ierobežojums
    public function isRestricted(): bool
    {
        return $this->restricted_until
            && $this->restricted_until->isFuture();
    }

    // Pārbauda, vai lietotājs ir bloķēts
    public function isBanned(): bool
    {
        return $this->status === 'banned';
    }

    // Atgriež ierobežojuma beigu datumu lietotāja saskarnei
    public function restrictionEndsAt(): ?string
    {
        return $this->restricted_until
            ? $this->restricted_until->format('d.m.Y H:i')
            : null;
    }

    // Automātiski noņem ierobežojumu, ja tā termiņš ir beidzies
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

}