<?php

namespace App\Http\Controllers\Admin;

use App\Events\NewCards;
use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function view($player)
    {
        $player = Player::withTrashed()->where('id',$player)->first();

        if($player === null) {
            abort(404);
        }

        return view('admin.players.view', [
            'player' => $player
        ]);
    }

    public function deal($player)
    {
        $player = Player::withTrashed()->where('id',$player)->first();

        if($player === null) {
            abort(404);
        }

        if($player->cards === null) {
            return redirect()->back();
        }

        $c = $player->game->getAvailableWhiteCards()->count();
        $cards = $player->game->getAvailableWhiteCards()->random(min($c,10));
        $player->cards()->detach();
        foreach($cards as $card) {
            $player->cards()->attach($card);
        }
        $player->save();

        event(new NewCards($player->user->id, $player->game->slug));

        return redirect()->back();
    }
}
