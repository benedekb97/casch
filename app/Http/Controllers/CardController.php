<?php

namespace App\Http\Controllers;

use App\Models\Card;
use File;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index()
    {
        return view('cards.index');
    }

    public function black()
    {
        $cards = Card::where('type','black')->paginate(12);

        return view('cards.black', [
            'cards' => $cards
        ]);
    }

    public function white()
    {
        $cards = Card::where('type', 'white')->paginate(12);

        return view('cards.white', [
            'cards' => $cards
        ]);
    }

    public function add(Request $request)
    {
        if($request->input('type')=='black'){
            $text_parts = explode('<blank>', $request->input('text'));
        }else{
            $text_parts = [$request->input('text')];
        }

        $card = new Card();
        $card->type=$request->input('type');
        $card->text = json_encode($text_parts);
        $card->save();

        return redirect()->back();
    }

    public function delete(Card $card)
    {
        $card->delete();

        return redirect()->back();
    }
}
