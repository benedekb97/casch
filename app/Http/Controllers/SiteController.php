<?php

namespace App\Http\Controllers;

use App\Models\Play;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $plays = Play::withTrashed()->where('featured',true)->get()->shuffle(random_int(0,1000));

        if($plays->count() >= 9){
            $plays = $plays->random(9)->shuffle();
        }

        return view('index',[
            'featured_plays' => $plays
        ]);
    }

    public function disclaimer()
    {
        return view('disclaimer');
    }
}
