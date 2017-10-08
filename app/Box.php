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
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\UserColor $user_color
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Box whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Box whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Box whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Box whereUserColorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Box whereX($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Box whereY($value)
 * @mixin \Eloquent
 */
class Box extends Model
{
    protected $fillable = ['x', 'y', 'user_color_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function user_color()
    {
        return $this->belongsTo(UserColor::class, 'user_color_id');
    }
}
