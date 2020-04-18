<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use Auth;
use Illuminate\Http\Request;

class GamesController extends Controller
{

    public function index($page = 1)
    {
        $players = Player::onlyTrashed()->where('user_id',Auth::id())->get();

        $games = [];
        foreach($players as $player) {
            $games[] = Game::onlyTrashed()->where('id',$player->game_id)->first();
        }

        $games = collect($games);
        $games = $games->sort(function($a, $b) {
            if($a->created_at == $b->created_at) {
                return 0;
            }

            return ($a->created_at > $b->created_at) ? -1 : 1;
        });

        $pages = ceil($games->count()/5);

        $games = $games->forPage($page, 5);


        return view('games.index', [
            'games' => $games,
            'players' => $players,
            'page' => $page,
            'pages' => $pages
        ]);
    }

    public function game(Game $game)
    {
        return view('games.game', [
            'game' => $game
        ]);
    }
}
