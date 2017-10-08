<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Question
 *
 * @property int $id
 * @property string $title
 * @property string $a
 * @property string $b
 * @property string $c
 * @property string $d
 * @property int $correct
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question whereA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question whereB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question whereC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question whereCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question whereD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Question extends Model
{
    protected $fillable = ['title', 'a', 'b', 'c', 'd', 'correct'];
    protected $hidden = ['created_at', 'updated_at'];
}
