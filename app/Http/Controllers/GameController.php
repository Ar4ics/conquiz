<?php

namespace App\Http\Controllers;

use App;
use App\Box;
use App\CompetitiveBox;
use App\Events\AnswersResults;
use App\Events\WinnerFound;
use App\Events\BoxClicked;
use App\Events\ShowCompetitiveBox;
use App\Events\GameCreated;
use App\Events\NewQuestion;
use App\Events\WhoMoves;
use App\Game;
use App\Question;
use App\User;
use App\UserColor;
use App\UserQuestion;
use Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $games = Game::withCount('user_colors')->with('winner.user')->get();

        $users = User::where('id', '<>', Auth::user()->id)->get();

        return view('games', ['games' => $games, 'users' => $users]);
    }


    public function store()
    {
        $nextQuestionId = rand(1, intdiv(Question::count(), 2));
        $game = Game::create([
            'title' => request('title'),
            'next_question_id' => $nextQuestionId,
            'count_x' => request('count_x'),
            'count_y' => request('count_y'),
        ]);

        $colors = ["green", "red", "blue"];
        $users = collect(request('users'));
        $users->prepend(Auth::user()->id);

        for ($i = 0; $i < $users->count(); $i++) {
            UserColor::create([
                'user_id' => $users->get($i),
                'color' => $colors[$i],
                'game_id' => $game->id
            ]);
        }

        broadcast(new GameCreated($game, $game->user_colors()->count()));

        return $game;
    }

    public function getGame($id)
    {
        $game = Game::with(['user_colors' => function (HasMany $q) {
                $q->select(['id', 'game_id', 'user_id', 'color', 'score']);
            }, 'user_colors.user' => function (BelongsTo $q) {
                $q->select(['id', 'name']);
            }])->find($id, ['id', 'title', 'current_question_id', 'count_x', 'count_y', 'stage3_has_finished', 'winner_user_color_id'])
            ?? App::abort(404);

        $competitive_box = $game->competitive_box ?? collect();

        $boxes = Box::join('user_colors', 'boxes.user_color_id', '=', 'user_colors.id')
            ->where('boxes.game_id', '=', $game->id)->get(['x', 'y', 'color']);

        $player = $game->getUserColor(Auth::user()->id) ?? collect();
        $who_moves = (!($game->current_question_id) && !($game->stage3_has_finished)) ? UserColor::join('users', 'user_colors.user_id', '=', 'users.id')
            ->where('game_id', $game->id)
            ->sequenced()
            ->first(['user_colors.id', 'user_colors.score', 'users.name']) : collect();
        $question = Question::find($game->current_question_id,
                ['id', 'title', 'a', 'b', 'c', 'd']) ?? collect();
        return view('game', [
            'game' => $game,
            'player' => $player,
            'boxes' => $boxes,
            'who_moves' => $who_moves,
            'question' => $question,
            'competitive_box' => $competitive_box,
        ]);
    }

    private function stage2Clicked(Game $game, $x, $y)
    {
        UserColor::find(request('userColorId'))->down();
        $competitiveBox = new CompetitiveBox(['x' => $x, 'y' => $y, 'competitors' => $game->user_colors->pluck('id')]);
        $game->competitive_box()->save($competitiveBox);
        broadcast(new ShowCompetitiveBox($competitiveBox->x, $competitiveBox->y, $game->id));
        $question = Question::find($game->next_question_id);
        $game->current_question_id = $question->id;
        $game->next_question_id++;
        $game->save();
        broadcast(new NewQuestion($question, $game->id));
        return response()->json([]);

    }

    private function stage3Clicked(Game $game, $x, $y)
    {
        $box = Box::where('game_id', '=', $game->id)
            ->where('x', '=', $x)
            ->where('y', '=', $y)->first();
        $userColor = UserColor::find(request('userColorId'));
        if ($box->user_color_id === $userColor->id) {
            return [
                'error' => 'Это поле вашего цвета',
                'code' => 2
            ];
        }
        $userColor->down();
        $competitors = collect();
        $competitors->push($userColor->id);
        $competitors->push($box->user_color_id);
        $competitiveBox = new CompetitiveBox(['x' => $x, 'y' => $y, 'competitors' => $competitors]);
        $game->competitive_box()->save($competitiveBox);
        broadcast(new ShowCompetitiveBox($competitiveBox->x, $competitiveBox->y, $game->id));
        $question = Question::find($game->next_question_id);
        $game->current_question_id = $question->id;
        $game->next_question_id++;
        $game->save();
        broadcast(new NewQuestion($question, $game->id));
        return response()->json([]);
    }

    public function boxClicked($id)
    {
        $game = Game::find($id);
        if ($game->current_question_id) {
            return [
                'error' => 'Задан вопрос',
                'code' => 1
            ];
        }
        $x = request('x');
        $y = request('y');
        if ($game->stage2_has_finished) {
            return $this->stage3Clicked($game, $x, $y);
        }

        if (Box::where('game_id', '=', $game->id)
            ->where('x', '=', $x)
            ->where('y', '=', $y)->first()) {
            return [
                'error' => 'Это поле занято',
                'code' => 0
            ];
        }
        if ($game->stage1_has_finished) {
            return $this->stage2Clicked($game, $x, $y);
        }
        $userColor = UserColor::find(request('userColorId'));
        $userColor->has_moved = true;
        $userColor->down();
        $userColor->save();
        $box = Box::create(['x' => $x, 'y' => $y, 'user_color_id' => $userColor->id, 'game_id' => $game->id]);
        broadcast(new BoxClicked($box));

        $who_moves = UserColor::join('users', 'user_colors.user_id', '=', 'users.id')
            ->where('game_id', $game->id)
            ->where('has_moved', '=', 'false')
            ->first(['user_colors.id', 'users.name']);
        if ($who_moves) {
            broadcast(new WhoMoves($who_moves->id, $who_moves->name, $game->id));
        } else {
            $question = Question::find($game->next_question_id);
            $game->current_question_id = $question->id;
            $game->next_question_id++;
            $game->save();
            broadcast(new NewQuestion($question, $game->id));
        }
        return response()->json([]);
    }

    private function stage2Answered(Game $game)
    {
        $userAnswer = request('userAnswer');
        $userColorId = request('userColorId');
        $questionId = request('questionId');
        UserQuestion::create([
            'question_id' => $questionId,
            'user_color_id' => $userColorId,
            'answer' => $userAnswer
        ]);
        $userColor = UserColor::find(request('userColorId'));
        $userColor->has_answered = true;
        $userColor->save();
        if ($game->allUserColorsAnswered()) {
            /** @var UserQuestion $userQuestion */
            $results = collect();
            $userQuestions = UserQuestion::join('user_colors', 'user_questions.user_color_id', '=', 'user_colors.id')
                ->where('user_colors.game_id', $game->id)
                ->where('question_id', $game->current_question_id)
                ->orderBy('user_questions.created_at')
                ->get();

            foreach ($userQuestions as $userQuestion) {
                $userQuestion->is_correct = $userQuestion->question->correct === $userQuestion->answer;
                $userQuestion->save();
                $userColor = $userQuestion->user_color;
                if ($userQuestion->is_correct) {
                    $userColor->score++;
                }
                $results->push([
                    'user_color_id' => $userQuestion->user_color_id,
                    'answer' => $userQuestion->answer,
                    'is_correct' => $userQuestion->is_correct,
                    'score' => $userColor->score
                ]);
                $userColor->has_answered = false;
                $userColor->save();
            }

            $deleted = collect();
            $cb = $game->competitive_box;
            $userQuestion = $userQuestions->first(function ($value) {
                return $value['is_correct'];
            });
            if ($userQuestion) {
                $box = Box::create(['x' => $cb->x, 'y' => $cb->y, 'user_color_id' => $userQuestion->user_color_id, 'game_id' => $game->id]);
                broadcast(new BoxClicked($box));
            } else {
                $deleted->push(['x' => $cb->x, 'y' => $cb->y]);
            }
            $cb->delete();

            $quest = Question::find($game->current_question_id);
            $game->current_question_id = null;
            $game->save();
            broadcast(new AnswersResults($results, $deleted, $quest->correct, $game->id));

            $boxesFilled = Box::where('game_id', '=', $game->id)->count();
            $boxesLeft = $game->count_x * $game->count_y - $boxesFilled;

            if ($boxesLeft == 0) {
                $game->stage2_has_finished = true;
                $game->save();
            }

            if ($game->winnerFound()) {
                broadcast(new WinnerFound($game->winner, $game->id));
            } else {
                $who_moves = UserColor::join('users', 'user_colors.user_id', '=', 'users.id')
                    ->where('game_id', $game->id)
                    ->sequenced()
                    ->first(['user_colors.id', 'users.name']);
                broadcast(new WhoMoves($who_moves->id, $who_moves->name, $game->id));
            }
        }
        return response()->json([]);

    }

    private function stage3Answered(Game $game)
    {
        $userAnswer = request('userAnswer');
        $userColorId = request('userColorId');
        $questionId = request('questionId');
        UserQuestion::create([
            'question_id' => $questionId,
            'user_color_id' => $userColorId,
            'answer' => $userAnswer
        ]);
        $userColor = UserColor::find(request('userColorId'));
        $userColor->has_answered = true;
        $userColor->save();
        if ($game->allCompetitorsAnswered()) {
            /** @var UserQuestion $userQuestion */
            $results = collect();
            $userQuestions = UserQuestion::join('user_colors', 'user_questions.user_color_id', '=', 'user_colors.id')
                ->where('user_colors.game_id', $game->id)
                ->where('question_id', $game->current_question_id)
                ->orderBy('user_questions.created_at')
                ->get();

            foreach ($userQuestions as $userQuestion) {
                $userQuestion->is_correct = $userQuestion->question->correct === $userQuestion->answer;
                $userQuestion->save();
                $userColor = $userQuestion->user_color;
                if ($userQuestion->is_correct) {
                    $userColor->score++;
                }
                $results->push([
                    'user_color_id' => $userQuestion->user_color_id,
                    'answer' => $userQuestion->answer,
                    'is_correct' => $userQuestion->is_correct,
                    'score' => $userColor->score
                ]);
                $userColor->has_answered = false;
                $userColor->save();
            }

            $deleted = collect();
            $cb = $game->competitive_box;
            $userQuestion = $userQuestions->first(function ($value) {
                return $value['is_correct'];
            });
            if ($userQuestion && $userQuestion->user_color_id === $cb->competitors[0]) {
                try {
                    Box::where('x', $cb->x)->where('y', $cb->y)
                        ->where('user_color_id', $cb->competitors[1])->delete();
                } catch (\Exception $e) {

                }
                $box = Box::create(['x' => $cb->x, 'y' => $cb->y, 'user_color_id' => $userQuestion->user_color_id, 'game_id' => $game->id]);
                broadcast(new BoxClicked($box));
            } else {
                $box = Box::where('x', $cb->x)->where('y', $cb->y)
                    ->where('user_color_id', $cb->competitors[1])->first();
                broadcast(new BoxClicked($box));
            }
            $cb->delete();


            $quest = Question::find($game->current_question_id);
            $game->current_question_id = null;
            $game->save();
            broadcast(new AnswersResults($results, $deleted, $quest->correct, $game->id));


            if ($game->winnerFound()) {
                broadcast(new WinnerFound($game->winner, $game->id));
            } else {
                $who_moves = UserColor::join('users', 'user_colors.user_id', '=', 'users.id')
                    ->where('game_id', $game->id)
                    ->sequenced()
                    ->first(['user_colors.id', 'users.name']);
                broadcast(new WhoMoves($who_moves->id, $who_moves->name, $game->id));
            }
        }
        return response()->json([]);


    }

    public function userAnswered($id)
    {
        $game = Game::find($id);
        if ($game->stage2_has_finished) {
            return $this->stage3Answered($game);
        }
        if ($game->stage1_has_finished) {
            return $this->stage2Answered($game);
        }

        $userAnswer = request('userAnswer');
        $userColorId = request('userColorId');
        $questionId = request('questionId');
        UserQuestion::create([
            'question_id' => $questionId,
            'user_color_id' => $userColorId,
            'answer' => $userAnswer
        ]);
        $userColor = UserColor::find(request('userColorId'));
        $userColor->has_answered = true;
        $userColor->save();
        if ($game->allUserColorsAnswered()) {
            /** @var UserQuestion $userQuestion */

            $results = collect();
            $deleted = collect();
            $userQuestions = UserQuestion::join('user_colors', 'user_questions.user_color_id', '=', 'user_colors.id')
                ->where('user_colors.game_id', $game->id)
                ->where('question_id', $game->current_question_id)
                ->get();
            foreach ($userQuestions as $userQuestion) {
                $userQuestion->is_correct = $userQuestion->question->correct === $userQuestion->answer;
                $userQuestion->save();
                $userColor = $userQuestion->user_color;
                if (!($userQuestion->is_correct)) {
                    $deletedBox = Box::where('user_color_id', $userColor->id)
                        ->orderBy('created_at', 'desc')->first();
                    $deleted->push($deletedBox);
                    $deletedBox->delete();
                } else {
                    $userColor->score++;
                }
                $results->push([
                    'user_color_id' => $userQuestion->user_color_id,
                    'answer' => $userQuestion->answer,
                    'is_correct' => $userQuestion->is_correct,
                    'score' => $userColor->score
                ]);
                $userColor->has_answered = false;
                $userColor->has_moved = false;
                $userColor->save();

            }
            $quest = Question::find($game->current_question_id);
            $game->current_question_id = null;
            $game->save();
            broadcast(new AnswersResults($results, $deleted, $quest->correct, $game->id));

            $boxesFilled = Box::where('game_id', '=', $game->id)->count();
            $boxesLeft = $game->count_x * $game->count_y - $boxesFilled;

            if ($boxesLeft == 0) {
                $game->stage1_has_finished = true;
                $game->stage2_has_finished = true;
                $game->save();
            } else if ($boxesLeft < $game->user_colors->count()) {
                $game->stage1_has_finished = true;
                $game->save();
            }

            $who_moves = UserColor::join('users', 'user_colors.user_id', '=', 'users.id')
                ->where('game_id', $game->id)
                ->sequenced()
                ->first(['user_colors.id', 'users.name']);
            broadcast(new WhoMoves($who_moves->id, $who_moves->name, $game->id));

        }
        return response()->json([]);

    }

}
