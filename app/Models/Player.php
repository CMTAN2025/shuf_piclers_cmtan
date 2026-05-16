<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = ['name', 'dupr', 'rating', 'wins', 'losses', 'matches_played'];

    public function ratingHistory()
    {
        return $this->hasMany(RatingHistory::class)->orderByDesc('created_at');
    }
}
