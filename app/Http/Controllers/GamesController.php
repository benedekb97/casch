<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use Auth;
use Illuminate\Http\Request;

class GamesController extends Controller
{

    public function index()
    {
        $players = Player::onlyTrashed()->where('user_id',Auth::id())->get();

        $games = [];
        foreach($players as $player) {
            $games[] = Game::onlyTrashed()->where('id',$player->game_id)->first();
        }

        return view('games.index', [
            'games' => $games,
            'players' => $players
        ]);
    }

    public function game(Game $game)
    {
        return view('games.game', [
            'game' => $game
        ]);
    }
}
