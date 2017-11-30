<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    public function detail($id)
    {
        $vehicle = DB::table('vehicles')->where('id', $id)->get();

        $output = [];
        foreach ($vehicle as $v) {
            $output['id'] = $v->id;
            $output['side'] = $v->side;
            $output['class'] = $v->classname;
            $output['type'] = $v->type;
            $output['pid'] = $v->pid;
            $output['alive'] = $v->alive;
            $output['blacklist'] = $v->blacklist;
            $output['active'] = $v->active;
            $output['plate'] = $v->plate;
            $output['color'] = $v->color;
            $output['inventory'] = $v->inventory;
            $output['gear'] = $v->gear;
            $output['fuel'] = $v->fuel;
            $output['damage'] = $v->damage;
            $output['insert_time'] = $v->insert_time;
        }
        return $output;
    }


    public function listAll()
    {
        $vehicles = DB::table('vehicles')->get();

        $output = [];
        $count = 0;
        foreach ($vehicles as $v) {
            $output[$count]['id'] = $v->id;
            $output[$count]['side'] = $v->side;
            $output[$count]['class'] = $v->classname;
            $output[$count]['type'] = $v->type;
            $output[$count]['pid'] = $v->pid;
            $output[$count]['alive'] = $v->alive;
            $output[$count]['blacklist'] = $v->blacklist;
            $output[$count]['active'] = $v->active;
            $output[$count]['plate'] = $v->plate;
            $output[$count]['color'] = $v->color;
            $output[$count]['inventory'] = $v->inventory;
            $output[$count]['gear'] = $v->gear;
            $output[$count]['fuel'] = $v->fuel;
            $output[$count]['damage'] = $v->damage;
            $output[$count]['insert_time'] = $v->insert_time;
            $count++;
        }
        return $output;
    }


    public function listForPlayer($id)
    {
        $player = DB::table('players')->where('uid', $id)->first();
        $pid = config('sharedapi.pid');
        $playerid = $player->$pid;
        $vehicles = DB::table('vehicles')->where('pid', $playerid)->get();

        $output = [];
        $count = 0;
        foreach ($vehicles as $v) {
            $output[$count]['id'] = $v->id;
            $output[$count]['side'] = $v->side;
            $output[$count]['class'] = $v->classname;
            $output[$count]['type'] = $v->type;
            $output[$count]['pid'] = $v->pid;
            $output[$count]['alive'] = $v->alive;
            $output[$count]['blacklist'] = $v->blacklist;
            $output[$count]['active'] = $v->active;
            $output[$count]['plate'] = $v->plate;
            $output[$count]['color'] = $v->color;
            $output[$count]['inventory'] = $v->inventory;
            $output[$count]['gear'] = $v->gear;
            $output[$count]['fuel'] = $v->fuel;
            $output[$count]['damage'] = $v->damage;
            $output[$count]['insert_time'] = $v->insert_time;
            $count++;
        }
        return $output;
    }


    public function repairVehicle($vid)
    {
        $vehicle = DB::table('vehicles')->where('id', $vid)->first();
        $changed = ($vehicle->alive != 1);
        DB::table('vehicles')->where('id', $vid)->update(['alive' => 1]);
        $player = DB::table('players')->where(config('sharedapi.pid'), $vehicle->pid)->first();
        $playerid = $player->uid;

        $toLog['vid'] = $vid;
        $toLog['pid'] = $playerid;
        $toLog['repaired'] = $changed;
        return $toLog;
    }

    public function returnVehicle($vid)
    {
        $vehicle = DB::table('vehicles')->where('id', $vid)->first();
        $changed = ($vehicle->active != 0);
        DB::table('vehicles')->where('id', $vid)->update(['active' => 0]);
        $player = DB::table('players')->where(config('sharedapi.pid'), $vehicle->pid)->first();
        $playerid = $player->uid;

        $toLog['vid'] = $vid;
        $toLog['pid'] = $playerid;
        $toLog['returned'] = $changed;
        return $toLog;
    }

    public function deleteVehicle($vid)
    {
        DB::table('vehicles')->where('id', $vid)->update(['alive' => 0]);
        $vehicle = DB::table('vehicles')->where('id', $vid)->first();
        $player = DB::table('players')->where(config('sharedapi.pid'), $vehicle->pid)->first();
        $playerid = $player->uid;

        $toLog['vid'] = $vid;
        $toLog['pid'] = $playerid;
        return $toLog;
    }

    public function editVehicle(Request $request, $vid)
    {
        $vehicle = DB::table('vehicles')->where('id', $vid)->first();
        $player = DB::table('players')->where(config('sharedapi.pid'), $vehicle->pid)->first();
        $playerid = $player->uid;

        $toLog['vid'] = $vid;
        $toLog['pid'] = $playerid;
        $toLog['fuel']['pre'] = $vehicle->fuel;
        $toLog['fuel']['post'] = $request->fuel;
        $toLog['inventory']['pre'] = $vehicle->inventory;
        $toLog['inventory']['post'] = $request->inventory;
        $toLog['gear']['pre'] = $vehicle->gear;
        $toLog['gear']['post'] = $request->gear;
        /*
        $toLog['color']['pre'] = $vehicle->color;
        $toLog['color']['post'] = $request->color;
        $toLog['damage']['pre'] = $vehicle->damage;
        $toLog['damage']['post'] = $request->damage;
        */

        if ($vehicle->fuel == $request->fuel)
        {
            $toLog['fuel']['changed'] = false;
        } else {
            $toLog['fuel']['changed'] = true;
        }
        if ($vehicle->inventory == $request->inventory)
        {
            $toLog['inventory']['changed'] = false;
        } else {
            $toLog['inventory']['changed'] = true;
        }
        if ($vehicle->gear == $request->gear)
        {
            $toLog['gear']['changed'] = false;
        } else {
            $toLog['gear']['changed'] = true;
        }

        DB::table('vehicles')->where('id', $vid)->update([
            'fuel' => $request->fuel,
            'inventory' => $request->inventory,
            'gear' => $request->gear,
        ]);

        return $toLog;
    }

    public function sideAndGarageChangeVehicle(Request $request, $vid)
    {
        $vehicle = DB::table('vehicles')->where('id', $vid)->first();
        $player = DB::table('players')->where(config('sharedapi.pid'), $vehicle->pid)->first();
        $playerid = $player->uid;

        $toLog['vid'] = $vid;
        $toLog['pid'] = $playerid;
        $toLog['side']['pre'] = $vehicle->side;
        $toLog['side']['post'] = $request->side;
        $toLog['type']['pre'] = $vehicle->type;
        $toLog['type']['post'] = $request->type;

        DB::table('vehicles')->where('id', $vid)->update([
            'side' => $request->side,
            'type' => $request->type
        ]);
        return $toLog;
    }

    public function changeVehicleOwner(Request $request, $vid)
    {
        $vehicle = DB::table('vehicles')->where('id', $vid)->first();
        $player = DB::table('players')->where(config('sharedapi.pid'), $vehicle->pid)->first();
        $preowner = $player->uid;

        $player2 = DB::table('players')->where(config('sharedapi.pid'), $request->newowner)->first();
        $newowner = $player2->uid;

        $toLog['vid'] = $vid;
        $toLog['preowner'] = $preowner;
        $toLog['newowner'] = $newowner;

        DB::table('vehicles')->where('id', $vid)->update([
            'pid' => $request->newowner
        ]);
        return $toLog;
    }
}
