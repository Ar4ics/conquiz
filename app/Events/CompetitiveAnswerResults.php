<?php

namespace App\Events;

use App\Box;
use App\UserColor;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CompetitiveAnswerResults implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $results;
    public $result_box;
    public $game_id;
    public $correct;
    public $winner;


    public function __construct($results, Box $result_box, $correct, $winner, $game_id)
    {
        $this->results = $results;
        $this->result_box = $result_box->toArray();
        $this->game_id = $game_id;
        $this->correct = $correct;
        $this->winner = UserColor::with('user')->find($winner['id']);
    }


    public function broadcastOn()
    {
        return new PrivateChannel('game.' . $this->game_id);
    }

    public function broadcastWith()
    {
        return [
            'results' => $this->results,
            'result_box' => $this->result_box,
            'correct' => $this->correct,
            'winner' => $this->winner,
        ];
    }
}
