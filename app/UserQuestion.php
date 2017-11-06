<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * App\UserQuestion
 *
 * @property int $id
 * @property int $question_id
 * @property int $user_color_id
 * @property int $answer
 * @property bool|null $is_correct
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Question $question
 * @property-read \App\UserColor $user_color
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserQuestion whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserQuestion whereIsCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserQuestion whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserQuestion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserQuestion whereUserColorId($value)
 * @mixin \Eloquent
 */
class UserQuestion extends Model
{
    protected $fillable = ['question_id', 'user_color_id', 'answer'];
    protected $hidden = ['updated_at'];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function user_color()
    {
        return $this->belongsTo(UserColor::class, 'user_color_id');
    }
}
