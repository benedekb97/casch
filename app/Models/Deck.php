<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deck extends Model
{
    use SoftDeletes;

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }
}
