<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'description',
        'image',
        'published_year',
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function folders()
    {
        return $this->belongsToMany(Folder::class);
    }

    public function comments()
    {
    return $this->hasMany(Comment::class);
    }


}

