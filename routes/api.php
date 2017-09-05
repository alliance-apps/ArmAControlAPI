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
Route::get('dashboardstats', 'PlayerController@getDashboardStats');
Route::get('dashboard/last30days', 'PlayerController@getlast30days');

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
Route::patch('updateplayer/otherdata/{uid}', 'PlayerController@editOtherData');

Route::get('player/{uid}/customfields', 'PlayerController@getCustomFields');
Route::patch('player/{uid}/customfields', 'PlayerController@changeCustomFields');






//Vehicle
Route::get('vehicle/detail/{id}', 'VehicleController@detail');
Route::get('vehicle/list', 'VehicleController@listAll');
Route::get('vehicle/list/{id}', 'VehicleController@listForPlayer');
Route::patch('vehicle/{vid}', 'VehicleController@repairVehicle');
Route::patch('vehicle/{vid}/return', 'VehicleController@returnVehicle');
Route::delete('vehicle/{vid}', 'VehicleController@deleteVehicle');
Route::patch('vehicle/{vid}/edit', 'VehicleController@editVehicle');
Route::patch('vehicle/{vid}/sidegarage', 'VehicleController@sideAndGarageChangeVehicle');
Route::patch('vehicle/{vid}/changeowner', 'VehicleController@changeVehicleOwner');

Route::get('gang/list', 'GangController@ganglist');
Route::get('gang/{id}', 'GangController@gang');
Route::delete('gang/{id}', 'GangController@deleteMember');
Route::put('gang/{id}', 'GangController@addMember');
Route::patch('gang/{id}/owner', 'GangController@changeOwner');
Route::patch('gang/{id}/name', 'GangController@changeName');
Route::patch('gang/{id}/other', 'GangController@changeOther');

Route::get('wanted/list', 'WantedController@wantedlist');
Route::get('wanted/{pid}', 'WantedController@wantedlistForPlayer');
Route::delete('wanted/{pid}', 'WantedController@deletePlayerWanted');

Route::get('house/list', 'HouseController@houselist');
Route::get('houses/{pid}', 'HouseController@houselistForPlayer');
Route::get('house/{id}', 'HouseController@house');

Route::get('allianceapps/locker/list', 'AALockerController@lockerList');