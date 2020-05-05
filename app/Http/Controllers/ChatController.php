<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Message;
use Auth;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function send(Request $request, $slug)
    {
        $game = Game::where('slug',$slug)->first();

        if(null === $game) {
            abort(404);
        }

        $message = new Message();
        $message->user_id = Auth::id();
        $message->game_id = $game->id;
        $message->message = strip_tags($request->input('message'));
        $message->save();

        event(new \App\Events\Message($game->slug, [
            'sent_by' => Auth::user()->nickname ?: Auth::user()->name,
            'message' => strip_tags($request->input('message')),
            'sent_at' => date('H:i'),
            'spectator' => Auth::user()->game()->id != $game->id,
            'user_id' => Auth::id()
        ]));

        return response()->json(['success' => true]);
    }

    public function get($slug)
    {
        $game = Game::where('slug' , $slug)->first();

        if(null === $game) {
            abort(404);
        }

        if(false === $game->spectatable && Auth::user()->game()->id !== $game->id) {
            abort(401);
        }

        $messages = Message::where('game_id', $game->id)->orderByDesc('id')->take(10)->get();

        $new_messages = [];

        foreach($messages as $message){
            $new_messages[] = [
                'user_id' => $message->user_id,
                'message' => $message->message,
                'sent_by' => $message->user->nickname ?: $message->user->name,
                'sent_at' => date("H:i", strtotime($message->created_at)),
                'spectator' => $message->user->game()->id !== $game->id
            ];
        }

        return response()->json([
            'messages' => collect($new_messages)
        ]);
    }
}
