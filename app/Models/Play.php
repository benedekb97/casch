<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Play extends Model
{
    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function cards()
    {
        return $this->belongsToMany(Card::class,'play_card', 'play_id','card_id');
    }

    public function turn()
    {
        return $this->belongsTo(Turn::class, 'turn_id');
    }
}
