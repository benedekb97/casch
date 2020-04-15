<?php

namespace App\Http\Controllers;

use App\Events\EditGame;
use App\Events\JoinGame;
use App\Events\LeaveGame;
use App\Events\LikePlay;
use App\Events\NewTurn;
use App\Events\PlayerReady;
use App\Events\StartGame;
use App\Events\PlayCards;
use App\Events\TurnPlaysFinished;
use App\Events\TurnFinished;
use App\Events\FinishedGame;
use App\Models\Card;
use App\Models\Game;
use App\Models\Player;
use App\Models\Play;
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

        if($game == null) {
            abort(404);
        }

        if($game->started == true && Auth::user()->game()->id == $game->id) {
            return redirect()->route('game.play', ['slug' => $slug]);
        }

        if($game->started == true && (Auth::user()->game() == null || Auth::user()->game()->id != $game->id)) {
            abort(404);
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
        }
        $game->save();

        event(new StartGame('start',$game->slug));

        return response()->json(['success' => true]);
    }

    public function play($slug)
    {
        $game = Game::where('slug',$slug)->first();

        if($game == null){
            abort(404);
        }
        if($game->round->current_turn->winning_play_id != null){
            return redirect()->route('game.turn.recap', ['game' => $game]);
        }

        if($game->round->current_turn->everyonePlayed() && $game->round->current_turn->host->user->id == Auth::id() && $game->round->current_turn->winning_play_id == null) {
            return redirect()->route('game.choose_turn_winner', ['game' => $game]);
        }

        if($game->finished) {
            return redirect()->route('game.finished', ['slug' => $game->slug]);
        }

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
        $played = $game->round->current_turn->players_played;
        $play = Auth::user()->player()->plays->where('turn_id', $game->round->current_turn->id)->first();

        if($play != null) {
            $cards_played = [];
            foreach($play->cards as $card) {
                $cards_played[] = implode('', json_decode($card->text, true));
            }
        }else{
            $cards_played = null;
        }

        $players_played = [];
        foreach($played as $player) {
            $players_played[] = $player->user->id;
        }

        $cards_needed = count(json_decode($black_card->text, true)) -1;

        return response()->json([
            'cards' => $cards,
            'black_card' => $black_card,
            'host_user_id' => $host,
            'cards_needed' => $cards_needed,
            'players' => $players,
            'players_played' => $players_played,
            'cards_played' => $cards_played
        ]);
    }

    public function submit(Game $game, Request $request)
    {
        if(Auth::id() === $game->round->current_turn->host->user->id) {
            return response(['success' => false]);
        }

        if(Auth::user()->game()===null || Auth::user()->game()->id !== $game->id){
            return response(['success' => false]);
        }

        $play = $game->round->current_turn->plays->where('player_id',Auth::user()->player()->id)->first();

        if($play !== null) {
            return response(['success' => false]);
        }
        Session::forget('liked');

        $cards_needed = count(json_decode($game->round->current_turn->card->text, true)) -1;
        $answers = [];

        if($cards_needed === 1) {
            if($request->input('answer1') === null) {
                return response()->json(['success' => false]);
            }
            $card = Auth::user()->player()->cards->where('id',$request->input('answer1'))->first();
            if($card == null) {
                return response(['success' => false]);
            }
            $answers = [
                $card
            ];
        }else{
            for($i = 0; $i<$cards_needed; $i++) {
                if($request->input('answer' . ($i+1)) === null) {
                    return response()->json(['success' => false]);
                }
                if(Auth::user()->player()->cards->where('id', $request->input('answer' . ($i+1)))->first() == null) {
                    return response(['success' => false]);
                }
                $answers[] = Auth::user()->player()->cards->where('id', $request->input('answer' . ($i+1)))->first();
            }
        }

        $play = new Play();
        $play->player_id = Auth::user()->player()->id;
        $play->turn_id = $game->round->current_turn->id;
        $play->save();

        foreach($answers as $answer) {
            Auth::user()->player()->cards()->detach($answer->id);
            $play->cards()->attach($answer);
            $play->save();
        }

        if($game->round->current_turn->everyonePlayed()) {
            event(new TurnPlaysFinished($game->round->current_turn->host->user->id, $game->slug));
        }else{
            event(new PlayCards(Auth::id(), $game->slug));
        }

        return response()->json(['success' => true]);
    }

    public function choose(Game $game)
    {
        if($game->round->current_turn->host->user->id !== Auth::id()) {
            if(Auth::user()->game()->id === $game->id) {
                return redirect()->route('game.play', ['slug' => $game->slug]);
            }
            abort(403);
        }

        return view('choose-turn-winner',[
            'plays' => $game->round->current_turn->plays,
            'black_card' => $game->round->current_turn->card,
            'game' => $game
        ]);
    }

    public function chooseSubmit(Game $game, Play $play)
    {
        if($game->round->current_turn->host->user->id !== Auth::id()) {
            if(Auth::user()->game()->id === $game->id) {
                return redirect()->route('game.play', ['slug' => $game->slug]);
            }
            abort(403);
        }

        $play->points = 10;
        $play->save();

        $game->round->current_turn->winning_play_id = $play->id;
        $game->round->current_turn->save();

        event(new TurnFinished(route('game.turn.recap', ['game' => $game]), $game->slug));

        return redirect()->route('game.turn.recap', ['game' => $game]);
    }

    public function turnRecap(Game $game)
    {
        if($game->round->current_turn->winning_play_id===null || !$game->round->current_turn->everyonePlayed()) {
            if($game->round->current_turn->host->user->id === Auth::id() && $game->round->current_turn->everyonePlayed()) {
                return redirect()->route('game.choose_turn_winner', ['game' => $game]);
            }
            return redirect()->route('game.play', ['slug' => $game->slug]);
        }

        $time_left = (strtotime($game->round->current_turn->updated_at)+10-time())*10000;

        return view('recap', [
            'game' => $game,
            'turn' => $game->round->current_turn,
            'black_card' => $game->round->current_turn->card,
            'time_left' => $time_left
        ]);
    }

    public function ready(Game $game)
    {
        if(Auth::user()->game() === null || Auth::user()->game()->id !== $game->id) {
            return response()->json(['success' => false]);
        }

        Auth::user()->player()->ready = true;
        Auth::user()->player()->save();

        event(new PlayerReady(Auth::id(), $game->slug));

        if($game->everyPlayerReady()) {

            $game->setupNewTurn();

            if($game->finished == true) {
                event(new FinishedGame(route('game.finished', ['slug' => $game->slug]), $game->slug));
                foreach($game->players as $player) {
                    $player->cards()->detach();
                    $player->plays()->delete();
                    $player->delete();
                }

                foreach($game->rounds as $round) {
                    $round->turns()->delete();
                    $round->delete();
                }
                $game->delete();
            }else{
                event(new NewTurn(true, $game->slug));
            }
            Session::forget('liked');

        }

        return response()->json(['success' => true]);
    }

    public function finished($slug)
    {
        $game = Game::withTrashed()->where('slug',$slug)->first();

        if($game == null) {
            abort(404);
        }

        $rounds = Round::withTrashed()->where('game_id',$game->id)->get();

        $points = [];
        $players = Player::withTrashed()->where('game_id',$game->id)->get();
        foreach($players as $player) {
            $plays = Play::withTrashed()->where('player_id', $player->id)->get();
            $player_points = 0;
            foreach($plays as $play) {
                $player_points += $play->points;
            }
            $points[$player->id] = [
                'name' => $player->user->name,
                'points' => $player_points
            ];
        }

        return view('finished',[
            'game' => $game,
            'points' => $points
        ]);
    }

    public function like(Game $game, Request $request)
    {
        if(Auth::user()->game() === null || Auth::user()->game()->id !== $game->id) {
            abort(403);
        }

        if(Session::has('liked')) {
            return response(['success' => false]);
        }

        $play_id = $request->input('play_id');
        $play = Play::find($play_id);

        if($play === null || $play->turn_id !== $game->round->current_turn->id) {
            return response(['success' => false]);
        }

        ++$play->likes;
        $play->save();
        Session::put('liked', true);

        event(new LikePlay([
            'id' => $play->id,
            'likes' => $play->likes
        ], $game->slug));

        return response()->json(['success' => true]);
    }
}
