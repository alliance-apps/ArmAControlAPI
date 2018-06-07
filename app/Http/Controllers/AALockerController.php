<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AALockerController extends Controller
{
    function convertMREStoArray($licensestring)
    {
        if($licensestring == '"[]"')
        {
            return null;
        }
        if(strpos($licensestring, '"[[') !== false)
        {
            $licensestring = str_replace('`', '', $licensestring);
            $licensestring = str_replace('"[[', '', $licensestring);
            $licensestring = str_replace(']]"', '', $licensestring);
            $licensestring = explode('],[', $licensestring);
            $licensearray = [];
            foreach($licensestring as $license)
            {
                $license = explode(',', $license);
                for ($i = 1; $i <= $license[0]; $i++)
                {
                    array_push($licensearray, $license[1]);
                }
            }
            return $licensearray;
        } else {
            $licensestring = str_replace('"[', '', $licensestring);
            $licensestring = str_replace(']"', '', $licensestring);
            $licensestring = str_replace('`', '', $licensestring);
            $licensearray = explode(',', $licensestring);
            $count = 0;
            foreach ($licensearray as $license)
            {

                $licenses[$count] = $license;
                $count++;
            }
        }
        return $licensearray;
    }



    public function lockerList()
    {
        $gangs = DB::table('locker')->get();
        foreach ($gangs as $gang)
        {
            $output['id'] = $gang->id;
            $output['owner'] = $gang->uid;
            $output['active'] = $gang->active;
            $output['open'] = $gang->open;
            $output['created_at'] = $gang->created_at;
            $output['updated_at'] = $gang->updated_at;
            $output['dbprimary'] = $this->convertMREStoArray($gang->locker_dbprimary);
            $output['dbhandgun'] = $this->convertMREStoArray($gang->locker_dbhandgun);
            $output['dbacc'] = $this->convertMREStoArray($gang->locker_dbattach);
            $output['clothes'] = $this->convertMREStoArray($gang->locker_clothing);
            $output['vests'] = $this->convertMREStoArray($gang->locker_vests);
            $output['backpack'] = $this->convertMREStoArray($gang->locker_backpack);
            $output['misc'] = $this->convertMREStoArray($gang->locker_other);
            $output['items'] = $this->convertMREStoArray($gang->locker_items);
            $output['virtitems'] = $this->convertMREStoArray($gang->locker_virtitems);
            $output['level'] = $gang->level;
            $output['side'] = $gang->side;
        }
        return $output;
    }
    
    public function lockerForPlayer($steamid)
    {
        $output['civ'] = null;
        $output['west'] = null;
        $output['guer'] = null;
        $output['east'] = null;
        $civ = DB::table('locker')->where('uid', $steamid)->where('side', 'CIV')->get();
        foreach($civ as $gang)
        {
            $output['civ']['id'] = $gang->id;
            $output['civ']['owner'] = $gang->uid;
            $output['civ']['active'] = $gang->active;
            $output['civ']['open'] = $gang->open;
            $output['civ']['created_at'] = $gang->created_at;
            $output['civ']['updated_at'] = $gang->updated_at;
            $output['civ']['dbprimary'] = $this->convertMREStoArray($gang->locker_dbprimary);
            $output['civ']['dbhandgun'] = $this->convertMREStoArray($gang->locker_dbhandgun);
            $output['civ']['dbacc'] = $this->convertMREStoArray($gang->locker_dbattach);
            $output['civ']['clothes'] = $this->convertMREStoArray($gang->locker_clothing);
            $output['civ']['vests'] = $this->convertMREStoArray($gang->locker_vests);
            $output['civ']['backpack'] = $this->convertMREStoArray($gang->locker_backpack);
            $output['civ']['misc'] = $this->convertMREStoArray($gang->locker_other);
            $output['civ']['items'] = $this->convertMREStoArray($gang->locker_items);
            $output['civ']['virtitems'] = $this->convertMREStoArray($gang->locker_virtitems);
            $output['civ']['level'] = $gang->level;
            $output['civ']['side'] = $gang->side;
        }
        $civ = DB::table('locker')->where('uid', $steamid)->where('side', 'WEST')->get();
        foreach($civ as $gang)
        {
            $output['west']['id'] = $gang->id;
            $output['west']['owner'] = $gang->uid;
            $output['west']['active'] = $gang->active;
            $output['west']['open'] = $gang->open;
            $output['west']['created_at'] = $gang->created_at;
            $output['west']['updated_at'] = $gang->updated_at;
            $output['west']['dbprimary'] = $this->convertMREStoArray($gang->locker_dbprimary);
            $output['west']['dbhandgun'] = $this->convertMREStoArray($gang->locker_dbhandgun);
            $output['west']['dbacc'] = $this->convertMREStoArray($gang->locker_dbattach);
            $output['west']['clothes'] = $this->convertMREStoArray($gang->locker_clothing);
            $output['west']['vests'] = $this->convertMREStoArray($gang->locker_vests);
            $output['west']['backpack'] = $this->convertMREStoArray($gang->locker_backpack);
            $output['west']['misc'] = $this->convertMREStoArray($gang->locker_other);
            $output['west']['items'] = $this->convertMREStoArray($gang->locker_items);
            $output['west']['virtitems'] = $this->convertMREStoArray($gang->locker_virtitems);
            $output['west']['level'] = $gang->level;
            $output['west']['side'] = $gang->side;
        }
        $civ = DB::table('locker')->where('uid', $steamid)->where('side', 'GUER')->get();
        foreach($civ as $gang)
        {
            $output['guer']['id'] = $gang->id;
            $output['guer']['owner'] = $gang->uid;
            $output['guer']['active'] = $gang->active;
            $output['guer']['open'] = $gang->open;
            $output['guer']['created_at'] = $gang->created_at;
            $output['guer']['updated_at'] = $gang->updated_at;
            $output['guer']['dbprimary'] = $this->convertMREStoArray($gang->locker_dbprimary);
            $output['guer']['dbhandgun'] = $this->convertMREStoArray($gang->locker_dbhandgun);
            $output['guer']['dbacc'] = $this->convertMREStoArray($gang->locker_dbattach);
            $output['guer']['clothes'] = $this->convertMREStoArray($gang->locker_clothing);
            $output['guer']['vests'] = $this->convertMREStoArray($gang->locker_vests);
            $output['guer']['backpack'] = $this->convertMREStoArray($gang->locker_backpack);
            $output['guer']['misc'] = $this->convertMREStoArray($gang->locker_other);
            $output['guer']['items'] = $this->convertMREStoArray($gang->locker_items);
            $output['guer']['virtitems'] = $this->convertMREStoArray($gang->locker_virtitems);
            $output['guer']['level'] = $gang->level;
            $output['guer']['side'] = $gang->side;
        }
        $civ = DB::table('locker')->where('uid', $steamid)->where('side', 'EAST')->get();
        foreach($civ as $gang)
        {
            $output['east']['id'] = $gang->id;
            $output['east']['owner'] = $gang->uid;
            $output['east']['active'] = $gang->active;
            $output['east']['open'] = $gang->open;
            $output['east']['created_at'] = $gang->created_at;
            $output['east']['updated_at'] = $gang->updated_at;
            $output['east']['dbprimary'] = $this->convertMREStoArray($gang->locker_dbprimary);
            $output['east']['dbhandgun'] = $this->convertMREStoArray($gang->locker_dbhandgun);
            $output['east']['dbacc'] = $this->convertMREStoArray($gang->locker_dbattach);
            $output['east']['clothes'] = $this->convertMREStoArray($gang->locker_clothing);
            $output['east']['vests'] = $this->convertMREStoArray($gang->locker_vests);
            $output['east']['backpack'] = $this->convertMREStoArray($gang->locker_backpack);
            $output['east']['misc'] = $this->convertMREStoArray($gang->locker_other);
            $output['east']['items'] = $this->convertMREStoArray($gang->locker_items);
            $output['east']['virtitems'] = $this->convertMREStoArray($gang->locker_virtitems);
            $output['east']['level'] = $gang->level;
            $output['east']['side'] = $gang->side;
        }
        return $output;   
    }
    
    public function lockerUnlock($steamid, Request $request)
    {
        $civ = DB::table('locker')->where('uid', $steamid)->where('side', $request->side)->update(["open" => 0]);
    }
    
    public function admintool()
    {
        $gangs = DB::table('adminlogs')->get();
        return $gangs;
    }
    
    public function lottery()
    {
        $lottery['info'] = DB::table('lottery_info')->get();
        $lottery['tickets'] = DB::table('lottery_tickets')->get();
        return $lottery;
    }

    public function ganglist()
    {
        $gangs = DB::table('gangs')->get();
        return $gangs;
    }

    public function editGang(Request $request, $id)
    {
        $gang = DB::table('locker')->find($id);
        $output['change'] = [];
        if ($gang->owner != $request->owner)
        {
            $output['change']['owner']['old'] = $gang->owner;
            $output['change']['owner']['new'] = $request->owner;
            DB::table('gangs')->where('id', $id)->update(['owner' => $request->owner]);
        }
        if ($gang->name != $request->name)
        {
            $output['change']['name']['old'] = $gang->name;
            $output['change']['name']['new'] = $request->name;
            DB::table('gangs')->where('id', $id)->update(['name' => $request->name]);
        }
        if ($gang->level != $request->level)
        {
            $output['change']['level']['old'] = $gang->level;
            $output['change']['level']['new'] = $request->level;
            DB::table('gangs')->where('id', $id)->update(['level' => $request->level]);
        }
        if ($gang->tag != $request->tag)
        {
            $output['change']['tag']['old'] = $gang->tag;
            $output['change']['tag']['new'] = $request->tag;
            DB::table('gangs')->where('id', $id)->update(['tag' => $request->tag]);
        }
        if ($gang->description != $request->description)
        {
            $output['change']['description']['old'] = $gang->description;
            $output['change']['description']['new'] = $request->description;
            DB::table('gangs')->where('id', $id)->update(['description' => $request->description]);
        }
        if ($gang->bank != $request->bank)
        {
            $output['change']['bank']['old'] = $gang->bank;
            $output['change']['bank']['new'] = $request->bank;
            DB::table('gangs')->where('id', $id)->update(['bank' => $request->bank]);
        }
        return $output;
    }

    public function addRemoveMember(Request $request, $id)
    {
        DB::table('players')->where('uid', $id)->update(['gang_id' => $request->gangid]);
        return ['success' => true];
    }

}
