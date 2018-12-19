<?php

namespace App\Events;

use App\Game;
use App\UserColor;
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
    public $user_colors;

    public function __construct(Game $game)
    {
        $this->game = $game;
        $this->user_colors = UserColor::with('user')->whereGameId($game->id)->get()->toArray();
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
            'game' => $this->game,
            'user_colors' => $this->user_colors
        ];
    }
}
