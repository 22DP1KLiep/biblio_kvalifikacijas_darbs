<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rating extends Model
{
    use HasFactory;

    // Datubāzes tabulas nosaukums
    protected $table = 'book_ratings';

    // Lauki, kurus atļauts masveidā aizpildīt
    protected $fillable = [
        'book_id',
        'user_id',
        'rating'
    ];

    // Lietotājs, kurš piešķīris vērtējumu
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Grāmata, kurai piešķirts vērtējums
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}