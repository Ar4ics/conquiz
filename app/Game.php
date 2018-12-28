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
 * @property int|null $next_question_id
 * @property int|null $winner_user_color_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array $move_order
 * @property int $move_index
 * @property string|null $mode
 * @property int|null $duration
 * @property string|null $questioned_at
 * @property string|null $stage
 * @property bool|null $is_finished
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Box[] $boxes
 * @property-read \App\CompetitiveBox $competitive_box
 * @property-read \App\Question|null $current_question
 * @property-read mixed $end
 * @property-read mixed $start
 * @property-read \App\Question|null $next_question
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserColor[] $user_colors
 * @property-read \App\UserColor|null $winner
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereCountX($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereCountY($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereCurrentQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereIsFinished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereMoveIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereMoveOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereNextQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereQuestionedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereStage($value)
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

    public $dates = ['created_at', 'updated_at'];

    protected $fillable = [
        'title',
        'next_question_id',
        'count_x',
        'count_y',
        'mode',
        'duration',
        'stage'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = ['date', 'end'];

    public function getDateAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getStartAttribute()
    {
        return $this->getLocalTime($this->created_at);
    }

    public function getEndAttribute()
    {
        return $this->getLocalTime($this->updated_at);
    }

    private function getLocalTime(Carbon $time) {
        return Carbon::createFromTimestamp($time->timestamp)
            ->timezone('Asia/Yekaterinburg')
            ->toDateTimeString();
    }

    public function setQuestionedAtAttribute(Carbon $value) {
        $this->attributes['questioned_at'] = $value->format('Y-m-d H:i:s.u');
    }

    public function getQuestionedAtAttribute($value) {
        return Carbon::parse($value);
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
            return UserColor::with('user')->find($this->move_order[$index]);
        } else {
            return null;
        }
    }

    public function shuffleUserColors()
    {
        $players = $this->user_colors()->where('had_lost', false)->pluck('id')->shuffle();
        $this->move_index = 0;
        $this->move_order = $players;
    }

    public function baseModeWinnerFound() {
        $winners = $this->user_colors()->where('had_lost', false)->get();
        if ($winners->count() === 1) {
            $this->winner_user_color_id = $winners->first()->id;
            $this->current_question_id = null;
            $this->is_finished = true;
            return true;
        }

        return false;
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
