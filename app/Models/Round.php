<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Round extends Model
{
    use SoftDeletes;

    public function turns()
    {
        return $this->hasMany(Turn::class, 'round_id');
    }

    public function current_turn()
    {
        return $this->belongsTo(Turn::class, 'turn_id');
    }

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public function hosts()
    {
        return $this->belongsToMany(Player::class, Turn::class, 'round_id', 'player_id');
    }

    public function black_cards()
    {
        return $this->belongsToMany(Card::class, Turn::class, 'round_id', 'card_id');
    }

    public function getNextHost()
    {
        $players = $this->game->players;
        $hosted = $this->hosts;

        $available = $players->diff($hosted);

        return $available->random();
    }
}
