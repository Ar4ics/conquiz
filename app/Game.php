<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['title', 'user_id', 'in_progress', ];
    protected $hidden = ['pivot', 'created_at', 'updated_at', ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }

    public function current_move()
    {
        return $this->belongsTo(User::class, 'move_user_id');
    }

    public function hasUser($user_id)
    {
        foreach ($this->users as $user) {
            if($user->id == $user_id) {
                return true;
            }
        }
    }
}
