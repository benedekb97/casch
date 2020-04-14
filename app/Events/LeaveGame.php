<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaveGame implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $game_slug;
    public $new_host;

    public function __construct($message, $game_slug, $new_host = null)
    {
        $this->message = $message;
        $this->game_slug = $game_slug;
        $this->new_host = $new_host;
    }

    public function broadcastOn()
    {
        return ['game-'.$this->game_slug];
    }

    public function broadcastAs()
    {
        return 'leave-game';
    }
}
