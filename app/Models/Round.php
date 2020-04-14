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
}
