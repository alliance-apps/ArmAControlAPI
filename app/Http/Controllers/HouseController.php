<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HouseController extends Controller
{
    public function houselist()
    {
        $gangs = DB::table('houses')->orderBy('id')->get();

        $count = 0;
        $output = [];
        foreach ($gangs as $gang)
        {
            $output[$count]['id'] = $gang->id;
            $output[$count]['pid'] = $gang->pid;
            $output[$count]['owned'] = intval($gang->owned);
            $output[$count]['garage'] = intval($gang->garage);
            $output[$count]['created_at'] = $gang->insert_time;

            $pos = $gang->pos;
            $pos = str_replace('[', '', $pos);
            $pos = str_replace(']', '', $pos);
            $pos = explode(',', $pos);
            $output[$count]['pos'] = $pos;

            $count++;
        }
        return $output;
    }

    public function houselistForPlayer($pid)
    {
        $gangs = DB::table('houses')->where('pid', $pid)->orderBy('id')->get();
        $return['error'] = true;
        $output = [];
        $count = 0;
        foreach ($gangs as $gang)
        {
            $return['error'] = false;
            $output[$count]['id'] = $gang->id;
            $output[$count]['pid'] = $gang->pid;
            $output[$count]['owned'] = intval($gang->owned);
            $output[$count]['garage'] = intval($gang->garage);
            $output[$count]['created_at'] = $gang->insert_time;

            $pos = $gang->pos;
            $pos = str_replace('[', '', $pos);
            $pos = str_replace(']', '', $pos);
            $pos = explode(',', $pos);
            $output[$count]['pos'] = $pos;

            $count++;
        }
        if ($return['error'])
        {
            return $return;
        } else {
            return $output;
        }

    }

    public function house($id)
    {
        $output = [];
        $house = DB::table('houses')->where('id', $id)->first();
        $return['error'] = true;
        if (is_null($house)) return $return;
        $output['id'] = $house->id;
        $output['pid'] = $house->pid;
        $output['owned'] = intval($house->owned);
        $output['garage'] = intval($house->garage);
        $output['created_at'] = $house->insert_time;
        $pos = $house->pos;
        $pos = str_replace('[', '', $pos);
        $pos = str_replace(']', '', $pos);
        $pos = explode(',', $pos);
        $output['pos'] = $pos;
        $containers = DB::table('containers')->where('pid', $house->pid)->get();
        $housecontainers = [];
        foreach ($containers as $container)
        {
            $curpos = $container->pos;
            $curpos = str_replace('[', '', $curpos);
            $curpos = str_replace(']', '', $curpos);
            $curpos = explode(',', $curpos);
            $x = intval($output['pos'][0]) - intval($curpos[0]);
            $y = intval($output['pos'][1]) - intval($curpos[1]);
            $z = intval($output['pos'][2]) - intval($curpos[2]);
            $distance = sqrt($x * $x + $y * $y + $z * $z);
            $distance = round($distance, 1);
            if ($distance < 20)
            {
                array_push($housecontainers, $container->id);
            }
        }
        $output['containers'] = null;
        $count = 0;
        foreach ($housecontainers as $housecontainer)
        {
            $container = DB::table('containers')->find($housecontainer);
            $output['containers'][$count]['id'] = $container->id;
            $output['containers'][$count]['pid'] = $container->pid;
            $output['containers'][$count]['classname'] = $container->classname;
            $pos = $container->pos;
            $pos = str_replace('[', '', $pos);
            $pos = str_replace(']', '', $pos);
            $pos = explode(',', $pos);
            $output['containers'][$count]['pos'] = $pos;
            $output['containers'][$count]['inventory'] = $container->inventory;
            $output['containers'][$count]['gear'] = $container->gear;

            $dir = $container->dir;
            $dir = str_replace('[', '', $dir);
            $dir = str_replace(']', '', $dir);
            $dir = explode(',', $dir);

            $output['containers'][$count]['dir'] = $dir;
            $output['containers'][$count]['owned'] = intval($container->owned);
            $output['containers'][$count]['active'] = intval($container->active);
            $output['containers'][$count]['created_at'] = $container->insert_time;
        }








        return $output;
    }

}
