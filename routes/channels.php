<?php

use App\Game;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('users.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('game.{game}', function ($user, $game) {
    //return $group->hasUser($user->id);
    return true;
});

Broadcast::channel('games', function ($user) {
    //return $group->hasUser($user->id);
    return true;
});

Broadcast::channel('game_users.{game}', function ($user, $game) {
    Log::debug($user);
    return $user;
});

Broadcast::channel('users', function ($user) {
    Log::debug($user);
    return $user;
});