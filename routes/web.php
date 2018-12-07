<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/crawl', 'CrawlController@parse');
Route::auth();
Route::get('/', 'HomeController@index');
Route::group(['middleware' => ['auth']], function() {
    Route::get('/home', 'HomeController@index');

    Route::get('/games', 'GameController@index');
    Route::post('/games', 'GameController@store');

    Route::get('/games/{id}', 'GameController@getGame');

    Route::post('/games/{id}/box/clicked', 'GameController@boxClicked');
    Route::post('/games/{id}/user/answered', 'GameController@userAnswered');

    Route::resource('/games/{id}/message', 'GameMessageController');
});

