<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Box
 *
 * @property int $id
 * @property int $x
 * @property int $y
 * @property int $user_color_id
 * @property int $game_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $cost
 * @property-read \App\Game $game
 * @property-read \App\UserColor $user_color
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Box newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Box newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Box query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Box whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Box whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Box whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Box whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Box whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Box whereUserColorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Box whereX($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Box whereY($value)
 * @mixin \Eloquent
 */
class Box extends Model
{
    protected $fillable = ['x', 'y', 'user_color_id', 'cost', 'game_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function user_color()
    {
        return $this->belongsTo(UserColor::class, 'user_color_id');
    }

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }
}
