<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Question
 *
 * @property int $id
 * @property string $title
 * @property array|null $answers
 * @property int $correct
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool|null $is_hidden
 * @property bool|null $is_exact_answer
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question whereAnswers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question whereCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question whereIsExactAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question whereIsHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Question whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Question extends Model
{
    protected $connection = 'sqlite';

    protected $casts = [
        'answers' => 'array',
        'is_hidden' => 'boolean',
        'is_exact_answer' => 'boolean',
        'correct' => 'integer'
    ];

    protected $fillable = [
        'title',
        'correct',
        'is_hidden',
        'is_exact_answer',
        'answers',
    ];

    protected $hidden = ['created_at', 'updated_at', 'is_hidden'];
}
