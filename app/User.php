<?php

namespace App;

use App\Game;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'game_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at', 'email', 'pivot',
    ];

    public function games()
    {
        return $this->belongsToMany(Game::class);
    }

    public function current_game() {
        return $this->belongsTo(Game::class);
    }
}
