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
            $output['updated_at'] = $gang->locker_timechange;
            $output['dbprimary'] = $this->convertMREStoArray($gang->locker_dbprimaer);
            $output['dbhandgun'] = $this->convertMREStoArray($gang->locker_dbpistole);
            $output['dbacc'] = $this->convertMREStoArray($gang->locker_dbaufsaetze);
            $output['clothes'] = $this->convertMREStoArray($gang->locker_kleidung);
            $output['vests'] = $this->convertMREStoArray($gang->locker_westen);
            $output['backpack'] = $this->convertMREStoArray($gang->locker_backpack);
            $output['misc'] = $this->convertMREStoArray($gang->locker_sonstiges);
            $output['items'] = $this->convertMREStoArray($gang->locker_items);
            $output['virtitems'] = $this->convertMREStoArray($gang->locker_virtitems);
        }
        return $output;
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
