<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use SoftDeletes;

    public function plays()
    {
        return $this->belongsToMany(Play::class, 'play_card', 'card_id','play_id');
    }

    public function turns()
    {
        return $this->hasMany(Turn::class, 'card_id');
    }

    public function players()
    {
        return $this->belongsToMany(Player::class,'player_card','card_id','player_id');
    }

    public function getTextWhite()
    {
        return implode('', json_decode($this->text, true));
    }

    public function getTextBlack()
    {
        return implode('____', json_decode($this->text, true));
    }

    public function decks()
    {
        return $this->belongsToMany(Deck::class, 'card_deck', 'card_id','deck_id');
    }
}
