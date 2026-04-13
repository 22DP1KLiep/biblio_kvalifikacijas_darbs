<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    // Aizsargātie lauki, ko drīkst masveidā pievienot
    protected $fillable = ['name'];

    /**
     * Attiecība: viens žanrs var būt vairākām grāmatām
     */
    public function books()
    {
        return $this->belongsToMany(Book::class);
    }
}
