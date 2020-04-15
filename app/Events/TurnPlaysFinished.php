<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TurnPlaysFinished implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $game_slug;

    public function __construct($message, $game_slug)
    {
        $this->message = $message;
        $this->game_slug = $game_slug;
    }

    public function broadcastOn()
    {
        return ['game-' . $this->game_slug];
    }

    public function broadcastAs()
    {
        return 'turn-plays-finished';
    }
}
