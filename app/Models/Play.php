<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Play extends Model
{
    use SoftDeletes;

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function getPlayer()
    {
        $player = Player::withTrashed()->where('id',$this->player_id)->first();

        return $player;
    }

    public function getUsers()
    {
        $player = Player::withTrashed()->where('id',$this->player_id)->first();
        $players = Player::withTrashed()->where('game_id',$player->game_id)->get();

        $users = [];
        foreach($players as $player){
            $users[] = $player->user_id;
        }

        return $users;
    }

    public function getGame()
    {
        $player = Player::withTrashed()->where('id',$this->player_id)->first();
        $game = Game::withTrashed()->where('id',$player->game_id)->first();

        return $game;
    }

    public function cards()
    {
        return $this->belongsToMany(Card::class,'play_card', 'play_id','card_id');
    }

    public function turn()
    {
        return $this->belongsTo(Turn::class, 'turn_id');
    }

    public function getTextHTML()
    {
        $turn = Turn::withTrashed()->where('id',$this->turn_id)->first();

        $black_card = Card::withTrashed()->where('id',$turn->card_id)->first();



        $text_html = '';

        foreach(json_decode($black_card->text) as $key => $text_piece) {
            if(isset($this->cards()->get()->toArray()[$key])){
                $text_html .= $text_piece . '<span class="white-text">' . implode('', json_decode($this->cards()->get()->toArray()[$key]['text'], true)) . '</span>';
            }else{
                $text_html .= $text_piece;
            }
        }

        return $text_html;
    }
}
