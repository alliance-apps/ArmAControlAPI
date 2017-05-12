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

Route::get('version', 'GETController@version');

//Player
Route::get('player', 'GETController@getPlayer');
Route::get('players/light', 'GETController@getPlayersLight');
Route::get('players/complete', 'GETController@getPlayersComplete');
Route::get('players/moneysum', 'GETController@getMoneySum');
Route::get('players/possiblelevels', 'GETController@getPossibleLevels');

Route::post('updateplayer/gear/{uid}', 'PlayerProfileController@editPlayerGear');
Route::post('updateplayer/level/{uid}', 'PlayerProfileController@editPlayerLevel');
Route::post('updateplayer/licenses/{uid}', 'PlayerProfileController@editPlayerLicenses');
Route::post('updateplayer/money/{uid}', 'PlayerProfileController@editPlayerMoney');


Route::get('player/{uid}', 'GETController@getPlayer');


Route::get('vehicle/detail/{id}', 'VehicleController@detail');
Route::get('vehicle/list/{id}', 'VehicleController@listForPlayer');
