<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use JWTAuth;
use Laravel\Socialite\Two\GoogleProvider;
use Namshi\JOSE\JWT;
use Pusher\Pusher;
use Socialite;
use Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $credentials = $request->only('email', 'password');

        if ($token = auth()->attempt($credentials)) {
            return response()->json([
                'status' => 'success',
            ])->header('Authorization', $token);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = auth()->login($user);

        return response()->json([
            'status' => 'success',
        ])->header('Authorization', $token);
    }

    public function social($provider)
    {
        /** @var GoogleProvider $driver */
        $driver = Socialite::driver($provider);
        /** @var \Laravel\Socialite\Two\User $userSocial */
        $userSocial = $driver->stateless()->user();
        if (!$userSocial) {
            return response()->json(['error' => 'user not found'], 404);
        }

        $user = User::where('email', $userSocial->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'name' => $userSocial->getName(),
                'email' => $userSocial->getEmail(),
                'avatar' => $userSocial->getAvatar(),
                'provider_id' => $userSocial->getId(),
                'provider' => $provider,
            ]);
        }

        $token = auth()->login($user);

        return response()->json()->header('Authorization', $token);
    }


    public function user() {
        return response()->json(['data' => auth()->user()]);
    }

    public function refresh() {
        return response()->json([
            'status' => 'success',
        ])->header('Authorization', Auth::refresh());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['status' => 'success']);
    }
}
