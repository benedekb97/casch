<?php

namespace App\Http\Controllers\Admin;

use App\Events\FinishedGame;
use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index()
    {
        return view('admin.games.index');
    }

    public function view($game)
    {
        $game = Game::withTrashed()->where('id',$game)->first();

        if($game === null) {
            abort(404);
        }

        return view('admin.games.view',[
            'game' => $game
        ]);
    }

    public function delete($game)
    {
        $game = Game::withTrashed()->where('id',$game)->first();

        if($game == null) {
            abort(404);
        }

        $players = $game->getPlayers();
        foreach($players as $player){
            $player->cards()->detach();
            $player->plays()->delete();

            $player->delete();
        }

        $rounds = $game->rounds;
        foreach($rounds as $round){
            $round->turns()->delete();
            $round->delete();
        }

        $game->finished = 1;
        $game->save();
        $game->delete();

        event(new FinishedGame(route('game.finished', ['slug' => $game->slug]), $game->slug));

        return redirect()->back();
    }

    public function players($game)
    {
        $game = Game::withTrashed()->where('id',$game)->first();

        if($game === null) {
            abort(404);
        }

        return view('admin.games.players', [
            'game' => $game
        ]);
    }
}
