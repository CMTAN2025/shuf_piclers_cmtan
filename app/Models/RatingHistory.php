<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingHistory extends Model
{
    protected $table = 'ratings_history';

    protected $fillable = ['player_id', 'match_id', 'rating_before', 'rating_after', 'delta'];

    public function player() { return $this->belongsTo(Player::class); }
    public function match()  { return $this->belongsTo(PickleMatch::class, 'match_id'); }
}
