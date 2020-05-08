<?php

namespace App\Http\Controllers;

use App\Exceptions\IDontGiveAFuckException;
use App\Models\ButtHurt;
use Auth;
use Illuminate\Http\Request;

class ButtHurtController extends Controller
{
    public function butthurt(Request $request)
    {
        if($request->getMethod() === 'POST')
        {
            $message = $request->input('message') ?: "";
            $user = Auth::id();
            $card = $request->input('card') ?: "";

            $butthurt = new ButtHurt();
            $butthurt->message = $message;
            $butthurt->card = $card;
            $butthurt->user_id = $user;
            $butthurt->save();

            return view('butthurt');
        }


        return view('complaint');

    }
}
