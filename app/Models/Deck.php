<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deck extends Model
{
    use SoftDeletes;

    public function games()
    {
        return $this->hasMany(Game::class, 'deck_id');
    }

    public function cards()
    {
        return $this->belongsToMany(Card::class, 'card_deck', 'deck_id','card_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function whiteCards()
    {
        return $this->cards->where('type','white');
    }

    public function blackCards()
    {
        return $this->cards->where('type','black');
    }
}
