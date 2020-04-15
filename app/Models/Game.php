<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;

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

    public function everyPlayerReady()
    {
        foreach($this->players as $player) {
            if(!$player->ready) {
                return false;
            }
        }

        return true;
    }

    public function getUsedBlackCards()
    {
        $cards = [];

        foreach($this->rounds as $round) {
            foreach($round->black_cards as $card) {
                $cards[$card->id] = $card;
            }
        }

        return $cards;
    }

    public function getAvailableBlackCards()
    {
        $used_black_cards = $this->getUsedBlackCards();

        $cards = Card::where('type','black')->get();
        foreach($used_black_cards as $black_card) {
            $cards = $cards->where('id','!=',$black_card->id);
        }

        $cards = $cards->all();

        return $cards;
    }

    public function getUsedWhiteCards()
    {
        $cards = [];
        foreach($this->players as $player){
            foreach($player->cards as $card){
                $cards[] = $card;
            }
        }

        return $cards;
    }

    public function getAvailableWhiteCards()
    {
        $previous_cards = $this->getUsedWhiteCards();
        $white_cards = Card::all()->where('type','white');
        foreach($previous_cards as $card) {
            $white_cards = $white_cards->where('id','!=',$card->id);
        }

        return $white_cards;
    }

    public function setupNewTurn()
    {
        $players = $this->players;
        foreach($players as $player) {
            $player->ready = false;
            $player->save();
        }

        $available_cards = $this->getAvailableBlackCards();

        $hosts_so_far = $this->round->hosts;

        $new_round = true;
        foreach($players as $player){
            if(!$hosts_so_far->find($player->id)) {
                $new_round = false;
            }

            $new_cards_count = 10-$player->cards()->count();
            $new_cards = $this->getAvailableWhiteCards()->random($new_cards_count);
            foreach($new_cards as $card){
                $player->cards()->attach($card);
            }
            $player->save();
        }

        if($new_round && $this->number_of_rounds==$this->round->number) {
            $new_round = false;
            $game_finished = true;
        }else{
            $game_finished = false;
        }

        if($new_round && !$game_finished) {
            $round = new Round();
            $round->game_id = $this->id;
            $round->number = $this->round->number + 1;
            $round->save();

            $turn = new Turn();
            $turn->round_id = $round->id;
            $turn->card_id = collect($available_cards)->random()->id;
            $turn->player_id = $round->getNextHost()->id;
            $turn->save();

            $round->turn_id = $turn->id;
            $round->save();

            $this->current_round = $round->id;
            $this->save();
        }elseif(!$new_round && !$game_finished) {
            $turn = new Turn();
            $turn->round_id = $this->round->id;
            $turn->card_id = collect($available_cards)->random()->id;
            $turn->player_id = $this->round->getNextHost()->id;
            $turn->save();

            $this->round->turn_id = $turn->id;
            $this->round->save();
        }else{
            $this->finished = true;
            $this->save();
        }

    }
}
