<?php

namespace App\Http\Controllers\Game\BaseMode;

use App;
use App\Box;
use App\CompetitiveBox;
use App\Events\CompetitiveAnswerResults;
use App\Events\NewExactQuestion;
use App\Events\ShowCompetitiveBox;
use App\Events\UserColorsChanged;
use App\Events\WhoAttack;
use App\Events\WhoMoves;
use App\Game;
use App\Http\Controllers\Game\Helpers;
use App\Question;
use App\UserColor;
use Carbon\Carbon;
use Exception;

class Stage2Controller
{
    public static function boxClicked(Game $game, $x, $y)
    {
        $userColorsIds = UserColor::whereGameId($game->id)->get()->pluck('id');
        $competitiveBox = new CompetitiveBox(['x' => $x, 'y' => $y, 'competitors' => $userColorsIds]);
        $game->competitive_box()->save($competitiveBox);


        $question = Helpers::getQuestion($game, true);

        if (!$question) {
            return [
                'error' => 'В игре закончились вопросы'
            ];
        } else {
            UserColor::whereGameId($game->id)->update(['has_answered' => false]);

            $game->current_question_id = $question->id;
            $game->questioned_at = Carbon::now();
            $game->save();

            broadcast(new WhoAttack($competitiveBox, $game->id));
            broadcast(new NewExactQuestion($question, $game->id));
        }

        return response()->json([]);
    }

    public static function userAnswered(Game $game)
    {
        if (!$game->allUserColorsAnswered()) {
            return response()->json([]);
        }

        $question = Question::find($game->current_question_id);

        if ($question->is_hidden) {
            return ['error' => 'Скрытый вопрос'];
        }
        /** @var Carbon $questionedAt */
        $questionedAt = $game->questioned_at;
        $userQuestions = Helpers::getUserQuestions($game);

        $results = collect();
        $minDiff = null;
        $winnerUserColor = null;

        foreach ($userQuestions as $userQuestion) {
            $userQuestion->is_correct = $question->correct === $userQuestion->answer;
            $diff = abs($question->correct - $userQuestion->answer);
            if (!$minDiff === null || $diff < $minDiff) {
                $minDiff = $diff;
                $winnerUserColor = $userQuestion->user_color;
            }
            /** @var Carbon $answeredAt */
            $answeredAt = $userQuestion->answered_at;

            $results->push([
                'user_name' => $userQuestion->user_color->user->name,
                'answer' => $userQuestion->answer,
                'is_correct' => $userQuestion->is_correct,
                'time' => $answeredAt->diff($questionedAt)->format('%s.%f')
            ]);
            $userQuestion->save();
        }

        if (!$winnerUserColor) {
            return ['error' => 'Этап 2. Победитель не выявлен'];
        }

        $competitiveBox = $game->competitive_box;
        $winnerUserColor->score += 300;
        $winnerUserColor->save();
        $targetBox = Box::create([
            'x' => $competitiveBox->x,
            'y' => $competitiveBox->y,
            'user_color_id' => $winnerUserColor->id,
            'cost' => 300,
            'game_id' => $game->id
        ]);
        $targetBox['color'] = $winnerUserColor->color;
        $targetBox['base_guards_count'] = 0;

        try {
            $competitiveBox->delete();
        } catch (Exception $exception) {
            return ['error' => 'Этап 2. Ошибка при удалении квадрата'];
        }

        $boxesFilled = Box::where('game_id', '=', $game->id)->count();
        $boxesLeft = $game->count_x * $game->count_y - $boxesFilled;

        if ($boxesLeft === 0) {
            $game->stage2_has_finished = true;
        }

        $game->current_question_id = null;
        $game->move_index++;

        $who_moves = $game->getMovingUserColor();
        if (!$who_moves) {
            $game->shuffleUserColors();
            $who_moves = $game->getMovingUserColor();
        } else {
            $game->save();
        }

        broadcast(new CompetitiveAnswerResults($results, $targetBox, $question->correct, $winnerUserColor, $game->id));
        broadcast(new WhoMoves($who_moves, $game->id));
        broadcast(new UserColorsChanged($game->id));

        return response()->json([]);
    }
}