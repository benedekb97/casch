<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public function plays()
    {
        return $this->hasMany(Play::class, 'player_id');
    }

    public function turns()
    {
        return $this->hasMany(Turn::class, 'player_id');
    }

    public function cards()
    {
        return $this->belongsToMany(Card::class, 'player_card','player_id','card_id');
    }

    public function score()
    {
        $plays = Play::withTrashed()->where('player_id',$this->id)->get();
        $score = 0;
        foreach($plays as $play) {
            $score += $play->points;
            if($play->points == 0) {
                $score += $play->likes;
            }
        }

        return $score;
    }
}
