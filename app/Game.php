<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Game
 *
 * @property int $id
 * @property string $title
 * @property int $count_x
 * @property int $count_y
 * @property bool $stage1_has_finished
 * @property bool $stage2_has_finished
 * @property bool $stage3_has_finished
 * @property int|null $current_question_id
 * @property int $next_question_id
 * @property int|null $winner_user_color_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Box[] $boxes
 * @property-read \App\CompetitiveBox $competitive_box
 * @property-read \App\Question|null $current_question
 * @property-read \App\Question $next_question
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserColor[] $user_colors
 * @property-read \App\UserColor|null $winner
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereCountX($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereCountY($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereCurrentQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereNextQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereStage1HasFinished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereStage2HasFinished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereStage3HasFinished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereWinnerUserColorId($value)
 * @mixin \Eloquent
 */
class Game extends Model
{
    protected $fillable = [
        'title',
        'next_question_id',
        'count_x',
        'count_y',
    ];
    protected $hidden = ['created_at', 'updated_at'];


    public function user_colors()
    {
        return $this->hasMany(UserColor::class);
    }

    public function boxes()
    {
        return $this->hasMany(Box::class);
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
        return $this->belongsTo(UserColor::class, 'winner_user_color_id');
    }

    public function competitive_box()
    {
        return $this->hasOne(CompetitiveBox::class, 'game_id', 'id');
    }

    public function winnerFound()
    {
        if ($this->boxes->count() === ($this->count_x * $this->count_y)) {
            $ids = $this->boxes->pluck('user_color_id')->unique();
            if ($ids->count() === 1) {
                $winner = UserColor::find($ids->get(0));
                $this->winner_user_color_id = $winner->id;
                $this->current_question_id = null;
                $this->stage3_has_finished = true;
                $this->save();
                return true;
            }
        }
        return false;
    }

    public function noQuestionsLeft()
    {
        $winner = $this->user_colors()->orderBy('score', 'desc')->first();
        $this->winner_user_color_id = $winner->id;
        $this->current_question_id = null;
        $this->stage3_has_finished = true;
        $this->save();
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

    public function allCompetitorsAnswered()
    {
        foreach ($this->competitive_box->competitors as $competitor) {
            $userColor = UserColor::find($competitor);
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
