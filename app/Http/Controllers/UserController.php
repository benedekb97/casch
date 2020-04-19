<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile()
    {
        $user = Auth::user();

        return view('user.profile', [
            'user' => $user
        ]);
    }

    public function edit()
    {
        $user = Auth::user();

        return view('user.profile.edit', [
            'user' => $user
        ]);
    }

    public function save(Request $request)
    {
        $user = User::find(Auth::id());

        $user->nickname = $request->input('nickname');
        $user->save();

        return redirect()->route('user.profile');
    }

    public function decks()
    {
        $user = Auth::user();
        $decks = $user->decks;

        return view('user.decks', [
            'user' => $user,
            'decks' => $decks
        ]);
    }
}
