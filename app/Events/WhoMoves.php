<?php

namespace App\Events;

use App\UserColor;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WhoMoves implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id;
    public $name;
    public $game_id;

    public function __construct($id, $name, $game_id)
    {
        $this->id = $id;
        $this->name = $name;
        $this->game_id = $game_id;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('game.' . $this->game_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}
