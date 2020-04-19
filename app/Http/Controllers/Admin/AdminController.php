<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Game;
use Auth;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $games = Game::all();

        $games_in_lobby = $games->where('started',0)->all();

        return view('admin.index', [
            'active_games' => $games,
            'games_in_lobby' => $games_in_lobby
        ]);
    }

    public function createBaseDeck()
    {
        $cards = Card::all();
        $deck = new Deck();
        $deck->user_id = Auth::id();
        $deck->name = "Alap pakli";
        $deck->public = true;
        $deck->save();

        foreach($cards as $card){
            $card->user_id = Auth::id();
            $card->save();
            $deck->cards()->attach($card);
        }
    }
}
