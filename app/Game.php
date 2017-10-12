<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Game
 *
 * @property int $id
 * @property string $title
 * @property bool $in_progress
 * @property int|null $current_question_id
 * @property int $next_question_id
 * @property int|null $winner_user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Question|null $current_question
 * @property-read \App\Question $next_question
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserColor[] $user_colors
 * @property-read \App\User|null $winner
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereCurrentQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereInProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereNextQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereWinnerUserId($value)
 * @mixin \Eloquent
 */
class Game extends Model
{
    protected $fillable = ['title', 'next_question_id', 'in_progress'];
    protected $hidden = ['created_at', 'updated_at'];


    public function user_colors()
    {
        return $this->hasMany(UserColor::class);
    }

    public function current_question()
    {
        return $this->belongsTo(Question::class, 'current_question_id');
    }

    public function next_question()
    {
        return $this->belongsTo(Question::class, 'next_question_id');
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }

    public function allUserColorsAnswered()
    {
        foreach ($this->user_colors as $userColor) {
            if (!($userColor->has_answered)) {
                return false;
            }
        }
        return true;
    }

    public function getUserColor($user_id)
    {
        foreach ($this->user_colors as $userColor) {
            if ($userColor->user_id === $user_id) {
                return $userColor;
            }
        }
        return null;
    }
}
