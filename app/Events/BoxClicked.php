<?php

namespace App\Events;

use App\Box;
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

    public $box;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Box $box)
    {
        $this->box = $box;
        
        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('game.' . $this->box->user_color->game->id);
    }

    public function broadcastWith()
    {
        return [
            'x' => $this->box->x,
            'y' => $this->box->y,
            'color' => $this->box->user_color->color
        ];
    }
}
