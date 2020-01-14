<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/14
 * Time: 10:49
 */

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class modificationController extends Controller
{
    public function __construct()
    {
        $all=request()->all();
        $token=request()->header('token')??'';
        if (!empty($token)) {
            $all['token']=$token;
        }
        if (empty($all['uid'])||empty($all['token'])) {
           return $this->rejson(202,'登陆失效');
        }
        $check=$this->checktoten($all['uid'],$all['token']);
        if ($check['code']==202) {
           return $this->rejson($check['code'],$check['msg']);
        }
    }
   /**
   * @api {post} /api/modification/user_head 修改用户名称
   * @apiName user_head
   * @apiGroup modification
   * @apiParam {string} id 用户id
   * @apiParam {string} name 要修改用户名称
   * @apiParam {string} token 验证登录
   * @apiSuccessExample 参数返回：
   * {
   *    "code":"200",
   *     "data":"",
   *     "msg":"修改成功"
   * }
   */
  public function user_head(){
      $all=request()->all();
      $res=[
          'name'=>$all['name'],
      ];
      if(empty($all['name'])||empty($all['id'])||empty($all['token'])){
          return $this->rejson(201,"缺少参数");
      }
      $data=DB::table("users")->where('id',$all['id'])->update($res);
      if($data){
          return $this->rejson(200,'修改成功',$data);
      }else{
          return $this->rejson(201,'修改失败');
      }
  }

  /**
   * @api {post} /api/modification/user_pictures 修改用户头像
   * @apiName user_pictures
   * @apiGroup modification
   * @apiParam {string} id 用户id
   * @apiParam {string} token 验证登录
   * @apiParam {string} avator 要修改用户头像
   * @apiSuccessExample 参数返回：
   * {
   *    "code":"200",
   *    "data":"",
   *    "msg":"修改成功"
   * }
   */
  public function user_pictures(){
      $all=request()->all();
      $res=[
          'avator'=>$all['avator'],
      ];
      if(empty($all['avator'])||empty($all['id'])||empty($all['token'])){
          return $this->rejson(201,"缺少参数");
      }
      $data=DB::table("users")->where('id',$all['id'])->update($res);
      if($data){
          return $this->rejson(200,'修改成功',$data);
      }else{
          return $this->rejson(201,'修改失败');
      }
  }
}