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

Route::get('version', 'PlayerController@version');

//Player
Route::get('player', 'PlayerController@getPlayer');
Route::get('players/light', 'PlayerController@getPlayersLight');
Route::get('players/complete', 'PlayerController@getPlayersComplete');
Route::get('players/moneysum', 'PlayerController@getMoneySum');
Route::get('players/possiblelevels', 'PlayerController@getPossibleLevels');
Route::get('player/{uid}', 'PlayerController@getPlayer');
Route::post('updateplayer/gear/{uid}', 'PlayerController@editPlayerGear');
Route::post('updateplayer/level/{uid}', 'PlayerController@editPlayerLevel');
Route::post('updateplayer/licenses/{uid}', 'PlayerController@editPlayerLicenses');
Route::post('updateplayer/money/{uid}', 'PlayerController@editPlayerMoney');

//Vehicle
Route::get('vehicle/detail/{id}', 'VehicleController@detail');
Route::get('vehicle/list/{id}', 'VehicleController@listForPlayer');
Route::patch('vehicle/{vid}', 'VehicleController@repairVehicle');
Route::patch('vehicle/{vid}/return', 'VehicleController@returnVehicle');
Route::delete('vehicle/{vid}', 'VehicleController@deleteVehicle');
Route::patch('vehicle/{vid}/edit', 'VehicleController@editVehicle');
Route::patch('vehicle/{vid}/sidegarage', 'VehicleController@sideAndGarageChangeVehicle');
Route::patch('vehicle/{vid}/changeowner', 'VehicleController@changeVehicleOwner');