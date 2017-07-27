<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{

    function isValidMd5($md5 ='') {
        return preg_match('/^[a-f0-9]{32}$/i', $md5);
    }

    function getTake($takereq)
    {
        if(isset($takereq))
        {
            return $takereq;
        }
        else
        {
            return 10;
        }
    }

    function getSkip($skipreq)
    {
        if(isset($skipreq))
        {
            return $skipreq;
        }
        else
        {
            return 0;
        }
    }

    function convertLicenseMREStoArray($licensestring)
    {
        if($licensestring == '"[]"')
        {
            return null;
        }
        $licensestring = str_replace('"[[', '', $licensestring);
        $licensestring = str_replace(']]"', '', $licensestring);
        $licensestring = str_replace('`', '', $licensestring);
        $licensearray = explode('],[', $licensestring);
        $count = 0;
        foreach ($licensearray as $license)
        {

            $licenses[$count] = explode(',', $license);
            $count++;
        }
        return $licenses;
    }

    function threePartMREStoArray($string, $intval)
    {
        if($string == '"[]"')
        {
            return null;
        }
        $string = str_replace('"[', '', $string);
        $string = str_replace(']"', '', $string);
        $arr = explode(',', $string);

        if($intval)
        {
            $count = 0;
            foreach ($arr as $a)
            {
                $arr[$count] = intval($a);
                $count++;
            }
        }
        return $arr;
    }













    public function version()
    {
        return "1.1";
    }

    public function getPlayersLight(Request $request)
    {
        $type = $request->type;
        if($type == "all")
        {
            $players = DB::table('players')->get();
        } elseif($type == "cops")
        {
            $players = DB::table('players')->where('coplevel', '>=', '1')->get();
        } elseif($type == "medics")
        {
            $players = DB::table('players')->where('mediclevel', '>=', '1')->get();
        } elseif($type == "opfors")
        {
            $players = DB::table('players')->where('uid', '<=', '0')->get();
        } elseif($type == "admins")
        {
            $players = DB::table('players')->where('adminlevel', '>=', '1')->get();
        } elseif($type == "donors")
        {
            $players = DB::table('players')->where('donorlevel', '>=', '1')->get();
        } elseif($type == "top10money")
        {
            $players = DB::table('players')->orderBy('bankacc', 'DESC')->take(10)->get();
        }




        $output = [];
        $count = 0;
        foreach ($players as $player)
        {
            $output[$count]['uid'] = $player->uid;
            $output[$count]['name'] = $player->name;
            $output[$count]['aliases'] = str_replace('`]"', '',str_replace('"[`', '', $player->aliases));
            $output[$count]['pid'] = $player->pid;
            $output[$count]['cash'] = $player->cash;
            $output[$count]['bank'] = $player->bankacc;
            $output[$count]['coplevel'] = intval($player->coplevel);
            $output[$count]['mediclevel'] = intval($player->mediclevel);
            $output[$count]['adminlevel'] = intval($player->adminlevel);
            $output[$count]['donorlevel'] = intval($player->donorlevel);
            $output[$count]['opforlevel']['enabled'] = false;
            $output[$count]['arrested'] = intval($player->arrested);
            $output[$count]['playtime']['enabled'] = true;
            $playtime = str_replace('"[', '', $player->playtime);
            $playtime = str_replace(']"', '', $playtime);
            $playtime = explode(',', $playtime);
            $output[$count]['playtime']['civ'] = intval($playtime[2]);
            $output[$count]['playtime']['cop'] = intval($playtime[0]);
            $output[$count]['playtime']['med'] = intval($playtime[1]);
            $output[$count]['insert_time'] = $player->insert_time;
            $output[$count]['last_seen'] = $player->last_seen;
            $count++;
        }
        return $output;
    }

    public function getPlayersComplete()
    {
        $players = DB::table('players')->get();



        $count = 0;
        foreach ($players as $player)
        {
            $output[$count]['uid'] = $player->uid;
            $output[$count]['name'] = $player->name;
            $output[$count]['aliases'] = str_replace('`]"', '',str_replace('"[`', '', $player->aliases));
            $output[$count]['pid'] = $player->pid;
            $output[$count]['cash'] = $player->cash;
            $output[$count]['bank'] = $player->bankacc;
            $output[$count]['coplevel'] = intval($player->coplevel);
            $output[$count]['mediclevel'] = intval($player->mediclevel);
            $output[$count]['adminlevel'] = intval($player->adminlevel);
            $output[$count]['donorlevel'] = intval($player->donorlevel);
            $output[$count]['opforlevel']['enabled'] = false;
            $output[$count]['arrested'] = intval($player->arrested);
            $output[$count]['blacklist'] = intval($player->blacklist);
            $output[$count]['civ_alive'] = intval($player->civ_alive);
            $output[$count]['playtime']['enabled'] = true;
            $playtime = str_replace('"[', '', $player->playtime);
            $playtime = str_replace(']"', '', $playtime);
            $playtime = explode(',', $playtime);
            $output[$count]['playtime']['civ'] = intval($playtime[2]);
            $output[$count]['playtime']['cop'] = intval($playtime[0]);
            $output[$count]['playtime']['med'] = intval($playtime[1]);
            $output[$count]['civ_licenses'] = $this->convertLicenseMREStoArray($player->civ_licenses);
            $output[$count]['cop_licenses'] = $this->convertLicenseMREStoArray($player->cop_licenses);
            $output[$count]['med_licenses'] = $this->convertLicenseMREStoArray($player->med_licenses);
            $output[$count]['civ_gear'] = $player->civ_gear;
            $output[$count]['cop_gear'] = $player->cop_gear;
            $output[$count]['med_gear'] = $player->med_gear;
            $output[$count]['stats']['enabled'] = true;
            $output[$count]['stats']['civ'] = $this->threePartMREStoArray($player->civ_stats, true);
            $output[$count]['stats']['cop'] = $this->threePartMREStoArray($player->cop_stats, true);
            $output[$count]['stats']['med'] = $this->threePartMREStoArray($player->med_stats, true);
            $output[$count]['pos']['enabled'] = true;
            $output[$count]['pos']['civ'] = $this->threePartMREStoArray($player->civ_position, false);
            $output[$count]['insert_time'] = $player->insert_time;
            $output[$count]['last_seen'] = $player->last_seen;
            $count++;
        }
        return $output;
    }








    public function getPlayer(Request $request, $uid)
    {
        if(strlen($uid) == 17 && ctype_digit($uid))
        {
            $player = DB::table('players')->where('pid', $uid)->take(1)->get();
        }
        else {
            $player = DB::table('players')->where('uid', $uid)->take(1)->get();
        }

        if(is_null($player))
        {
            die('ss');
        }

        $output = [[]];

        $count = 0;
        foreach ($player as $player)
        {
            $output[$count]['uid'] = $player->uid;
            $output[$count]['name'] = $player->name;
            $output[$count]['aliases'] = str_replace('`]"', '',str_replace('"[`', '', $player->aliases));
            $output[$count]['pid'] = $player->pid;
            $output[$count]['cash'] = $player->cash;
            $output[$count]['bank'] = $player->bankacc;
            $output[$count]['coplevel'] = intval($player->coplevel);
            $output[$count]['mediclevel'] = intval($player->mediclevel);
            $output[$count]['adminlevel'] = intval($player->adminlevel);
            $output[$count]['donorlevel'] = intval($player->donorlevel);
            $output[$count]['opforlevel'] = 0;
            $output[$count]['arrested'] = intval($player->arrested);
            $output[$count]['blacklist'] = intval($player->blacklist);
            $output[$count]['civ_alive'] = intval($player->civ_alive);
            $output[$count]['playtime']['enabled'] = true;
            $playtime = str_replace('"[', '', $player->playtime);
            $playtime = str_replace(']"', '', $playtime);
            $playtime = explode(',', $playtime);
            $output[$count]['playtime']['civ'] = intval($playtime[2]);
            $output[$count]['playtime']['cop'] = intval($playtime[0]);
            $output[$count]['playtime']['med'] = intval($playtime[1]);
            $output[$count]['civ_licenses'] = $this->convertLicenseMREStoArray($player->civ_licenses);
            $output[$count]['cop_licenses'] = $this->convertLicenseMREStoArray($player->cop_licenses);
            $output[$count]['med_licenses'] = $this->convertLicenseMREStoArray($player->med_licenses);
            $output[$count]['opfor_licenses'] = null;
            $output[$count]['civ_licenses_string'] = $player->civ_licenses;
            $output[$count]['cop_licenses_string'] = $player->cop_licenses;
            $output[$count]['med_licenses_string'] = $player->med_licenses;
            $output[$count]['opfor_licenses_string'] = null;
            $output[$count]['newgear'] = env('NEW_GEAR', false);
            $output[$count]['civ_gear'] = $player->civ_gear;
            $output[$count]['cop_gear'] = $player->cop_gear;
            $output[$count]['med_gear'] = $player->med_gear;
            $output[$count]['opfor_gear'] = '"[]"';
            $output[$count]['stats']['enabled'] = true;
            $output[$count]['stats']['civ'] = $this->threePartMREStoArray($player->civ_stats, true);
            $output[$count]['stats']['cop'] = $this->threePartMREStoArray($player->cop_stats, true);
            $output[$count]['stats']['med'] = $this->threePartMREStoArray($player->med_stats, true);
            $output[$count]['pos']['enabled'] = true;
            $output[$count]['pos']['civ'] = $this->threePartMREStoArray($player->civ_position, false);
            $output[$count]['insert_time'] = $player->insert_time;
            $output[$count]['last_seen'] = $player->last_seen;
            $count++;
        }
        return $output[0];
    }

    public function getMoneySum()
    {
        $bank = $players = DB::table('players')->sum('bankacc');
        $cash = $players = DB::table('players')->sum('cash');
        return ($bank + $cash);
    }

    public function getPossibleLevels()
    {
        $type = DB::select("SHOW COLUMNS FROM players WHERE Field = 'coplevel'")[0]->Type;
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        $type = explode("','", $matches[1]);
        $return['cop'] = intval(end($type));

        $type = DB::select("SHOW COLUMNS FROM players WHERE Field = 'mediclevel'")[0]->Type;
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        $type = explode("','", $matches[1]);
        $return['med'] = intval(end($type));

        $type = DB::select("SHOW COLUMNS FROM players WHERE Field = 'adminlevel'")[0]->Type;
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        $type = explode("','", $matches[1]);
        $return['admin'] = intval(end($type));

        $type = DB::select("SHOW COLUMNS FROM players WHERE Field = 'donorlevel'")[0]->Type;
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        $type = explode("','", $matches[1]);
        $return['donor'] = intval(end($type));

        $return['opfor'] = -1;

        return $return;
    }


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
