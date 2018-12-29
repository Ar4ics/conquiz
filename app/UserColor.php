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
 * @property int $score
 * @property bool $has_answered
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $had_lost
 * @property int|null $base_box_id
 * @property int|null $base_guards_count
 * @property int|null $place
 * @property-read \App\Box|null $base
 * @property-read \App\Game $game
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereBaseBoxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereBaseGuardsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereHadLost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereHasAnswered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor wherePlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserColor whereUserId($value)
 * @mixin \Eloquent
 */
class UserColor extends Model
{
    protected $fillable = ['user_id', 'game_id', 'color', 'base_box_id', 'base_guards_count'];
    protected $hidden = ['created_at', 'updated_at'];

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public function base()
    {
        return $this->belongsTo(Box::class, 'base_box_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
