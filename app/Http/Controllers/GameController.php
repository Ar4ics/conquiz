<?php

namespace App\Http\Controllers;

use App;
use App\Box;
use App\Events\GameCreated;
use App\Game;
use App\GameMessage;
use App\Helpers\Constants;
use App\Helpers\ErrorConstants;
use App\Http\Controllers\Game\Stage1Controller;
use App\Http\Controllers\Game\Stage2Controller;
use App\Http\Controllers\Game\Stage3Controller;
use App\Question;
use App\User;
use App\UserColor;
use App\UserQuestion;
use Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Validator;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::with('user_colors.user')->with('winner.user')->orderBy('updated_at', 'desc')->get();

        $users = User::where('id', '<>', Auth::user()->id)->get();

        $messages = GameMessage::whereGameId(0)->with('user')->orderBy('created_at')->get();

        return view('games', [
            'games' => json_encode($games),
            'users' => json_encode($users),
            'messages' => json_encode($messages),
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'title' => 'required|string',
            'count_x' => 'required|integer|between:2,12',
            'count_y' => 'required|integer|between:2,12',
            'mode' => 'required|string|in:classic,base_mode',
            'duration' => 'nullable|integer',
            'users' => 'required|array',
            'users.*' => 'integer'
        ]);

        if ($validator->fails()) {
            return [
                'error' => ErrorConstants::GAME_VALIDATION_ERRORS,
            ];
        }

        if ($data['mode'] === 'classic') {
            $data['next_question_id'] = Question::whereIsHidden(false)
                ->whereIsExactAnswer(false)
                ->inRandomOrder()
                ->first()->id;
        }

        $data['stage'] = Constants::GAME_STAGE_1;
        $game = Game::create($data);

        $colors = ['LightPink', 'LightGreen', 'LightBlue'];
        $users = collect($data['users']);
        $users->prepend(Auth::user()->id);

        if ($users->count() > count($colors) || $users->count() < 2) {
            return [
                'error' => ErrorConstants::GAME_VALIDATION_ERRORS,
            ];
        }

        for ($i = 0; $i < $users->count(); $i++) {
            UserColor::create([
                'user_id' => $users->get($i),
                'color' => $colors[$i],
                'game_id' => $game->id
            ]);
        }

        $game->shuffleUserColors();

        $game->save();

        broadcast(new GameCreated($game));

        return response()->json(['game' => $game]);
    }

    public function getGame($id)
    {
        $game = Game::find($id);
        if (!$game) {
            App:abort(404);
        }
        $userColors = UserColor::whereGameId($game->id)->with([
            'user' => function (BelongsTo $q) {
                $q->select(['id', 'name']);
            },
            'base' => function (BelongsTo $q) {
                $q->select(['id', 'x', 'y']);
            }])->orderBy('score', 'desc')->get();

        $boxes = Box::join('user_colors', 'boxes.user_color_id', '=', 'user_colors.id')
            ->where('boxes.game_id', '=', $game->id)->get(['x', 'y', 'color', 'user_color_id', 'cost']);

        $player = $game->getUserColor(Auth::user()->id);
        $whoMoves = $game->getMovingUserColor();

        $field = [];

        for ($y = 0; $y < $game->count_y; $y++) {
            $field[$y] = [];
            for ($x = 0; $x < $game->count_x; $x++) {
                $field[$y][] = [
                    'x' => $x,
                    'y' => $y,
                    'color'=> 'white',
                    'cost'=> 0,
                    'user_color_id'=> null,
                    'base'=> null
                ];
            }
        }

        foreach ($boxes as $box) {
            $b = &$field[$box->y][$box->x];
            $b['cost'] = $box->cost;
            $b['user_color_id'] = $box->user_color_id;
            $b['color'] = $box->user_color->color;
        }

        foreach ($userColors as $userColor) {
            if ($userColor->base) {
                $b = &$field[$userColor->base->y][$userColor->base->x];
                $b['base'] = ['guards' => $userColor->base_guards_count, 'user_name' => $userColor->user->name];
            }
        }

        $winner = UserColor::with('user')->find($game->winner_user_color_id);

        $competitiveBox = $game->competitive_box;

        $question = Question::find($game->current_question_id);

        $messages = GameMessage::whereGameId($game->id)->with('user')->orderBy('created_at')->get();

        return view('game', [
            'game' => json_encode($game),
            'player' => json_encode($player),
            'field' => json_encode($field),
            'who_moves' => json_encode($whoMoves),
            'question' => json_encode($question),
            'user_colors' => json_encode($userColors),
            'competitive_box' => json_encode($competitiveBox),
            'winner' => json_encode($winner),
            'messages' => json_encode($messages),
        ]);
    }

    public function boxClicked($id)
    {
        $game = Game::find($id);
        if ($game->stage3_has_finished) {
            return [
                'error' => 'Игра завершена',
            ];
        }
        if ($game->current_question_id) {
            return [
                'error' => 'Задан вопрос',
            ];
        }
        $x = request('x');
        $y = request('y');
        $userColor = UserColor::find(request('userColorId'));

        $who_moves = $game->getMovingUserColor();
        if ($userColor->id !== $who_moves->id) {
            return [
                'error' => 'Сейчас ходит ' . $who_moves->user->name,
            ];
        }

        $box = Box::where('game_id', '=', $game->id)
            ->where('x', '=', $x)
            ->where('y', '=', $y)->first();

        if ($game->stage2_has_finished) {
            return Stage3Controller::boxClicked($game, $x, $y, $userColor, $box);
        }

        if ($box) {
            return [
                'error' => 'Это поле занято',
            ];
        }

        if ($game->stage1_has_finished) {
            return Stage2Controller::boxClicked($game, $x, $y);
        }

        return Stage1Controller::boxClicked($game, $x, $y, $userColor);
    }

    public function userAnswered($id)
    {
        $game = Game::find($id);
        if ($game->stage3_has_finished) {
            return [
                'error' => 'Игра завершена',
            ];
        }

        if (!$game->current_question_id) {
            return [
                'error' => 'Вопрос не задан',
            ];
        }

        $userAnswer = request('userAnswer');
        $userColorId = request('userColorId');
        $questionId = request('questionId');
        $userColor = UserColor::find(request('userColorId'));

        if ($game->stage2_has_finished && (!in_array($userColor->id, $game->competitive_box->competitors))) {
            return [
                'error' => 'Вы не должны отвечать на этот вопрос',
            ];
        }

        if (UserQuestion::where('question_id', '=', $questionId)
            ->where('user_color_id', '=', $userColorId)->first()) {
            return [
                'error' => 'Вы уже ответили на этот вопрос',
            ];
        }

        UserQuestion::create([
            'question_id' => $questionId,
            'user_color_id' => $userColorId,
            'answer' => $userAnswer
        ]);
        $userColor->has_answered = true;
        $userColor->save();
        if ($game->stage2_has_finished) {
            return Stage3Controller::userAnswered($game);
        }

        if ($game->stage1_has_finished) {
            return Stage2Controller::userAnswered($game);
        }

        return Stage1Controller::userAnswered($game);
    }

}
