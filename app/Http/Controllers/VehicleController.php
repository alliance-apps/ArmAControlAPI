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

    public function listForPlayer($id)
    {
        $player = DB::table('players')->where('uid', $id)->first();
        $playerid = $player->pid;
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
}
