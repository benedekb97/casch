<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewTurn implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message,$game_slug;


    public function __construct($message, $game_slug)
    {
        $this->game_slug = $game_slug;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return ['game-' . $this->game_slug];
    }

    public function broadcastAs()
    {
        return 'new-turn';
    }
}
