<?php

namespace App;

use HighSolutions\EloquentSequence\Sequence;
use Illuminate\Database\Eloquent\Model;

/**
 * App\UserColor
 *
 * @property int $id
 * @property int $user_id
 * @property int $game_id
 * @property string $color
 * @property bool $has_moved
 * @property bool $has_answered
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Game $game
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereHasAnswered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereHasMoved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereUserId($value)
 * @mixin \Eloquent
 */
class UserColor extends Model
{
    protected $fillable = ['user_id', 'game_id', 'color', 'has_moved'];
    protected $hidden = ['created_at', 'updated_at'];

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
