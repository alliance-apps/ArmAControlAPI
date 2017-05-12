<?php

namespace App\Http\Controllers;

use App\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GETController extends Controller
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
        return "1";
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
            $output[$count]['playtime']['civ'] = intval($playtime[0]);
            $output[$count]['playtime']['cop'] = intval($playtime[1]);
            $output[$count]['playtime']['med'] = intval($playtime[2]);
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
            $output[$count]['playtime']['civ'] = intval($playtime[0]);
            $output[$count]['playtime']['cop'] = intval($playtime[1]);
            $output[$count]['playtime']['med'] = intval($playtime[2]);
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
            $output[$count]['playtime']['civ'] = intval($playtime[0]);
            $output[$count]['playtime']['cop'] = intval($playtime[1]);
            $output[$count]['playtime']['med'] = intval($playtime[2]);
            $output[$count]['civ_licenses'] = $this->convertLicenseMREStoArray($player->civ_licenses);
            $output[$count]['cop_licenses'] = $this->convertLicenseMREStoArray($player->cop_licenses);
            $output[$count]['med_licenses'] = $this->convertLicenseMREStoArray($player->med_licenses);
            $output[$count]['opfor_licenses'] = null;
            $output[$count]['civ_licenses_string'] = $player->civ_licenses;
            $output[$count]['cop_licenses_string'] = $player->cop_licenses;
            $output[$count]['med_licenses_string'] = $player->med_licenses;
            $output[$count]['opfor_licenses_string'] = null;
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





}
