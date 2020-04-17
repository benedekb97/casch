<?php

namespace App\Http\Controllers;

use App\Models\Play;
use Illuminate\Http\Request;

class PlayController extends Controller
{
    public function feature($play)
    {
        $play = Play::withTrashed()->where('id',$play)->first();

        if($play == null) {
            abort(404);
        }

        $play->featured = true;
        $play->save();

        return redirect()->back();
    }

    public function unfeature($play)
    {
        $play = Play::withTrashed()->where('id',$play)->first();

        if($play == null) {
            abort(404);
        }

        $play->featured = false;
        $play->save();

        return redirect()->back();
    }
}
