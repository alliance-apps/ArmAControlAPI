<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GangController extends Controller
{
    function convertLicenseMREStoArray($licensestring)
    {
        if($licensestring == '"[]"')
        {
            return null;
        }
        $licensestring = str_replace('"[', '', $licensestring);
        $licensestring = str_replace(']"', '', $licensestring);
        $licensestring = str_replace('`', '', $licensestring);
        $licensearray = explode(',', $licensestring);
        return $licensearray;
    }

    function getGangMemberNames($members)
    {
        $giveback = [];
        $count = 0;
        foreach ($members as $member)
        {
            $player = DB::table('players')->where(env('TABLE_PLAYERS_PID', 'pid'), $member)->first();
            if (is_null($player))
            {
                $giveback[$count] = "ERRROR NO PLAYER";
            } else {
                $giveback[$count] = $player->name;
            }
            $count++;
        }
        return $giveback;
    }



    public function ganglist()
    {
        $gangs = DB::table('gangs')->orderBy('active', 'DESC')->get();
        $output = [];
        $count = 0;
        foreach ($gangs as $gang)
        {
            $output[$count]['id'] = $gang->id;
            $output[$count]['owner'] = $gang->owner;
            $output[$count]['name'] = $gang->name;
            $output[$count]['maxmembers'] = $gang->maxmembers;
            $output[$count]['bank'] = $gang->bank;
            $output[$count]['active'] = $gang->active;
            $output[$count]['created_at'] = $gang->insert_time;
            try {
                $output[$count]['members'] = $this->convertLicenseMREStoArray($gang->members);
                $output[$count]['membersNames'] = $this->getGangMemberNames($output[$count]['members']);
            } catch (\Exception $e) {
                $output[$count]['name'] = $output[$count]['name'].' EXCEPTION';
                $output[$count]['members'] = [];
                $output[$count]['membersNames'] = [];
            }


            $count++;
        }
        return $output;
    }

    public function gang($id)
    {
        $gangs = DB::table('gangs')->where('id', $id)->get();
        $output = [];
        foreach ($gangs as $gang)
        {
            $output['id'] = $gang->id;
            $output['owner'] = $gang->owner;
            $output['name'] = $gang->name;
            $output['maxmembers'] = $gang->maxmembers;
            $output['bank'] = $gang->bank;
            $output['active'] = $gang->active;
            $output['created_at'] = $gang->insert_time;
            $output['members'] = $this->convertLicenseMREStoArray($gang->members);
        }
        return $output;
    }

    public function deleteMember($id, Request $request)
    {
        $gangs = DB::table('gangs')->where('id', $id)->get();
        foreach ($gangs as $gang)
        {
            $output['id'] = $gang->id;
            $output['owner'] = $gang->owner;
            $output['name'] = $gang->name;
            $output['maxmembers'] = $gang->maxmembers;
            $output['bank'] = $gang->bank;
            $output['active'] = $gang->active;
            $output['created_at'] = $gang->insert_time;
            $output['members'] = $this->convertLicenseMREStoArray($gang->members);
        }
        $return['error'] = true;

        if (!isset($request->pid)) return $return;
        if ($output['owner'] == $request->pid) return $return;

        $memberstring = '"[';

        foreach ($output['members'] as $member)
        {
            if ($member != $request->pid)
            {
                if ($memberstring == '"[')
                {
                    $memberstring .= '`'.$member.'`';
                } else {
                    $memberstring .= ',`'.$member.'`';
                }
            }
        }
        $memberstring .= ']"';
        $return['error'] = false;
        $return['gang'] = $id;
        $return['removed_player'] = $request->pid;
        DB::table('gangs')->where('id', $id)->update(['members' => $memberstring]);
        return $return;
    }

    public function addMember($id, Request $request)
    {
        $gangs = DB::table('gangs')->where('id', $id)->get();
        foreach ($gangs as $gang)
        {
            $output['id'] = $gang->id;
            $output['owner'] = $gang->owner;
            $output['name'] = $gang->name;
            $output['maxmembers'] = $gang->maxmembers;
            $output['bank'] = $gang->bank;
            $output['active'] = $gang->active;
            $output['created_at'] = $gang->insert_time;
            $output['members'] = $this->convertLicenseMREStoArray($gang->members);
        }
        $return['error'] = true;

        if (!isset($request->pid)) return $return;
        if ($output['owner'] == $request->pid) return $return;

        $memberstring = '"[';

        foreach ($output['members'] as $member)
        {
            if ($member == $request->pid) return $return;
            if ($memberstring == '"[')
            {
                $memberstring .= '`'.$member.'`';
            } else {
                $memberstring .= ',`'.$member.'`';
            }
        }
        $memberstring .= ',`'.$request->pid.'`';
        $memberstring .= ']"';
        $return['error'] = false;
        $return['gang'] = $id;
        $return['added_player'] = $request->pid;
        DB::table('gangs')->where('id', $id)->update(['members' => $memberstring]);
        return $return;
    }

    public function changeOwner($id, Request $request)
    {
        $gangs = DB::table('gangs')->where('id', $id)->get();
        foreach ($gangs as $gang)
        {
            $output['id'] = $gang->id;
            $output['owner'] = $gang->owner;
            $output['name'] = $gang->name;
            $output['maxmembers'] = $gang->maxmembers;
            $output['bank'] = $gang->bank;
            $output['active'] = $gang->active;
            $output['created_at'] = $gang->insert_time;
            $output['members'] = $this->convertLicenseMREStoArray($gang->members);
        }
        $return['error'] = true;
        if (!isset($request->pid)) return $return;

        foreach ($output['members'] as $member)
        {
            if ($member == $request->pid) $return['error'] = false;
        }

        if (!$return['error'])
        {
            $return['error'] = false;
            $return['gang'] = $id;
            $return['old_owner'] = $output['owner'];
            $return['new_owner'] = $request->pid;
            DB::table('gangs')->where('id', $id)->update(['owner' => $request->pid]);
        }
        return $return;
    }

    public function changeName($id, Request $request)
    {
        $gangs = DB::table('gangs')->where('id', $id)->get();
        foreach ($gangs as $gang)
        {
            $return['old_name'] = $gang->name;
        }
        $return['error'] = true;
        if (!isset($request->name)) return $return;

        $return['error'] = false;
        $return['gang'] = $id;
        $return['new_name'] = $request->name;
        DB::table('gangs')->where('id', $id)->update(['name' => $request->name]);
        return $return;
    }

    public function changeOther($id, Request $request)
    {
        $gangs = DB::table('gangs')->where('id', $id)->get();
        foreach ($gangs as $gang)
        {
            $output['id'] = $gang->id;
            $output['owner'] = $gang->owner;
            $output['name'] = $gang->name;
            $output['maxmembers'] = $gang->maxmembers;
            $output['bank'] = $gang->bank;
            $output['active'] = $gang->active;
            $output['created_at'] = $gang->insert_time;
        }
        $return['error'] = true;

        if (!isset($request->maxmembers)) return $return;
        if (!isset($request->bank)) return $return;
        if (!isset($request->active)) return $return;

        $return['error'] = false;
        $return['gang'] = $id;
        $return['old_maxmembers'] = $output['maxmembers'];
        $return['new_maxmembers'] = intval($request->maxmembers);
        $return['old_bank'] = $output['bank'];
        $return['new_bank'] = intval($request->bank);
        $return['old_active'] = $output['active'];
        $return['new_active'] = intval($request->active);
        DB::table('gangs')->where('id', $id)->update([
            'maxmembers' => $request->maxmembers,
            'bank' => $request->bank,
            'active' => $request->active
        ]);
        return $return;
    }



}
