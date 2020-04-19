<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Deck;
use Auth;
use Illuminate\Http\Request;

class DeckController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        return view('user.decks.create',[
            'user' => $user
        ]);
    }

    public function save(Request $request, Deck $deck = null)
    {
        if($deck === null) {
            $deck = new Deck();
        }elseif(Auth::id() !== $deck->user_id){
            abort(403);
        }

        $deck->name = $request->input('name');
        $deck->public = $request->input('public') ? 1 : 0;
        $deck->user_id = Auth::id();
        $deck->save();

        return redirect()->route('user.decks.view', ['deck' => $deck]);
    }

    public function view(Deck $deck)
    {
        $decks = Deck::where('user_id', Auth::id())
            ->where('id','!=',$deck->id)
            ->orWhere('public',true)
            ->where('id','!=',$deck->id)
            ->get();

        return view('user.decks.view', [
            'deck' => $deck,
            'decks' => $decks
        ]);
    }

    public function white(Deck $deck, $page = 1)
    {
        $pages = ceil($deck->whiteCards()->count()/12);

        return view('user.decks.white', [
            'deck' => $deck,
            'cards' => $deck->whiteCards()->forPage($page,12),
            'page' => $page,
            'pages' => $pages
        ]);
    }

    public function black(Deck $deck, $page = 1)
    {
        $pages = ceil($deck->blackCards()->count()/12);

        return view('user.decks.black', [
            'deck' => $deck,
            'cards' => $deck->blackCards()->forPage($page, 12),
            'page' => $page,
            'pages' => $pages
        ]);
    }

    public function addBlack(Deck $deck, Request $request, Card $card = null)
    {
        if($deck->user_id !== Auth::id()) {
            abort(403);
        }

        $text = $request->input('text');

        if(!$text || !in_array(count(explode('<blank>', $text)), range(2, 10), true)){
            abort(400);
        }

        if($card === null){
            $card = new Card();
            $card->user_id = Auth::id();
        }

        if($card->user_id !== Auth::id()) {
            abort(403);
        }

        $card->type = 'black';
        $card->text = json_encode(explode('<blank>', $text));
        $card->save();

        if($deck->cards->where('id',$card->id)->first()===null){
            $deck->cards()->attach($card);
        }

        return redirect()->back();
    }

    public function addWhite(Deck $deck, Request $request, Card $card = null)
    {
        if($deck->user_id !== Auth::id()) {
            abort(403);
        }

        $text = $request->input('text');

        if(!$text){
            abort(400);
        }

        if($card === null){
            $card = new Card();
            $card->user_id = Auth::id();
        }

        if($card->user_id !== Auth::id()) {
            abort(403);
        }

        $card->type = 'white';
        $card->text = json_encode([$text]);
        $card->save();

        if($deck->cards->where('id',$card->id)->first()===null){
            $deck->cards()->attach($card);
        }


        return redirect()->back();
    }

    public function removeCard(Deck $deck, Card $card)
    {
        if(Auth::id() !== $deck->user_id) {
            abort(403);
        }

        $deck->cards()->detach($card);
        $deck->save();

        return redirect()->back();
    }

    public function import(Deck $deck, Request $request)
    {
        if(!$request->input('deck')) {
            abort(400);
        }

        $to_import = Deck::where('id',$request->input('deck'))->first();

        if($to_import === null) {
            abort(400);
        }

        foreach($to_import->cards as $card) {
            if($deck->cards->where('id',$card->id)->first()===null){
                $deck->cards()->attach($card);
            }
        }

        return redirect()->back();
    }
}
