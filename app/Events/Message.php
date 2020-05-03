<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Message implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $game_slug;

    public function __construct($game_slug, $message)
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
        return 'message';
    }
}
