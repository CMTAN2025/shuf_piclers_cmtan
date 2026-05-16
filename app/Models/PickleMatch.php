<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickleMatch extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'mode', 'team_a_p1', 'team_a_p2', 'team_b_p1', 'team_b_p2',
        'score_a', 'score_b', 'winner', 'ratings_applied',
    ];

    public function playerA1() { return $this->belongsTo(Player::class, 'team_a_p1'); }
    public function playerA2() { return $this->belongsTo(Player::class, 'team_a_p2'); }
    public function playerB1() { return $this->belongsTo(Player::class, 'team_b_p1'); }
    public function playerB2() { return $this->belongsTo(Player::class, 'team_b_p2'); }
    public function ratingsHistory() { return $this->hasMany(RatingHistory::class, 'match_id'); }
}
