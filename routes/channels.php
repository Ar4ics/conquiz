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


Broadcast::channel('game.{game}', function ($user, $game) {
    return true;
});

Broadcast::channel('games', function ($user) {
    return true;
});

Broadcast::channel('users', function ($user) {
    return $user;
});
