<?php

namespace App\Http\Controllers\Admin;

use App\Events\FinishedGame;
use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index($page = 1)
    {
        $games = Game::withTrashed()->get()->sortByDesc('created_at')->forPage($page, 12);

        $pages = ceil(Game::withTrashed()->get()->count()/12);

        return view('admin.games.index',[
            'games' => $games,
            'pages' => $pages,
            'page' => $page
        ]);
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

    public function plays($game, $page = 1)
    {
        $game = Game::withTrashed()->where('id',$game)->first();

        if($game == null) {
            abort(404);
        }

        $plays = $game->getPlays()->forPage($page, 12);
        $pages = ceil($game->getPlays()->count()/12);

        return view('admin.games.plays', [
            'game' => $game,
            'plays' => $plays,
            'page' => $page,
            'pages' => $pages
        ]);
    }

    public function endAll()
    {
        $games = Game::all();

        foreach ($games as $game) {
            if($game == null) {
                continue;
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
        }

        return redirect()->back();
    }
}
