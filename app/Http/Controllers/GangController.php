<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            $player = DB::table('players')->where(config('sharedapi.pid'), $member)->first();
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








    public function aaGangList()
    {
      $gangs = DB::table('gangs')->orderBy('insert_time', 'DESC')->get();
      $count = 0;
      foreach ($gangs as $gang)
      {
          $output[$count]['id'] = $gang->id;
          $output[$count]['owner'] = $gang->owner;
          $output[$count]['name'] = $gang->name;
          $output[$count]['bank'] = $gang->bank;
          $output[$count]['tag'] = $gang->tag;
          $output[$count]['type'] = $gang->type;
          $output[$count]['insert_time'] = $gang->insert_time;
          $count++;
      }
      return $output;
    }

    public function aaGangSingle($id, Request $request)
    {
      $gangs = DB::table('gangs')->where('id', $id)->get();
      $count = 0;

      if($request->small === true)
      {
        foreach ($gangs as $gang)
        {
            $output[$count]['id'] = $gang->id;
            $output[$count]['owner'] = $gang->owner;
            $output[$count]['name'] = $gang->name;
            $output[$count]['bank'] = $gang->bank;
            $output[$count]['active'] = $gang->active;
            $output[$count]['level'] = $gang->level;
            $output[$count]['tag'] = $gang->tag;
            $output[$count]['hq'] = $gang->hq;
            $output[$count]['hq_upgrades'] = $gang->hq_upgrades;
            $perm = str_replace('"', '', $gang->permission);
            $perm = str_replace('`', '"', $perm);
            $output[$count]['permission'] = json_decode($perm);
            $output[$count]['tax'] = $gang->tax;
            $output[$count]['type'] = $gang->type;
            $output[$count]['insert_time'] = $gang->insert_time;

            $count++;
        }
        if($count == 0) return [];
        return $output[0];
      }



      foreach ($gangs as $gang)
      {
          $output[$count]['id'] = $gang->id;
          $output[$count]['owner'] = $gang->owner;
          $output[$count]['name'] = $gang->name;
          $output[$count]['bank'] = $gang->bank;
          $output[$count]['active'] = $gang->active;
          $output[$count]['level'] = $gang->level;
          $output[$count]['tag'] = $gang->tag;
          $output[$count]['description'] = $gang->description;
          $output[$count]['hq'] = $gang->hq;
          $output[$count]['hq_upgrades'] = $gang->hq_upgrades;
          $perm = str_replace('"', '', $gang->permission);
          $perm = str_replace('`', '"', $perm);
          $output[$count]['permission'] = json_decode($perm);
          $output[$count]['tax'] = $gang->tax;
          $output[$count]['type'] = $gang->type;
          $output[$count]['tax_sum'] = $gang->tax_sum;
          $output[$count]['hq_garage'] = $gang->hq_garage;
          $output[$count]['insert_time'] = $gang->insert_time;
          $output[$count]['members'] = DB::table('players')->where('gang_id', $gang->id)->get(['uid', config('sharedapi.pid'), 'name', 'gang_perm_id']);

          $count++;
      }
      if($count == 0) return [];
      return $output[0];
    }

    public function aaChangeParams(Request $request, $id)
    {
      $gang = $this->aaGangSingle($id);
      $return['error'] = true;

      if (!isset($request->owner)) return $return;
      if (!isset($request->name)) return $return;
      if (!isset($request->bank)) return $return;
      if (!isset($request->level)) return $return;
      if (!isset($request->tag)) return $return;
      if (!isset($request->description)) return $return;
      if (!isset($request->hq)) return $return;
      if (!isset($request->hq_upgrades)) return $return;
      if (!isset($request->tax)) return $return;

      $return['error'] = false;
      $return['gang'] = $id;
      $return['old_owner'] = $gang->owner;
      $return['new_owner'] = $request->owner;
      $return['old_name'] = $gang->name;
      $return['new_name'] = $request->name;
      $return['old_bank'] = intval($gang->bank);
      $return['new_bank'] = intval($request->bank);
      $return['old_level'] = intval($gang->level);
      $return['new_level'] = intval($request->level);
      $return['old_tag'] = $gang->tag;
      $return['new_tag'] = $request->tag;
      $return['old_description'] = $gang->description;
      $return['new_description'] = $request->description;
      $return['old_hq'] = $gang->hq;
      $return['new_hq'] = $request->hq;
      $return['old_hq_upgrades'] = $gang->hq_upgrades;
      $return['new_hq_upgrades'] = $request->hq_upgrades;
      $return['old_tax'] = intval($gang->tax);
      $return['new_tax'] = intval($request->tax);

      DB::table('gangs')->where('id', $id)->update([
          'owner' => $request->owner,
          'name' => $request->name,
          'bank' => intval($request->bank),
          'level' => intval($request->level),
          'tag' => $request->tag,
          'description' => $request->description,
          'hq' => $request->hq,
          'hq_upgrades' => $request->hq_upgrades,
          'tax' => intval($request->tax),
      ]);
      return $return;
    }

    public function aaEditMembers(Request $request)
    {
      $pid = intval($request->playeruid);
      $gang_id = intval($request->gangid);
      $gang_perm_id = intval($request->gang_perm_id);
      $return['error'] = false;
      DB::table('players')->where('uid', $pid)->update([
          'gang_id' => $gang_id,
          'gang_perm_id' => $gang_perm_id
      ]);
      return $return;
    }

















}
