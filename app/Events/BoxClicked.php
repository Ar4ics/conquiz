<?php

namespace App\Events;

use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BoxClicked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $game_id;
    public $x;
    public $y;
    

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($x, $y, $id)
    {
        $this->x = $x;
        $this->y = $y;
        $this->game_id = $id;
        
        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('game.' . $this->game_id);
    }

    public function broadcastWith()
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
        ];
    }
}
