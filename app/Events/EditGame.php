<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EditGame implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public $game_slug;

    public $host_id;

    public function __construct($message, $game_slug, $host_id)
    {
        $this->message = $message;
        $this->game_slug = $game_slug;
        $this->host_id = $host_id;
    }

    public function broadcastOn()
    {
        return ['game-'.$this->game_slug];
    }

    public function broadcastAs()
    {
        return 'edit-game';
    }
}
