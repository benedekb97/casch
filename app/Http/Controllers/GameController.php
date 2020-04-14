<?php

namespace App\Http\Controllers;

use App\Events\EditGame;
use App\Events\JoinGame;
use App\Events\LeaveGame;
use App\Events\StartGame;
use App\Models\Card;
use App\Models\Game;
use App\Models\Player;
use App\Models\Round;
use App\Models\Turn;
use Auth;
use Illuminate\Http\Request;
use Session;
use Str;

class GameController extends Controller
{
    public function host()
    {
        if(Auth::user()->hostedGame==null){
            $game = new Game();
            $game->slug = Str::random('16');
            $game->host_user_id = Auth::id();
            $game->save();

            $player = new Player();
            $player->user_id = Auth::id();
            $player->game_id = $game->id;
            $player->save();
        }else{
            $game = Auth::user()->hostedGame;
            $player = Auth::user()->player();
        }

        if($game->started == true) {
            return redirect()->route('game.play', ['slug' => $game->slug]);
        }


        if(Auth::user()->game() != null && (Auth::user()->hostedGame == null || Auth::user()->hostedGame->id != Auth::user()->game()->id)) {
            event(new LeaveGame(['id' => Auth::user()->player()->id, 'name' => Auth::user()->name], Auth::user()->game()->slug));
        }


        return view('game',[
            'game' => $game,
            'player_id' => $player->id
        ]);
    }

    public function join($slug)
    {
        if(!Auth::check()) {
            Session::put(['game_slug' => $slug]);

            return redirect()->route('login');
        }

        if(Session::has('game_slug')) {
            Session::forget('game_slug');
        }

        if(Auth::user()->game()!=null && Auth::user()->game()->slug != $slug) {
            return redirect()->route('leave', ['slug' => $slug]);
        }

        $game = Game::all()->where('slug',$slug)->first();

        if($game->started == true && Auth::user()->game()->id == $game->id) {
            return redirect()->route('game.play', ['slug' => $slug]);
        }

        if(Auth::user()->player() == null) {
            $player = new Player();
            $player->user_id = Auth::id();
            $player->game_id = $game->id;
            $player->save();

            event(new JoinGame(['id' => $player->id, 'name' => Auth::user()->name], $slug));
        }else{
            $player = Auth::user()->player();
        }


        return view('game', [
            'game' => $game,
            'player_id' => $player->id
        ]);
    }

    public function leave($slug = null)
    {
        $game = Auth::user()->game();

        if($game == null) {
            return redirect()->route('index');
        }

        $player_id = Auth::user()->player()->id;

        Auth::user()->player()->delete();

        $next_host = $game->players->first();

        if($game != null && $next_host != null) {
            if($game->host_user_id == Auth::id()){
                event(new LeaveGame(['id' => $player_id, 'name' => Auth::user()->name],$game->slug, $next_host->id));
            }else{
                event(new LeaveGame(['id' => $player_id, 'name' => Auth::user()->name],$game->slug));
            }
        }

        if($next_host == null) {
            $game->delete();
        }else{
            $game->host_user_id = $next_host->user->id;
            $game->save();
        }

        if($slug != null) {
            return redirect()->route('join', ['slug' => $slug]);
        }

        return redirect()->route('index');
    }

    public function change(Game $game, Request $request)
    {
        if(Auth::id() != $game->host->id) {
            return response()->json(['success' => false]);
        }

        if(!is_int((int)$request->input('rounds')) || $request->input('rounds')<1) {
            return response()->json(['success'=>false]);
        }

        $game->number_of_rounds = $request->input('rounds');
        $game->save();

        event(new EditGame(['rounds' => $game->number_of_rounds], $game->slug, $game->host_user_id));

        return response()->json(['success' => true]);
    }

    public function start(Game $game, Request $request)
    {
        if($game->host->id != Auth::id()) {
            return response()->json(['success' => false]);
        }
        if($game->players->count() < 2) {
            return response()->json(['success' => false]);
        }

        $all_players = $game->players;
        $first_host = $all_players->random();

        $cards = Card::all();

        $round = new Round();
        $round->game_id = $game->id;
        $round->number = 1;
        $round->save();

        $turn = new Turn();
        $turn->player_id = $first_host->id;
        $turn->card_id = $cards->where('type','black')->random()->id;
        $turn->round_id = $round->id;
        $turn->save();

        $round->turn_id = $turn->id;
        $round->save();

        foreach($all_players as $player) {
            $player_cards = $cards->where('type','white')->random(10);
            foreach($player_cards as $card) {
                $player->cards()->attach($card);
                $cards = $cards->filter(function($item) use ($card){
                    return $item->id !== $card->id;
                });
            }
        }

        $game->started = 1;
        $game->current_round = $round->id;
        if($game->number_of_rounds === null) {
            $game->number_of_rounds = 3;
            $game->save();
        }

        event(new StartGame('start',$game->slug));

        return response()->json(['success' => true]);
    }

    public function play($slug)
    {
        $game = Game::where('slug',$slug)->first();

        return view('play',[
            'game' => $game
        ]);
    }

    public function data(Game $game)
    {
        $cards = Auth::user()->player()->cards;
        $black_card = $game->round->current_turn->card;
        $host = $game->round->current_turn->host->user->id;
        $players = $game->users;

        $cards_needed = count(json_decode($black_card->text, true)) -1;

        return response()->json([
            'cards' => $cards,
            'black_card' => $black_card,
            'host_user_id' => $host,
            'cards_needed' => $cards_needed,
            'players' => $players
        ]);
    }
}
