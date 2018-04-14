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

}
