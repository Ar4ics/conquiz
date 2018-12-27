<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Laravel\Socialite\Two\GoogleProvider;
use Log;
use Socialite;

class SocialController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function redirect($provider)
    {
        /** @var GoogleProvider $driver */
        $driver = Socialite::driver($provider);
        return $driver->with(["access_type" => "offline", "prompt" => "consent select_account"])->stateless()->redirect();
    }


    public function callback(Request $request, $provider)
    {
        /** @var GoogleProvider $driver */
        $driver = Socialite::driver($provider);
        /** @var \Laravel\Socialite\Two\User $userSocial */
        $userSocial = $driver->stateless()->user();
        if (!$userSocial) {
            return ['error' => 'user is null'];
        }

        dd($userSocial);

        $user = User::where('email', $userSocial->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'name'          => $userSocial->getName(),
                'email'         => $userSocial->getEmail(),
                'avatar'         => $userSocial->getAvatar(),
                'provider_id'   => $userSocial->getId(),
                'provider'      => $provider,
            ]);
        }

        $token = auth()->login($user);

        return response()->json([
            'status' => 'success',
        ])->header('Authorization', $token);
    }
}
