<?php

namespace App;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\GameMessage
 *
 * @property int $id
 * @property int $game_id
 * @property int $user_id
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Game $game
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GameMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GameMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GameMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GameMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GameMessage whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GameMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GameMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GameMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GameMessage whereUserId($value)
 * @mixin \Eloquent
 */
class GameMessage extends Model
{
    protected $fillable = ['message', 'user_id', 'game_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public static $createRules = [
        'message' => 'required|string',
        'user_id' => 'required|integer',
        'game_id' => 'required|integer',
    ];

    protected $appends = ['date', 'time'];

    public function getDateAttribute()
    {
        return Carbon::createFromTimestamp(strtotime($this->created_at))
            ->timezone('Asia/Yekaterinburg')
            ->toDateString();
    }

    public function getTimeAttribute()
    {
        return Carbon::createFromTimestamp(strtotime($this->created_at))
            ->timezone('Asia/Yekaterinburg')
            ->toTimeString();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

}