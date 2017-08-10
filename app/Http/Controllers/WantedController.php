<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WantedController extends Controller
{
    function convertLicenseMREStoArray($licensestring)
    {
        if($licensestring == '"[]"' || $licensestring == "any")
        {
            return null;
        }
        $licensestring = str_replace('"[', '', $licensestring);
        $licensestring = str_replace(']"', '', $licensestring);
        $licensestring = str_replace('`', '', $licensestring);
        $licensearray = explode(',', $licensestring);
        $count = 0;
        foreach ($licensearray as $lca)
        {
            $licensearray[$count] = intval($lca);
            $count++;
        }
        return $licensearray;
    }


    public function wantedlist()
    {
        $wanteds = DB::table('wanted')->orderBy('wantedBounty', 'DESC')->get();

        $count = 0;
        foreach ($wanteds as $wanted)
        {
            $output[$count]['pid'] = $wanted->wantedID;
            $output[$count]['name'] = $wanted->wantedName;
            $output[$count]['bounty'] = intval($wanted->wantedBounty);
            $output[$count]['active'] = intval($wanted->active);
            $output[$count]['created_at'] = $wanted->insert_time;
            $output[$count]['crimes'] = $this->convertLicenseMREStoArray($wanted->wantedCrimes);

            $count++;
        }
        return $output;
    }

    public function wantedListForPlayer($pid)
    {
        $wanted = DB::table('wanted')->where('wantedID', $pid)->first();
        $return['error'] = true;
        if (is_null($wanted)) return $return;

        $output['pid'] = $wanted->wantedID;
        $output['name'] = $wanted->wantedName;
        $output['bounty'] = intval($wanted->wantedBounty);
        $output['active'] = intval($wanted->active);
        $output['created_at'] = $wanted->insert_time;
        $output['crimes'] = $this->convertLicenseMREStoArray($wanted->wantedCrimes);
        return $output;
    }

    public function deletePlayerWanted(Request $request)
    {
        $wanted = DB::table('wanted')->where('wantedID', $request->pid)->first();
        $output['pid'] = $request->pid;
        $output['bounty'] = $wanted->wantedBounty;
        $wanted->delete();
    }
}
