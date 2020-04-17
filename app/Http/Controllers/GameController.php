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
use App\Events\StartLoad;
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
use File;
use Illuminate\Http\Request;
use Session;
use Str;

class GameController extends Controller
{
    private $messages = [
        'Kártyák kiosztása',
        'Kiscicák simogatása',
        'Karantén megszegése',
        'Korona továbbítása',
        'WC-papír ellopása',
        '1020 ajtó felgyújtása',
        'Kerámia megtömése',
        'WC felrobbantása',
        'Petárda begyújtása',
        'Hátrányos helyzetűek kinevetése',
        'Macskaalom nyakbaöntése',
        '1020 ágyú újratöltése',
        'WC-papír osztás elfelejtése',
        'Sör szisszentése',
        'S.I.R. chat lenémítása',
        'Hímzőgép karbantartása',
        'Nem létező akvárium kitakarítása',
        'Lábszag szippantása',
        'Szenek felrakása',
        'Alufólia lyukasztása',
        'Hitler újraélesztése',
        'FFSZ logó felfestése',
        'QPA megrendezése (szarul)',
        'Májkrém kidobása a 10-ről',
        'MOL székház becélzása',
        'Portások felbaszása',
        'Jakab Zoltán kirugása',
        'Zöld István elszívása',
        'FNT körbehányása',
        'Antennák kikerülése',
        'Pipa beégetése',
        'Pipa elszívása',
        'Liftközbuli szétrobbantása',
        'Mentor hívása',
        'S.I.R. tábor megrendezése',
        'Rádió lemerítése',
        'Harci kutyák dolgoztatása',
        'Korsó elejtése',
        'Korsó összeragasztása',
        'Nagymester leitatása',
        'Juniorok csicskáztatása',
        'Gólyák csicskáztatása',
        'Seniorok csicskáztatása',
        'Apródok csicskáztatása',
        'Lovagok csicskáztatása',
        'Nagymester csicskáztatása',
        'Várurak csicskáztatása',
        'Hetek csicskáztatása',
        'KB feloszlatása',
        'OBI feloszlatása',
        'HK feloszlatása'
    ];

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
        $everyone_ready = true;
        foreach($game->players as $player){
            if(!$player->ready && $player->user->id != $game->host->id){
                $everyone_ready = false;
            }
        }

        return view('game',[
            'game' => $game,
            'player_id' => $player->id,
            'messages' => $this->messages,
            'everyone_ready' => $everyone_ready
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
            // if the game started and you are not in the game
//            if(Auth::user()->game() != null){
                return redirect()->route('game.play', ['slug' => Auth::user()->game()->slug]);
//            }
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
//        if($game->started && ($game->round->current_turn->winner_id != null)) {
//            return redirect()->route('game.turn.recap', ['game' => $game]);
//        }

        $everyone_ready = true;
        foreach($game->players as $player){
            if(!$player->ready && $player->user->id != $game->host->id){
                $everyone_ready = false;
            }
        }

        return view('game', [
            'game' => $game,
            'player_id' => $player->id,
            'messages' => $this->messages,
            'everyone_ready' => $everyone_ready
        ]);
    }

    public function leave($slug = null)
    {
        $game = Auth::user()->game();

        if($game == null) {
            return redirect()->route('index');
        }

        $player_id = Auth::user()->player()->id;

        Auth::user()->player()->forceDelete();

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

    public function change($slug, Request $request)
    {
        $game = Game::where('slug',$slug)->first();
        if($game == null) {
            abort(404);
        }

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

        event(new StartLoad(null, $game->slug));

        foreach($game->players as $player)
        {
            $player->ready = 0;
            $player->save();
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

//        if($game->round->current_turn->everyonePlayed()) {
//            return redirect()->route('game.turn.recap', ['game' => $game]);
//        }

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
        $players = $game->users->where('deleted_at',null)->all();
        $played = $game->round->current_turn->players_played;
        $play = Auth::user()->player()->plays->where('turn_id', $game->round->current_turn->id)->first();
        $scores = [];

        foreach($players as $player) {
            $scores[$player->id] = $player->player()->score();
        }

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
            'cards_played' => $cards_played,
            'scores' => $scores
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
            event(new TurnPlaysFinished([
                'host_id' => $game->round->current_turn->host->user->id,
                'recap_url' => route('game.turn.recap', ['game' => $game])
            ], $game->slug));
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

        if(Auth::user()->game()== null || Auth::user()->game()->id != $game->id) {
            abort(404);
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

        if(Auth::user()->game()== null || Auth::user()->game()->id != $game->id) {
            abort(404);
        }

        $play->points = $game->players()->count();
        $play->save();

        $play_text = "";


        $black_card = $game->round->current_turn->card;
        foreach(json_decode($black_card->text) as $key => $text) {
            $play_text .= $text;
            if(isset($play->cards()->get()->toArray()[$key])) {
                $play_text .= "<span class='white-text'>".implode('', json_decode($play->cards()->get()->toArray()[$key]['text'], true))."</span>";
            }
        }


        $game->round->current_turn->winning_play_id = $play->id;
        $game->round->current_turn->save();

        $time_left = strtotime($game->round->current_turn->updated_at)+max($game->players()->count()*3,10)-time();

        event(new TurnFinished([
            'id' => $play->id,
            'text' => $play_text,
            'player_id' => $play->player->user->id,
            'name' => $play->player->user->name,
            'winner_points' => $play->player->score(),
            'time_left' => $time_left,
            'recap_url' => route('game.turn.recap', ['game' => $game])
        ], $game->slug));

        return redirect()->route('game.turn.recap', ['game' => $game]);
    }

    public function turnRecap(Game $game)
    {
        if(Auth::user()->game()== null || Auth::user()->game()->id != $game->id) {
            abort(404);
        }

        if($game->round->current_turn->winning_play_id===null || !$game->round->current_turn->everyonePlayed()) {
            if($game->round->current_turn->host->user->id === Auth::id() && $game->round->current_turn->everyonePlayed()) {
                return redirect()->route('game.choose_turn_winner', ['game' => $game]);
            }
        }

        if(!$game->round->current_turn->allPlayed()) {
            return redirect()->route('game.play', ['slug' => $game->slug]);
        }

        if($game->round->current_turn->winning_play!=null) {
            $time_left = strtotime($game->round->current_turn->updated_at)+max($game->players()->count()*3,10)-time();
        }else{
            $time_left = time();
        }

        return view('recap', [
            'game' => $game,
            'turn' => $game->round->current_turn,
            'black_card' => $game->round->current_turn->card,
            'time_left' => $time_left,
            'messages' => $this->messages
        ]);
    }

    public function ready(Game $game)
    {
        if(Auth::user()->game() === null || Auth::user()->game()->id !== $game->id) {
            return response()->json(['success' => false]);
        }
        if(Auth::user()->player()->ready) {
            return response()->json(['success' => false]);
        }

        if($game->round->current_turn->winning_play_id == null) {
            return response()->json(['success' => false]);
        }

        Auth::user()->player()->ready = true;
        Auth::user()->player()->save();


        event(new PlayerReady(Auth::id(), $game->slug));

        if($game->everyPlayerReady()) {
            event(new StartLoad(true, $game->slug));
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

    public function finished($slug, $page = null)
    {
        $game = Game::withTrashed()->where('slug',$slug)->first();

        if($game == null) {
            abort(404);
        }

        $rounds = Round::withTrashed()->where('game_id',$game->id)->get();

        $all_plays = [];

        $points = [];

        $in_game = false;

        $players = Player::withTrashed()->where('game_id',$game->id)->get();
        foreach($players as $player) {
            $plays = Play::withTrashed()->where('player_id', $player->id)->get();
            $player_points = 0;
            foreach($plays as $play) {
                $player_points += $play->points + $play->likes;
                $all_plays[] = $play;
            }
            $points[] = [
                'name' => $player->user->name,
                'points' => $player_points
            ];

            if($player->user_id == Auth::id()) {
                $in_game = true;
            }
        }
        if(!$in_game) {
            abort(404);
        }

        $points = collect($points);

        $points = $points->sort(function($a, $b){
            if($a['points'] == $b['points']) {
                return 0;
            }

            return ($a['points'] > $b['points']) ? -1 : 1;
        });

        $points = array_values($points->toArray());

        $all_plays = collect($all_plays);
        $pages = ceil($all_plays->count()/12);

        $all_plays = $all_plays->sort(function($a, $b){
            if($a->likes+$a->points == $b->likes+$b->points) {
                return 0;
            }

            return ($a->likes+$a->points > $b->likes+$b->points) ? -1 : 1;
        });

        if($page === null) {
            $best_plays = $all_plays->slice(0, 12);
        }else{
            $best_plays = $all_plays->forpage($page, 12);
        }


        return view('finished',[
            'game' => $game,
            'points' => $points,
            'plays' => $best_plays,
            'page' => $page,
            'pages' => $pages
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

//    public function upload(Request $request)
//    {
//        $file = $request->file('file');
//
//        $contents = File::get($file);
//        $cards = explode(';', $contents);
//
//        $count = 0;
//        foreach($cards as $text) {
//            $card = new Card();
//            $card->type='black';
//            $card->text = json_encode(explode('<blank>',$text));
//            $card->save();
//            $count++;
//        }
//
//        return dd($count . " cards uploaded");
//    }

    public function readyLobbyToggle($slug)
    {
        $game = Game::where('slug', $slug)->first();

        if($game == null || Auth::user()->game() == null || Auth::user()->game()->id != $game->id) {
            return response()->json(['success' => false]);
        }

        Auth::user()->player()->ready = !Auth::user()->player()->ready;
        Auth::user()->player()->save();

        $everyone_ready = true;
        foreach($game->players as $player){
            if(!$player->ready && $player->user->id != $game->host->id){
                $everyone_ready = false;
            }
        }

        event(new PlayerReady([
            'id' => Auth::user()->player()->id,
            'everyone_ready' => $everyone_ready
        ], $slug));

        return response()->json(['success' => true]);
    }
}
