<?php

namespace App\Events;

use App\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $game;
    public $users_count;

    public function __construct(Game $game, $users_count)
    {
        $this->game = $game;
        $this->users_count = $users_count;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('games');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->game->id,
            'title' => $this->game->title,
            'user_colors_count' => $this->users_count
        ];
    }
}
