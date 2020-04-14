<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use SoftDeletes;

    public function players()
    {
        return $this->hasMany(Player::class, 'game_id');
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'players', 'game_id', 'user_id');
    }

    public function round()
    {
        return $this->belongsTo(Round::class, 'current_round');
    }

    public function rounds()
    {
        return $this->hasMany(Round::class, 'game_id');
    }
}
