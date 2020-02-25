<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class InvitesController extends Controller
{
    /**
     * @api {post} /api/invites/makeinvite 生成邀请码
     * @apiName makeinvite
     * @apiParam {string} uid 用户id
     * @apiGroup invite
     * @apiSuccessExample 参数返回:
     * {
          "code": 200,
          "msg": "成功生成邀请码",
         "data": {
              "data": {
              "code": "00005E",
              "uid": 35,
              "make_time": "2020-02-25:10:51:18",
              "phone": "17671446672",
         }
           }
     }
     */
    public function makeInvite()
    {
        $all=request()->all();
        if(empty($all['uid'])){
            return $this->rejson(205,'请先登录','');exit;
        }
        $id =$all['uid'];    // 当前登录用户的id
        $vipData = DB::table("vip")
            ->where(['user_id'=>$id,'is_del'=>0])
            ->first(['user_id','grade']);
        if(empty($vipData)){
           return $this->rejson(201,'您当前还不是超级会员','');exit;
        }
         $uid = $vipData->user_id;
         $inviteCode = $this->createCode("$uid");
         $selUser = DB::table("users")               //用户信息
             ->where('id','=',$uid)
             ->first(['id','mobile','name']);
         if(empty($selUser)){
             return $this->rejson(202,'没有该用户','');exit;
         }
         $screen = DB::table("invite")
             ->where('users_id','=',$uid)
             ->first(['users_id']);
         if(!empty($screen)){
             return $this->rejson(203,'该用户已有邀请码','');exit;
         }
         $time =date("Y-m-d:H:i:s",time());
         $createInvite = DB::table("invite")
             ->insert([
                 'usernames'     =>  $selUser->name,
                 'invite_code'   =>  $inviteCode,
                 'make_time'     => $time,
                 'phone'         => $selUser->mobile,
                 'users_id'      => $selUser->id
             ]);
         $return = ['code'=>$inviteCode,'uid'=>$selUser->id,'make_time'=>$time,'phone'=>$selUser->mobile];
        if($createInvite){
            return $this->rejson(200,'成功生成邀请码',['data'=>$return]);exit;
        }else{
            return $this->rejson(206,'未生成邀请码','');exit;
        }
    }

    /**
     * @api {get} /api/invites/invitenum 邀请码下级数
     * @apiName invitenum
     * @apiParam {string} code 邀请码
     * @apiGroup invite
     * @apiSuccessExample 参数返回:
     *{
         "code": "200",
         "msg": "邀请成功",
         "data": {
         "collar": 2,
         "uid": 13
         }
     * }
     */
       public function inviteNum()
       {
           $all=request()->all();
           if(empty($all['code'])){
               return $this->rejson('201','请输入邀请码');exit;
           }
           $code = $all['code'];        //验证码
           $uid =  $this->deCode($code);
           $oldCollar = DB::table("invite")
               ->where('users_id','=',$uid)
               ->first(['id','collar','invite_code']);
           if(empty($oldCollar)){            //判断用户是否有验证码
               return $this->rejson('202','该用户不是超级会员');exit;
           }
           if($oldCollar->invite_code != $code){   //判断邀请码是否一致
               return $this->rejson('203','填入的邀请码不一致');exit;
           }
           $oldVal = $oldCollar->collar;
           $id = $oldCollar->id;        //邀请码主键id

           $updCollar = DB::table("invite")
               ->where('id',$id)
               ->update(['collar'=>$oldVal+1]);
           $newCollar = DB::table("invite")
               ->where('id','=',$id)
               ->first(['id','collar','invite_code']);
           $return = ['collar'=>$newCollar->collar,'uid'=>$uid];
          if ($updCollar){
              return $this->rejson('200','邀请成功',$return);exit;
          }else{
              return $this->rejson('204','邀请失败');exit;
          }
       }
}