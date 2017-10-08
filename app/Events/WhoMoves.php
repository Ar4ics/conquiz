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

    public $whoMoves;

    public function __construct(UserColor $whoMoves)
    {
        $this->whoMoves = $whoMoves;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('game.' . $this->whoMoves->game_id);
    }

    public function broadcastWith()
    {
        return [
            'who_moves' => $this->whoMoves
        ];
    }
}
