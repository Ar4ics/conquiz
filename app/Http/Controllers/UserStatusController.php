<?php

namespace App\Http\Controllers;


use App\Events\UserOffline;
use App\Events\UserOnline;
use App\Game;
use Auth;
use Log;

class UserStatusController extends Controller
{
    public function goOffline()
    {
        Log::debug('offline');

        $user = Auth::user();
        $user->status = 'offline';
        $user->save();

        broadcast(new UserOffline($user));
    }
}