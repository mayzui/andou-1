<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use App\Services\ActionLogsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Auth;

class InviteController extends BaseController
{
    /**
     * @author  jsy
     * @deprecated  邀请码管理显示
     */

     public function inviteList(Request $request)
     {
         $input =$request->all();
         if(empty($input['code'])){
             $inviteData = DB::table("invite")
                 ->paginate(10);
             return $this->view('invitelist',['list'=>$inviteData]);
         }else{
             $code = $input['code'];
             $inviteData = DB::table("invite")
                 ->where('invite_code','like','%'.$code.'%')
                 ->paginate(10);
             if(empty($inviteData[0])){
                 $inviteData = DB::table("invite")
                   ->where('usernames','like','%'.$code.'%')
                   ->paginate(2);
             }
             return $this->view('invitelist',['list'=>$inviteData,'code'=>$code]);
         }

     }
}

