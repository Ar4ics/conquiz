<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['auth:api']], function() {
    Route::get('/games', 'GameController@index');
    Route::post('/games', 'GameController@store');

    Route::get('/games/{id}', 'GameController@getGame');

    Route::post('/games/{id}/box/clicked', 'GameController@boxClicked');
    Route::post('/games/{id}/user/answered', 'GameController@userAnswered');

    Route::post('/games/{id}/base/box/clicked', 'Game\BaseMode\GameController@boxClicked');
    Route::post('/games/{id}/base/user/answered', 'Game\BaseMode\GameController@userAnswered');

    Route::resource('/games/{id}/message', 'GameMessageController');
});

Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
    Route::group([], function() {
        Route::post('login', 'AuthController@login');
        Route::post('register', 'AuthController@register');
    });

    Route::group(['middleware' => ['auth:api']], function() {
        Route::get('user', 'AuthController@user');
        Route::post('logout', 'AuthController@logout');
        Route::get('refresh', 'AuthController@refresh');
    });
});

Route::post('auth/{provider}', 'Auth\AuthController@social');
