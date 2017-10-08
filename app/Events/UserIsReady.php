<?php

namespace App\Events;

use App\User;
use App\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserIsReady implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $game_id;


    public function __construct(User $user, $game_id)
    {
        // $this->user = $user;
        $this->user = $user;
        $this->game_id = $game_id;
        
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
            'user' => $this->user,
        ];
    }
}
