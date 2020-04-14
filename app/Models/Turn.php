<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Turn extends Model
{
    use SoftDeletes;

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }

    public function plays()
    {
        return $this->hasMany(Play::class, 'turn_id');
    }

    public function host()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function round()
    {
        return $this->belongsTo(Round::class, 'round_id');
    }
}
