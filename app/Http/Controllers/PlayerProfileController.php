<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerProfileController extends Controller
{
    /**
     * Changes gear in DB if changed and returns changed values
     * Expects all 4 gear params and the DB UID
     */
    public function editPlayerGear(Request $request, $uid)
    {
        if(is_null($request->civ_gear) || is_null($request->cop_gear) || is_null($request->med_gear) || is_null($request->opfor_gear))
        {
            die('false');
        }

        $players = DB::table('players')->where('uid', $uid)->take(1)->get();
        foreach ($players as $p)
        {
            $player = $p;
        }
        $Gear['civ']['pre'] = $player->civ_gear;
        $Gear['cop']['pre'] = $player->cop_gear;
        $Gear['med']['pre'] = $player->med_gear;
        $Gear['opfor']['pre'] = '"[]"';
        $Gear['civ']['post'] = $request->civ_gear;
        $Gear['cop']['post'] = $request->cop_gear;
        $Gear['med']['post'] = $request->med_gear;
        $Gear['opfor']['post'] = $request->opfor_gear;

        if($Gear['civ']['pre'] == $Gear['civ']['post'])
        {
            $Gear['civ']['changed'] = false;
            unset($Gear['civ']['pre']);
            unset($Gear['civ']['post']);
        } else {
            $Gear['civ']['changed'] = true;
            $players = DB::table('players')->where('uid', $uid)->update(['civ_gear' => $Gear['civ']['post']]);
        }
        if($Gear['cop']['pre'] == $Gear['cop']['post'])
        {
            $Gear['cop']['changed'] = false;
            unset($Gear['cop']['pre']);
            unset($Gear['cop']['post']);
        } else {
            $Gear['cop']['changed'] = true;
            $players = DB::table('players')->where('uid', $uid)->update(['cop_gear' => $Gear['cop']['post']]);
        }
        if($Gear['med']['pre'] == $Gear['med']['post'])
        {
            $Gear['med']['changed'] = false;
            unset($Gear['med']['pre']);
            unset($Gear['med']['post']);
        } else {
            $Gear['med']['changed'] = true;
            $players = DB::table('players')->where('uid', $uid)->update(['med_gear' => $Gear['med']['post']]);
        }
        if($Gear['opfor']['pre'] == $Gear['opfor']['post'])
        {
            $Gear['opfor']['changed'] = false;
            unset($Gear['opfor']['pre']);
            unset($Gear['opfor']['post']);
        } else {
            $Gear['opfor']['changed'] = true;
            //TODO: Well, Opfor isn't implemented. Do it here!
        }
        return $Gear;
    }

    public function editPlayerLevel(Request $request, $uid)
    {
        $players = DB::table('players')->where('uid', $uid)->take(1)->get();
        foreach ($players as $p)
        {
            $player = $p;
        }
        $level['cop']['pre'] = $player->coplevel;
        $level['med']['pre'] = $player->mediclevel;
        $level['opfor']['pre'] = 0;
        $level['admin']['pre'] = $player->adminlevel;
        $level['donor']['pre'] = $player->donorlevel;
        $level['blacklist']['pre'] = $player->blacklist;
        $level['arrested']['pre'] = $player->arrested;

        if(isset($request->cop))
        {
            if($request->cop == $level['cop']['pre'])
            {
                $level['cop']['changed'] = false;
                unset($level['cop']['pre']);
            } else {
                $level['cop']['post'] = $request->cop;
                $level['cop']['changed'] = true;
                DB::table('players')->where('uid', $uid)->update(['coplevel' => $level['cop']['post']]);
            }
        } else {
            $level['cop']['changed'] = false;
            unset($level['cop']['pre']);
        }
        if(isset($request->med))
        {
            if($request->med == $level['med']['pre'])
            {
                $level['med']['changed'] = false;
                unset($level['med']['pre']);
            } else {
                $level['med']['post'] = $request->med;
                $level['med']['changed'] = true;
                DB::table('players')->where('uid', $uid)->update(['mediclevel' => $level['med']['post']]);
            }
        } else {
            $level['med']['changed'] = false;
            unset($level['med']['pre']);
        }
        if(isset($request->opfor))
        {
            if($request->opfor == $level['opfor']['pre'])
            {
                $level['opfor']['changed'] = false;
                unset($level['opfor']['pre']);
            } else {
                $level['opfor']['post'] = $request->opfor;
                $level['opfor']['changed'] = false;
            }
        } else {
            $level['opfor']['changed'] = false;
            unset($level['opfor']['pre']);
        }
        if(isset($request->admin))
        {
            if($request->admin == $level['admin']['pre'])
            {
                $level['admin']['changed'] = false;
                unset($level['admin']['pre']);
            } else {
                $level['admin']['post'] = $request->admin;
                $level['admin']['changed'] = true;
                DB::table('players')->where('uid', $uid)->update(['adminlevel' => $level['admin']['post']]);
            }
        } else {
            $level['admin']['changed'] = false;
            unset($level['admin']['pre']);
        }
        if(isset($request->donor))
        {
            if($request->donor == $level['donor']['pre'])
            {
                $level['donor']['changed'] = false;
                unset($level['donor']['pre']);
            } else {
                $level['donor']['post'] = $request->donor;
                $level['donor']['changed'] = true;
                DB::table('players')->where('uid', $uid)->update(['donorlevel' => $level['donor']['post']]);
            }
        } else {
            $level['donor']['changed'] = false;
            unset($level['donor']['pre']);
        }
        if(isset($request->blacklist))
        {
            if($request->blacklist == $level['blacklist']['pre'])
            {
                $level['blacklist']['changed'] = false;
                unset($level['blacklist']['pre']);
            } else {
                $level['blacklist']['post'] = $request->blacklist;
                $level['blacklist']['changed'] = true;
                DB::table('players')->where('uid', $uid)->update(['blacklist' => $level['blacklist']['post']]);
            }
        } else {
            $level['blacklist']['changed'] = false;
            unset($level['blacklist']['pre']);
        }
        if(isset($request->arrested))
        {
            if($request->arrested == $level['arrested']['pre'])
            {
                $level['arrested']['changed'] = false;
                unset($level['arrested']['pre']);
            } else {
                $level['arrested']['post'] = $request->arrested;
                $level['arrested']['changed'] = true;
                DB::table('players')->where('uid', $uid)->update(['arrested' => $level['arrested']['post']]);
            }
        } else {
            $level['arrested']['changed'] = false;
            unset($level['arrested']['pre']);
        }
        return $level;
    }

    public function editPlayerLicenses(Request $request, $uid)
    {
        $players = DB::table('players')->where('uid', $uid)->take(1)->get();
        foreach ($players as $p)
        {
            $player = $p;
        }
        if (isset($request->civ))
        {
            DB::table('players')->where('uid', $uid)->update(['civ_licenses' => $request->civ]);
        }
        if (isset($request->cop))
        {
            DB::table('players')->where('uid', $uid)->update(['cop_licenses' => $request->cop]);
        }
        if (isset($request->med))
        {
            DB::table('players')->where('uid', $uid)->update(['med_licenses' => $request->med]);
        }
        if (isset($request->opfor))
        {
            //TODO: Opfor if needed
        }
    }

    public function editPlayerMoney(Request $request, $uid)
    {
        $players = DB::table('players')->where('uid', $uid)->take(1)->get();
        foreach ($players as $p)
        {
            $player = $p;
            $preBank = $p->bankacc;
            $preCash = $p->cash;
        }
        DB::table('players')->where('uid', $uid)->update(['bankacc' => $request->bank, 'cash' => $request->cash]);

        $toLog['bank']['pre'] = $preBank;
        $toLog['bank']['post'] = intval($request->bank);
        $toLog['bank']['change'] = $request->bank - $preBank;
        $toLog['cash']['pre'] = $preCash;
        $toLog['cash']['post'] = intval($request->cash);
        $toLog['cash']['change'] = $request->cash - $preCash;
        return $toLog;

    }

}
