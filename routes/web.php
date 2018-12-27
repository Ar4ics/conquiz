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
//Route::get('auth/{provider}/redirect', 'Auth\SocialController@redirect');
//Route::get('auth/{provider}', 'Auth\SocialController@callback');
Route::get('/{any}', 'SpaController@index')->where('any', '.*');
