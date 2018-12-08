<?php

namespace App;

use App\Events\WinnerFound;
use Carbon\Carbon;
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
 * @property array $move_order
 * @property int $move_index
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereMoveIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereMoveOrder($value)
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
    protected $casts = [
        'move_order' => 'array'
    ];

    protected $fillable = [
        'title',
        'next_question_id',
        'count_x',
        'count_y',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = ['start', 'end'];

    public function getStartAttribute()
    {
        return $this->getLocalTime($this->created_at);
    }

    public function getEndAttribute()
    {
        return $this->getLocalTime($this->updated_at);
    }

    private function getLocalTime($value) {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone('Asia/Yekaterinburg')
            ->toDateTimeString();
    }


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

    public function getMovingUserColor()
    {
        $index = $this->move_index;
        if ($index < count($this->move_order)) {
            return UserColor::find($this->move_order[$index]);
        } else {
            return null;
        }
    }

    public function shuffleUserColors()
    {
        $players = $this->user_colors()->where('had_lost', false)->pluck('id')->shuffle();
        $this->move_index = 0;
        $this->move_order = $players;
        $this->save();

    }

    public function winnerUserColorWasFound()
    {
        $losers = collect([]);
        $players = $this->user_colors()->where('had_lost', false)->get();
        foreach ($players as $userColor) {
            $count = Box::where('user_color_id', '=', $userColor->id)->count();
            if ($count === 0) {
                $losers->push($userColor);
                $userColor->had_lost = true;
                $userColor->save();
            }
        }

        if ($losers->isEmpty()) {
            return false;
        }

        $diff = $players->diff($losers);
        if ($diff->count() === 1) {
            $winner = UserColor::find($diff->first()->id);
            $this->winner_user_color_id = $winner->id;
            $this->current_question_id = null;
            $this->stage3_has_finished = true;
            $this->save();
            return true;
        }

        $this->shuffleUserColors();
        return false;
    }
}
