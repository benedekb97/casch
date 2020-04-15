<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

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

    public function players_played()
    {
        return $this->belongsToMany(Player::class, 'plays', 'turn_id', 'player_id');
    }

    public function winning_play()
    {
        return $this->belongsTo(Play::class, 'winning_play_id');
    }

    public function everyonePlayed()
    {
        $host_id = $this->host->id;

        $can_play = $this->round->game->players->filter(function($item) use ($host_id){
            return $item->id !== $host_id;
        });

        $everyone_played = true;
        foreach($can_play as $player) {
            if($this->plays->where('player_id', $player->id)->first() === null && $player->id !== Auth::user()->player()->id) {
                $everyone_played = false;
            }
        }

        return $everyone_played;
    }
}
