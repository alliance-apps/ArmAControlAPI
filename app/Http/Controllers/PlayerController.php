<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
        return env('VERSION', "1.2");
    }

    public function getPlayersLight(Request $request)
    {
        $type = $request->type;
        if (is_null($type)) $type = "all";
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
            $pid = env('TABLE_PLAYERS_PID', 'pid');
            $output[$count]['pid'] = $player->$pid;
            $output[$count]['cash'] = $player->cash;
            $output[$count]['bank'] = $player->bankacc;
            $output[$count]['coplevel'] = intval($player->coplevel);
            $output[$count]['mediclevel'] = intval($player->mediclevel);
            $output[$count]['adminlevel'] = intval($player->adminlevel);
            $output[$count]['donorlevel'] = intval($player->donorlevel);
            $output[$count]['opforlevel']['enabled'] = env('TABLE_PLAYERS_OPFOR_ENABLED', false);
            if (env('TABLE_PLAYERS_OPFOR_ENABLED', false))
            {
                $opfor = env('TABLE_PLAYERS_OPFOR');
                $output[$count]['opforlevel'] = intval($player->$opfor);
            }
            $output[$count]['arrested'] = intval($player->arrested);
            $output[$count]['playtime']['enabled'] = env('TABLE_PLAYERS_PLAYTIME_ENABLED', true);
            if (env('TABLE_PLAYERS_PLAYTIME_ENABLED', true))
            {
                $playtime = str_replace('"[', '', $player->playtime);
                $playtime = str_replace(']"', '', $playtime);
                $playtime = explode(',', $playtime);
                $output[$count]['playtime']['civ'] = intval($playtime[2]);
                $output[$count]['playtime']['cop'] = intval($playtime[0]);
                $output[$count]['playtime']['med'] = intval($playtime[1]);
            }
            if (env('TABLE_PLAYERS_TIMESTAMPS', true))
            {
                $output[$count]['insert_time'] = $player->insert_time;
                $output[$count]['last_seen'] = $player->last_seen;
            } else {
                $output[$count]['insert_time'] = '0000-00-00 00:00:00';
                $output[$count]['last_seen'] = '0000-00-00 00:00:00';
            }
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
            $pid = env('TABLE_PLAYERS_PID', 'pid');
            $output[$count]['pid'] = $player->$pid;
            $output[$count]['cash'] = $player->cash;
            $output[$count]['bank'] = $player->bankacc;
            $output[$count]['coplevel'] = intval($player->coplevel);
            $output[$count]['mediclevel'] = intval($player->mediclevel);
            $output[$count]['adminlevel'] = intval($player->adminlevel);
            $output[$count]['donorlevel'] = intval($player->donorlevel);
            $output[$count]['opforlevel']['enabled'] = env('TABLE_PLAYERS_OPFOR_ENABLED', false);
            if (env('TABLE_PLAYERS_OPFOR_ENABLED', false))
            {
                $opfor = env('TABLE_PLAYERS_OPFOR');
                $output[$count]['opforlevel'] = intval($player->$opfor);
            }
            $output[$count]['arrested'] = intval($player->arrested);
            $output[$count]['blacklist'] = intval($player->blacklist);
            $output[$count]['civ_alive'] = intval($player->civ_alive);
            $output[$count]['playtime']['enabled'] = env('TABLE_PLAYERS_PLAYTIME_ENABLED', true);
            if (env('TABLE_PLAYERS_PLAYTIME_ENABLED', true))
            {
                $playtime = str_replace('"[', '', $player->playtime);
                $playtime = str_replace(']"', '', $playtime);
                $playtime = explode(',', $playtime);
                $output[$count]['playtime']['civ'] = intval($playtime[2]);
                $output[$count]['playtime']['cop'] = intval($playtime[0]);
                $output[$count]['playtime']['med'] = intval($playtime[1]);
            }
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
            if (env('TABLE_PLAYERS_TIMESTAMPS', true))
            {
                $output[$count]['insert_time'] = $player->insert_time;
                $output[$count]['last_seen'] = $player->last_seen;
            } else {
                $output[$count]['insert_time'] = '0000-00-00 00:00:00';
                $output[$count]['last_seen'] = '0000-00-00 00:00:00';
            }
            $count++;
        }
        return $output;
    }








    public function getPlayer(Request $request, $uid)
    {
        if(strlen($uid) == 17 && ctype_digit($uid))
        {
            $player = DB::table('players')->where(env('TABLE_PLAYERS_PID', 'pid'), $uid)->take(1)->get();
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
            $pid = env('TABLE_PLAYERS_PID', 'pid');
            $output[$count]['pid'] = $player->$pid;


            $output[$count]['cash'] = $player->cash;
            $output[$count]['bank'] = $player->bankacc;
            $cop_cash = env('TABLE_PLAYERS_SEPCASH_COPCASH', 'cash');
            $cop_bank = env('TABLE_PLAYERS_SEPCASH_COPBANK', 'bankacc');
            $med_cash = env('TABLE_PLAYERS_SEPCASH_MEDCASH', 'cash');
            $med_bank = env('TABLE_PLAYERS_SEPCASH_MEDBANK', 'bankacc');
            $opfor_cash = env('TABLE_PLAYERS_SEPCASH_OPFORCASH', 'cash');
            $opfor_bank = env('TABLE_PLAYERS_SEPCASH_OPFORBANK', 'bankacc');
            $output[$count]['sepcash'] = env('TABLE_PLAYERS_SEPCASH', false);
            $output[$count]['cop_cash'] = $player->$cop_cash;
            $output[$count]['cop_bank'] = $player->$cop_bank;
            $output[$count]['med_cash'] = $player->$med_cash;
            $output[$count]['med_bank'] = $player->$med_bank;
            $output[$count]['opfor_cash'] = $player->$opfor_cash;
            $output[$count]['opfor_bank'] = $player->$opfor_bank;

            $output[$count]['coplevel'] = intval($player->coplevel);
            $output[$count]['mediclevel'] = intval($player->mediclevel);
            $output[$count]['adminlevel'] = intval($player->adminlevel);
            $output[$count]['donorlevel'] = intval($player->donorlevel);
            $output[$count]['extralevel1_enabled'] = env('TABLE_PLAYERS_EXTRALEVEL_1', false);
            if (env('TABLE_PLAYERS_EXTRALEVEL_1', false))
            {
                $el1 = env('TABLE_PLAYERS_EXTRALEVEL_1_column');
                $output[$count]['extralevel1'] = intval($player->$el1);
            }
            $output[$count]['extralevel2_enabled'] = env('TABLE_PLAYERS_EXTRALEVEL_2', false);
            if (env('TABLE_PLAYERS_EXTRALEVEL_2', false))
            {
                $el2 = env('TABLE_PLAYERS_EXTRALEVEL_2_column');
                $output[$count]['extralevel2'] = intval($player->$el2);
            }

            $output[$count]['opforlevel']['enabled'] = env('TABLE_PLAYERS_OPFOR_ENABLED', false);
            $output[$count]['opforlevel'] = 0;
            if (env('TABLE_PLAYERS_OPFOR_ENABLED', false))
            {
                $opfor = env('TABLE_PLAYERS_OPFOR');
                $output[$count]['opforlevel'] = intval($player->$opfor);
            }
            $output[$count]['arrested'] = intval($player->arrested);
            $output[$count]['blacklist'] = intval($player->blacklist);
            $output[$count]['civ_alive'] = intval($player->civ_alive);
            $output[$count]['playtime']['enabled'] = env('TABLE_PLAYERS_PLAYTIME_ENABLED', true);
            if (env('TABLE_PLAYERS_PLAYTIME_ENABLED', true))
            {
                $playtime = str_replace('"[', '', $player->playtime);
                $playtime = str_replace(']"', '', $playtime);
                $playtime = explode(',', $playtime);
                $output[$count]['playtime']['civ'] = intval($playtime[2]);
                $output[$count]['playtime']['cop'] = intval($playtime[0]);
                $output[$count]['playtime']['med'] = intval($playtime[1]);
            }
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
            if (env('TABLE_PLAYERS_OPFOR_ENABLED', false))
            {
                $opfg = env('TABLE_PLAYERS_OPFOR_GEAR');
                $output[$count]['opfor_gear'] = $player->$opfg;
            }
            $output[$count]['stats']['enabled'] = true;
            $output[$count]['stats']['civ'] = $this->threePartMREStoArray($player->civ_stats, true);
            $output[$count]['stats']['cop'] = $this->threePartMREStoArray($player->cop_stats, true);
            $output[$count]['stats']['med'] = $this->threePartMREStoArray($player->med_stats, true);
            $output[$count]['pos']['enabled'] = true;
            $output[$count]['pos']['civ'] = $this->threePartMREStoArray($player->civ_position, false);
            if (env('TABLE_PLAYERS_TIMESTAMPS', true))
            {
                $output[$count]['insert_time'] = $player->insert_time;
                $output[$count]['last_seen'] = $player->last_seen;
            } else {
                $output[$count]['insert_time'] = '0000-00-00 00:00:00';
                $output[$count]['last_seen'] = '0000-00-00 00:00:00';
            }
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


    public function getDashboardStats()
    {
        $start = microtime(true);
        $bank = $players = DB::table('players')->sum('bankacc');
        $cash = $players = DB::table('players')->sum('cash');
        $output['money'] = $bank + $cash;
        $output['players'] = DB::table('players')->count();
        $output['cops'] = DB::table('players')->where('coplevel', '>=', 1)->count();
        $output['last7days'] = DB::table('players')->where('insert_time', '>=', Carbon::now()->subWeek())->get()->count();
        $output['last24hours'] = DB::table('players')->where('insert_time', '>=', Carbon::now()->subDay())->get()->count();
        $output['activelast48hours'] = DB::table('players')->where('last_seen', '>=', Carbon::now()->subDays(2))->get()->count();
        $output['activelast4hours'] = DB::table('players')->where('last_seen', '>=', Carbon::now()->subHours(4))->get()->count();
        $output['vehicles'] = DB::table('vehicles')->count();
        $output['vehicles_civ'] = DB::table('vehicles')->where('side', 'civ')->get()->count();
        $output['vehicles_cop'] = DB::table('vehicles')->where('side', 'cop')->get()->count();
        $output['vehicles_med'] = DB::table('vehicles')->where('side', 'med')->get()->count();
        $output['vehicles_active'] = DB::table('vehicles')->where('active', 1)->get()->count();
        $output['vehicles_alive'] = DB::table('vehicles')->where('alive', 1)->get()->count();
        $output['vehicles_last24hours'] = DB::table('vehicles')->where('insert_time', '>=', Carbon::now()->subDay())->get()->count();
        $output['vehicles_last7days'] = DB::table('vehicles')->where('insert_time', '>=', Carbon::now()->subWeek())->get()->count();
        $output['houses'] = DB::table('houses')->count();
        $output['gangs'] = DB::table('gangs')->count();
        $output['containers'] = DB::table('containers')->count();
        $output['totalBounty'] = intval(DB::table('wanted')->sum('wantedBounty'));
        $output['time'] = round((microtime(true) - $start) * 1000);

        return $output;

    }
    public function getlast30days() {
        $output = [];
        for ($i = 0; $i <= 30; $i++) {
            $datestring = Carbon::now()->subdays($i)->toDateString();
            $val = DB::table('players')->where(DB::raw('date(insert_time)'), $datestring)->get()->count();
            $val1 = DB::table('players')->where(DB::raw('date(insert_time)'), Carbon::now()->subdays($i)->toDateString())->where(DB::raw('date(last_seen)'),'>', DB::raw('date(insert_time)'))->get()->count();
            $output[$i][1] = $val;
            $output[$i][0] = $val1;
            $output[$i][2] = $datestring;
        }
        return $output;
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
        if (env('TABLE_PLAYERS_OPFOR_ENABLED', false))
        {
            $type = DB::select("SHOW COLUMNS FROM players WHERE Field = '".env('TABLE_PLAYERS_OPFOR')."'")[0]->Type;
            preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
            $type = explode("','", $matches[1]);
            $return['opfor'] = intval(end($type));
        }

        $return['extralevel1'] = -1;
        if (env('TABLE_PLAYERS_EXTRALEVEL_1', false))
        {
            $type = DB::select("SHOW COLUMNS FROM players WHERE Field = '".env('TABLE_PLAYERS_EXTRALEVEL_1_column')."'")[0]->Type;
            preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
            $type = explode("','", $matches[1]);
            $return['extralevel1'] = intval(end($type));
        }

        $return['extralevel2'] = -1;
        if (env('TABLE_PLAYERS_EXTRALEVEL_2', false))
        {
            $type = DB::select("SHOW COLUMNS FROM players WHERE Field = '".env('TABLE_PLAYERS_EXTRALEVEL_2_column')."'")[0]->Type;
            preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
            $type = explode("','", $matches[1]);
            $return['extralevel2'] = intval(end($type));
        }


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
        if (env('TABLE_PLAYERS_OPFOR_ENABLED', false))
        {
            $opfg = env('TABLE_PLAYERS_OPFOR_GEAR');
            $Gear['opfor']['pre'] = $player->$opfg;
        }

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
        $Gear['opfor']['changed'] = false;
        if (env('TABLE_PLAYERS_OPFOR_ENABLED', false))
        {
            if($Gear['opfor']['pre'] == $Gear['opfor']['post'])
            {
                $Gear['opfor']['changed'] = false;
                unset($Gear['opfor']['pre']);
                unset($Gear['opfor']['post']);
            } else {
                $Gear['opfor']['changed'] = true;
                $players = DB::table('players')->where('uid', $uid)->update([env('TABLE_PLAYERS_OPFOR_GEAR') => $Gear['opfor']['post']]);
            }
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
        if (env('TABLE_PLAYERS_OPFOR_ENABLED', false))
        {
            $opfor = env('TABLE_PLAYERS_OPFOR');
            $level['opfor']['pre'] = $player->$opfor;
        }

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
                if (env('TABLE_PLAYERS_OPFOR_ENABLED', false))
                {
                    $level['opfor']['post'] = $request->opfor;
                    $level['opfor']['changed'] = true;
                    DB::table('players')->where('uid', $uid)->update([env('TABLE_PLAYERS_OPFOR') => $level['opfor']['post']]);
                } else {
                    $level['opfor']['post'] = $request->opfor;
                    $level['opfor']['changed'] = false;
                }
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
            if (env('TABLE_PLAYERS_SEPCASH', false))
            {
                $cop_cash = env('TABLE_PLAYERS_SEPCASH_COPCASH', 'cash');
                $cop_bank = env('TABLE_PLAYERS_SEPCASH_COPBANK', 'bankacc');
                $med_cash = env('TABLE_PLAYERS_SEPCASH_MEDCASH', 'cash');
                $med_bank = env('TABLE_PLAYERS_SEPCASH_MEDBANK', 'bankacc');
                $opfor_cash = env('TABLE_PLAYERS_SEPCASH_OPFORCASH', 'cash');
                $opfor_bank = env('TABLE_PLAYERS_SEPCASH_OPFORBANK', 'bankacc');
                $output['cop_cash'] = $p->$cop_cash;
                $output['cop_bank'] = $p->$cop_bank;
                $output['med_cash'] = $p->$med_cash;
                $output['med_bank'] = $p->$med_bank;
                $output['opfor_cash'] = $p->$opfor_cash;
                $output['opfor_bank'] = $p->$opfor_bank;
            }
        }
        DB::table('players')->where('uid', $uid)->update(['bankacc' => $request->bank, 'cash' => $request->cash]);

        $toLog['bank']['pre'] = $preBank;
        $toLog['bank']['post'] = intval($request->bank);
        $toLog['bank']['change'] = $request->bank - $preBank;
        $toLog['cash']['pre'] = $preCash;
        $toLog['cash']['post'] = intval($request->cash);
        $toLog['cash']['change'] = $request->cash - $preCash;

        if (env('TABLE_PLAYERS_SEPCASH', false))
        {
            DB::table('players')->where('uid', $uid)->update([
                env('TABLE_PLAYERS_SEPCASH_COPCASH') => $request->copcash,
                env('TABLE_PLAYERS_SEPCASH_COPBANK') => $request->copbank,
                env('TABLE_PLAYERS_SEPCASH_MEDCASH') => $request->medcash,
                env('TABLE_PLAYERS_SEPCASH_MEDBANK') => $request->medbank,
                env('TABLE_PLAYERS_SEPCASH_OPFORCASH') => $request->opforcash,
                env('TABLE_PLAYERS_SEPCASH_OPFORBANK') => $request->opforbank
                ]);
            $toLog['cop_bank']['pre'] = $output['cop_bank'];
            $toLog['cop_cash']['pre'] = $output['cop_cash'];
            $toLog['med_bank']['pre'] = $output['med_bank'];
            $toLog['med_cash']['pre'] = $output['med_cash'];
            $toLog['opfor_bank']['pre'] = $output['opfor_bank'];
            $toLog['opfor_cash']['pre'] = $output['opfor_cash'];

            $toLog['cop_bank']['post'] = $request->copbank;
            $toLog['cop_cash']['post'] = $request->copcash;
            $toLog['med_bank']['post'] = $request->medbank;
            $toLog['med_cash']['post'] = $request->medcash;
            $toLog['opfor_bank']['post'] = $request->opforbank;
            $toLog['opfor_cash']['post'] = $request->opforcash;

            $toLog['cop_bank']['change'] = $request->copbank - $toLog['cop_bank']['pre'];
            $toLog['cop_cash']['change'] = $request->copcash - $toLog['cop_cash']['pre'];
            $toLog['med_bank']['change'] = $request->medbank - $toLog['med_bank']['pre'];
            $toLog['med_cash']['change'] = $request->medcash - $toLog['med_cash']['pre'];
            $toLog['opfor_bank']['change'] = $request->opforbank - $toLog['opfor_bank']['pre'];
            $toLog['opfor_cash']['change'] = $request->opforcash - $toLog['opfor_cash']['pre'];


        }
        return $toLog;

    }

    public function editOtherData(Request $request, $uid)
    {
        $players = DB::table('players')->where('uid', $uid)->take(1)->get();
        foreach ($players as $p)
        {
            $player = $p;
            $preName = $p->name;
        }
        DB::table('players')->where('uid', $uid)->update(['name' => $request->name]);

        $toLog['name']['pre'] = $preName;
        $toLog['name']['post'] = $request->name;
        if ($toLog['name']['pre'] == $toLog['name']['post'])
        {
            $toLog['name']['changed'] = false;
        } else {
            $toLog['name']['changed'] = true;
        }
        return $toLog;
    }

    public function getCustomFields(Request $request, $uid)
    {
        $players = DB::table('players')->where('uid', $uid)->take(1)->get();
        if(is_null($players)) abort(404);
        $fields = explode(',', $request->fields);

        foreach ($players as $p)
        {
            foreach ($fields as $field)
            {
                $output[$field] = $p->$field;
            }
        }
        return $output;
    }

    public function changeCustomFields(Request $request, $uid)
    {
        $players = DB::table('players')->where('uid', $uid)->take(1)->get();
        if(is_null($players)) abort(404);
        $fields = explode(',', $request->fields);

        foreach ($players as $p)
        {
            foreach ($fields as $field)
            {
                $output['pre'][$field] = $p->$field;
                if (is_null($request->$field))
                {
                    $output['post'][$field] = null;
                } else {
                    DB::table('players')->where('uid', $uid)->update([$field => $request->$field]);
                    $output['post'][$field] = $request->$field;
                }

            }
        }
        return $output;
    }

}
