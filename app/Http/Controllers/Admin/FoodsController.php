<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Role;
use App\Repositories\RulesRepository;
use App\Handlers\Tree;
use PhpParser\Node\Stmt\Return_;

class FoodsController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /*
     *      修改状态
     * */
    public function informationStatus(){
        $all = \request() -> all();
        // 根据获取的id 查询状态
        $foods_information_data = DB::table('foods_information') -> where('id',$all['id']) -> first();
        // 判断当前状态
        if($foods_information_data -> status == 1){
            $data = [
                'status' => 0
            ];
        }else{
            $data = [
                'status' => 1
            ];
        }
        // 执行修改操作
        $i = DB::table('foods_information') -> where('id',$all['id']) -> update($data);
        if($i){
            flash('更新成功') -> success();
            return redirect()->route('foods.information');
        }else{
            flash('更新失败') -> error();
            return redirect()->route('foods.information');
        }
    }

    /*
     *      拒绝退款
     * */
    public function return_refuse(){
        $all = \request() -> all();
        // 拒绝退款，将状态修改为拒绝退款
        $i = DB::table('foods_user_ordering') -> where('id',$all['id']) -> update(['status'=>80]);
        if($i){
            flash("已拒绝退款") -> success();
            return redirect()->route('foods.orders');
        }else{
            flash("失败，请稍后重试") -> error();
            return redirect()->route('foods.orders');
        }
    }
    /*
     *      同意退款
     * */
    public function return_money(){
        $all = \request() -> all();
        // 同意退款，1.将金额返还用户，2.将状态修改为同意退款
        // 根据当前订单id，获取当前订单信息
        $data = DB::table('foods_user_ordering') -> where('id',$all['id']) -> first();
        // 获取用户信息
        $user_data = DB::table('users') -> where('id',$data -> user_id) -> first();
        // 判断用户支付方式
        if($data -> method == 4){    // 余额支付
            $money = $user_data -> money + $data -> pay_money;
            DB::beginTransaction();
            try{
                // 修改用户金额
                $m = DB::table('users') -> where('id',$data -> user_id) -> update(['money' => $money]);
                // 修改订单状态
                $n = DB::table('foods_user_ordering') -> where('id',$all['id']) -> update(['status'=>70]);
                // 修改主表订单状态
                $j = DB::table('orders') -> where('order_sn',$data -> order_sn) -> first();
                if(empty($j)){
                    DB::rollBack();
                    flash('退款失败，订单表中未查询到相关订单') -> error();
                    return redirect()->route('foods.orders');
                }
                DB::table('orders') -> where('order_sn',$data -> order_sn) -> update(['status'=>70]);
                DB::commit();
                flash("退款成功") -> success();
                return redirect()->route('foods.orders');
            }catch (\Exception $exception){
                DB::rollBack();
                flash("退款失败，请稍后重试") -> error();
                return redirect()->route('foods.orders');
            }
        }else if($data -> method == 1){
            // 微信退款
            require_once base_path()."/wxpay/lib/WxPay.Api.php";
            require_once base_path()."/wxpay/example/WxPay.NativePay.php";
            //查询订单,根据订单里边的数据进行退款
            $order = json_decode(json_encode(DB::table('foods_user_ordering') -> where('id',$all['id']) -> first()),true);
            $merch = new \WxPayConfig();
            $merchid = $merch->GetMerchantId();
            if(!$order){
                return false;
            }
            $suiji = $this -> suiji();
            $input = new \WxPayRefund();
            $input->SetOut_trade_no($order['order_sn']);   //自己的订单号
            $input->SetOut_refund_no($order['order_sn']);   //退款单号
            $input->SetTotal_fee((int)($order['pay_money']*100));   // 订单标价金额，单位为分
            $input->SetRefund_fee((int)($order['pay_money']*100));   // 退款总金额，订单总金额，单位为分，只能为整数
            $input->SetOp_user_id($merchid);        // 商户号
            $result = \WxPayApi::refund($merch,$input); //退款操作
            if(($result['return_code']=='SUCCESS') && ($result['result_code']=='SUCCESS')){
                $n = DB::table('foods_user_ordering') -> where('id',$all['id']) -> update(['status'=>70]);
                //退款成功
                flash('退款成功') -> success();
                return redirect()->route('foods.orders');
            }else if(($result['return_code']=='FAIL') || ($result['result_code']=='FAIL')){
                //退款失败
                //原因
                $reason = (empty($result['err_code_des'])?$result['return_msg']:$result['err_code_des']);

                flash($reason) -> error();
                return redirect()->route('foods.orders');
            }else{
                //失败
                flash("退款失败请稍后重试") -> error();
                return redirect()->route('foods.orders');
            }
        }
    }

    /*
     *      评论
     * */
    // 商品评论
    public function commnets(){
        $id = Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            $data = DB::table('order_commnets')
                -> join('users','order_commnets.user_id','=','users.id')     // 链接用户表
                -> join('foods_information','order_commnets.goods_id','=','foods_information.id')     // 链接商品表
                -> where('type',3)
                -> where('merchants_id',$i->id)
                -> where('order_commnets.is_del',0)
                -> select(['order_commnets.id','users.name as username','foods_information.name as goodsname','stars','order_commnets.content','order_commnets.created_at'])
                -> paginate(10);
        }else{
            // 反之则为。管理员
            // 查询，商城评论
            $data = DB::table('order_commnets')
                -> join('users','order_commnets.user_id','=','users.id')     // 链接用户表
                -> join('foods_information','order_commnets.goods_id','=','foods_information.id')     // 链接商品表
                -> where('type',3)
                -> where('order_commnets.is_del',0)
                -> select(['order_commnets.id','users.name as username','foods_information.name as goodsname','stars','order_commnets.content','order_commnets.created_at'])
                -> paginate(10);

        }
        return $this->view('',['data' => $data]);

    }
    // 新增商品评论
    public function commnetsAdd(){
        $id = Auth::id();
        if(\request() -> isMethod("get")){
            // 查询商品列表
            $goodsData = DB::table("foods_information") -> get();
            // 跳转新增界面
            return $this->view('',['goodsData'=>$goodsData]);
        }else{
            // 执行新增操作
            // 获取提交的内容
            $all = \request() -> all();
            $data  = [
                'order_id' => $id,
                'user_id' => $id,
                'goods_id' => $all['goods_id'],
                'type' => 3,
                'merchants_id' => $id,
                'stars' => $all['stars'],
                'content' => $all['content'],
                'created_at' => date("Y-m-d H:i:s")
            ];
            // 链接数据库，新增内容
            $i = DB::table('order_commnets') -> insert($data);
            if($i){
                flash('新增成功') -> success();
                return redirect()->route('foods.commnets');
            }else{
                flash('新增失败') -> error();
                return redirect()->route('foods.commnets');
            }
        }
    }
    // 删除商品评论
    public function commnetsDel(){
        // 获取传入的id
        $all = \request() -> all();
        // 根据id删除表中数据
        $data = [
            'is_del' => 1
        ];
        $i = DB::table("order_commnets") -> where('id',$all['id']) -> update($data);
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('foods.commnets');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('foods.commnets');
        }
    }

    /*
     *      套餐
     * */
    public function set_meal(){
        $all = \request() -> all();
        // 判断用户是否开店，并且已经认证通过
        $id = Auth::id();
        // 判断该用户，是否申请饭店 并且已经认证通过
        $i = DB::table('merchants')
            -> where("user_id",$id)
            -> where("is_reg",1)
            -> where("merchant_type_id",4)
            -> first();
        // 判断是否执行条件查询
        if(!empty($all['name'])){
            // 条件查询
            $where[] = ['foods_set_meal.name', 'like', '%'.$all['name'].'%'];
            $name = $all['name'];
        }else{
            // 跳转页面
            $where[] = ['foods_set_meal.name', 'like', '%'."".'%'];
            $name = "";
        }

        if(!empty($i)){
            // 商户
            // 查询数据库，套餐信息表内容
            $data = DB::table("foods_set_meal")
                -> join('merchants','foods_set_meal.merchant_id','=','merchants.id')
                -> where('foods_set_meal.merchant_id',$i -> id)
                -> where('is_del',0)
                -> where($where)
                -> select(['foods_set_meal.id','foods_set_meal.name as set_meal_name','image','price','num','room','room_price','foods_set_meal.status','merchants.name as merchants_name'])
                -> paginate(10);
        }else{
            // 管理员
            // 查询数据库，套餐信息表内容
            $data = DB::table("foods_set_meal")
                -> join('merchants','foods_set_meal.merchant_id','=','merchants.id')
                -> where('is_del',0)
                -> where($where)
                -> select(['foods_set_meal.id','foods_set_meal.name as set_meal_name','image','price','num','room','room_price','foods_set_meal.status','merchants.name as merchants_name'])
                -> paginate(10);
        }
        return $this->view('',['data'=>$data,'name'=>$name]);
    }

    // 新增 and 修改 套餐信息
    public function set_mealchange(){
        $all = \request() -> all();
        if(\request() -> isMethod("get")){
            // 判断跳转新增界面，还是修改界面
            if(empty($all['id'])){
                $data = (object)[
                    'room' => 0
                ];
                // 跳转新增界面
                return $this->view('',['data'=>$data]);
            }else{
                // 跳转修改界面
                // 根据传入的id 查询数据库中的内容
                $data = DB::table("foods_set_meal") -> where('id',$all['id']) -> first();
                return $this->view('',['data'=>$data]);
            }
        }else{
            // 判断执行新增操作，还是执行修改操作
            if(empty($all['id'])){
                // 执行新增操作
                $room_price = $all['room_price'];
                if(empty($all['room'])){
                    $room = 0;
                    $room_price = 0;
                }else{
                    $room = 1;
                }
                $merchants_data = DB::table('merchants') -> where("user_id",Auth::id())-> where('merchant_type_id',4) -> first();
                // 获取提交的数据
                $data = [
                    'merchant_id' => $merchants_data -> id,
                    'name' => $all['name'],
                    'image' => $all['img'],
                    'price' => $all['price'],
                    'num' => $all['num'],
                    'room' => $room,
                    'room_price' => $room_price
                ];
                // 链接数据库，执行新增操作
                $i = DB::table("foods_set_meal") -> insert($data);
                if($i){
                    flash('新增成功') -> success();
                    return redirect()->route('foods.set_meal');
                }else{
                    flash('新增失败') -> error();
                    return redirect()->route('foods.set_meal');
                }
            }else{
                // 执行修改操作
                $room_price = $all['room_price'];
                if(empty($all['room'])){
                    $room = 0;
                    $room_price = 0;
                }else{
                    $room = 1;
                }
                $merchants_data = DB::table('merchants') -> where("user_id",Auth::id())-> where('merchant_type_id',4) -> first();
                // 获取提交的数据
                $data = [
                    'merchant_id' => $merchants_data -> id,
                    'name' => $all['name'],
                    'image' => $all['img'],
                    'price' => $all['price'],
                    'num' => $all['num'],
                    'room' => $room,
                    'room_price' => $room_price
                ];
                // 根据获取的id 对信息进行修改
                $i = DB::table("foods_set_meal") -> where('id',$all['id']) -> update($data);
                if($i){
                    flash('修改成功') -> success();
                    return redirect()->route('foods.set_meal');
                }else{
                    flash('修改失败，未进行任何修改。') -> error();
                    return redirect()->route('foods.set_meal');
                }
            }
        }
    }

    // 删除套餐信息
    public function set_mealdel(){
        // 获取传入的id值
        $all = \request() -> all();
        // 根据id 删除表中数据
        $data = [
            'is_del' => 1
        ];
        // 链接数据库 删除数据
        $i = DB::table("foods_set_meal") -> where('id',$all['id']) -> update($data);
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('foods.set_meal');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('foods.set_meal');
        }
    }

    // 修改上下架状态
    public function set_mealstatus(){
        // 获取传入的id值
        $all = \request() -> all();
        // 根据id 查询数据库中的内容
        $data = DB::table("foods_set_meal")
            -> where('id',$all['id'])
            -> select(['status'])
            -> first();
        // 判断当前状态是上架 还是 下架
        if($data->status == 0){
            // 下架
            $arr = [
                'status' => 1
            ];
        }else{
            // 上架
            $arr = [
                'status' => 0
            ];
        }
        // 链接数据库 修改上下架状态
        $i = DB::table("foods_set_meal") -> where('id',$all['id']) -> update($arr);
        if($i){
            flash('状态更新成功') -> success();
            return redirect()->route('foods.set_meal');
        }else{
            flash('状态更新失败') -> error();
            return redirect()->route('foods.set_meal');
        }
    }

    // 套餐中的商品信息
    public function set_meal_information(){
        // 获取传入的 id
        $all = \request() ->all();
        // 根据传入的id 查询套餐表中的数据
        $meal = DB::table("foods_set_meal")
            -> select(['id','merchant_id','name'])
            -> where('id',$all['id']) -> first();
        // 查询菜品详情表中的数据
        $information = DB::table("foods_information") -> get();
        // 根据传入的id，获取套餐商品表中的菜品id，
        $data = DB::table("foods_set_meal_information") -> where('set_meal_id',$all['id']) ->get();
        // 将获取的数据转换为数组
        $data = json_decode(json_encode($data),true);
        // 定义一个空数组用于存放菜品id
        $a = [];
        foreach ($data as $k =>$v){
            // 将数据库获取的值中的菜品id 取出来
            $a[$k] = $v['information_id'];
        }
        // 定义一个数组，用于传输数据
        $arr = [
            'meal' => $meal,
            'information' => $information,
            'information_id' => $a
        ];
        return $this->view('',$arr);
    }

    // 修改 套餐中的商品信息
    public function set_meal_informationChange(){
        // 获取提交的数据
        $all = \request() ->all();
        // 删除具有该套餐id 中，商品信息表中的所有信息
        DB::table("foods_set_meal_information") -> where('set_meal_id',$all['set_meal_id']) ->delete();

        $information_id = $all['information_id'];
        $i = 0;
        // 循环新增数据
        foreach ($information_id as $v){
            // 定义一个数组用于新增
            $data = [
                'set_meal_id' => $all['set_meal_id'],
                'set_meal_name' => $all['set_meal_name'],
                'information_id' => $v
            ];
            // 链接数据库 新增数据
            $i = DB::table("foods_set_meal_information") -> insert($data);
            $data = [];
        }
        if($i){
            flash('操作成功') -> success();
            return redirect()->route('foods.set_meal');
        }else{
            flash('操作失败') -> error();
            return redirect()->route('foods.set_meal');
        }
    }

    /*
     *      饭店管理
     * */
    public function administration(){
        $all = request()->all();
        $id = \Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            $where[]=['id','>','0'];
            $where[]=['merchant_type_id',4];
            $screen['merchant_type_id'] = 4;
            if (!empty($all['name'])) {
                $where[]=['name', 'like', '%'.$all['name'].'%'];
                $screen['name']=$all['name'];
            }else{
                $screen['name']='';
            }
            if(!empty($all['status'])){
                $status = $all['status'];
                if($all['status'] == 2){            // 待审核
                    $where[] = ['merchants.is_reg',0];
                }elseif ($all['status'] == 1){      // 已审核
                    $where[] = ['merchants.is_reg',1];
                }elseif ($all['status'] == 3){      // 已禁用
                    $where[] = ['merchants.status',0];
                }elseif ($all['status'] == 4){      // 已启用
                    $where[] = ['merchants.status',1];
                }else{

                }
            }else{
                $status = 0;
            }
            $data=DB::table('merchants')
                -> where('user_id',$id)
                -> where($where)
                -> orderBy('is_reg','desc')
                -> paginate(10);
            foreach ($data as $key => $value) {
                $merchant_type=Db::table('merchant_type')->where('id',$value->merchant_type_id)->pluck('type_name');
                if (!empty($merchant_type[0])) {
                    $data[$key]->merchant_type_id=$merchant_type[0];
                }else{
                    $data[$key]->merchant_type_id='';
                }
                $username=Db::table('users')->where('id',$value->user_id)->pluck('name');
                if (!empty($username[0])) {
                    $data[$key]->username=$username[0];
                }else{
                    $data[$key]->username='';
                }
            }
            $wheres['type']=DB::table('merchant_type')->get();
            $wheres['where']=$screen;
        }else{
            $where[]=['id','>','0'];
            $where[]=['merchant_type_id',4];
            $screen['merchant_type_id'] = 4;
            if (!empty($all['name'])) {
                $where[]=['name', 'like', '%'.$all['name'].'%'];
                $screen['name']=$all['name'];
            }else{
                $screen['name']='';
            }
            if(!empty($all['status'])){
                $status = $all['status'];
                if($all['status'] == 2){            // 待审核
                    $where[] = ['merchants.is_reg',0];
                }elseif ($all['status'] == 1){      // 已审核
                    $where[] = ['merchants.is_reg',1];
                }elseif ($all['status'] == 3){      // 已禁用
                    $where[] = ['merchants.status',0];
                }elseif ($all['status'] == 4){      // 已启用
                    $where[] = ['merchants.status',1];
                }else{

                }
            }else{
                $status = 0;
            }
            $data=DB::table('merchants')
                ->where($where)
                -> orderBy('is_reg','desc')
                ->paginate(10);
            foreach ($data as $key => $value) {
                $merchant_type=Db::table('merchant_type')->where('id',$value->merchant_type_id)->pluck('type_name');
                if (!empty($merchant_type[0])) {
                    $data[$key]->merchant_type_id=$merchant_type[0];
                }else{
                    $data[$key]->merchant_type_id='';
                }
                $username=Db::table('users')->where('id',$value->user_id)->pluck('name');
                if (!empty($username[0])) {
                    $data[$key]->username=$username[0];
                }else{
                    $data[$key]->username='';
                }
            }
            $wheres['type']=DB::table('merchant_type')->get();
            $wheres['where']=$screen;
        }
        return $this->view('',['data'=>$data,'i'=>$i,'status' => $status],['wheres'=>$wheres]);
    }
    // 禁用商家
    public function status(){
        $all = \request() -> all();
        // 根据当前id 查询当前商户的状态
        $data = DB::table('merchants') -> where('id',$all['id']) ->first();
        if($data -> status == 1){
            $arr = [
                'status' => 0
            ];
        }else{
            $arr = [
                'status' => 1
            ];
        }
        $i = DB::table('merchants') -> where('id',$all['id']) -> update($arr);
        if($i){
            flash("状态更新成功") -> success();
            return redirect()->route('foods.administration');
        }else{
            flash("状态更新失败") -> error();
            return redirect()->route('foods.administration');
        }
    }

    // 修改饭店状态
    public function administrationStatus(){
        $all = \request() -> all();
        if($all['is_reg'] ==  0){
            $data = ['status'=>1];
        }else{
            $data = ['status'=>0];
        }
        // 链接数据库，修改商家状态
        $i = DB::table('foods_classification') -> where('id',$all['id']) -> update($data);
        if($i){
            flash("状态更新成功") -> success();
            return redirect()->route('foods.administration');
        }else{
            flash("状态更新失败") -> error();
            return redirect()->route('foods.administration');
        }
    }


    /*
     *      饭店审核
     * */
    public function examine(){
        // 链接数据库，查询商户表
        $data = DB::table("merchants")
            -> join("merchant_type","merchants.merchant_type_id","=","merchant_type.id")
            -> where("role_id",5)
            -> select(['merchants.id','merchants.user_id','is_reg','merchants.name','merchants.desc','merchants.address','merchant_type.type_name'])
            -> paginate(10);
        // 跳转饭店审核界面
        return $this->view('',['data'=>$data]);
    }

    // 审核通过
    public function examinepass(){
        // 获取传入的值
        $all = \request() -> all();
        // 定义一个数组用于存放修改数据
        $data = [
            'is_reg' => 1
        ];
        // 链接数据库，修改商户认证状态
        $i = DB::table("merchants") -> where('id',$all['id']) -> update($data);
        if($i){
            flash('认证成功') -> success();
            return redirect()->route('foods.examine');
        }else{
            flash('认证失败') -> error();
            return redirect()->route('foods.examine');
        }

    }


    /*
     *      订单表
     * */
    // 跳转订单界面
    public function orders(){
        $all = \request() -> all();
        // 判断用户是否开店，并且已经认证通过
        $id = Auth::id();
        // 判断该用户，是否申请饭店 并且已经认证通过
        $i = DB::table('merchants')
            -> join("merchant_type","merchants.merchant_type_id","=","merchant_type.id")
            ->select('merchants.id')
            -> where("role_id",5)
            -> where("user_id",$id)
            -> where("is_reg",1)
            -> first();
        $where=[];

        // 判断条件查询
        if(!empty($all['status'])){
            $status = $all['status'];
            if($all['status'] == 20){            // 待入住
                $where[] = ['status',20];
            }elseif ($all['status'] == 30){      // 已入住
                $where[] = ['status',30];
            }elseif ($all['status'] == 60){      // 申请退款
                $where[] = ['status',60];
            }elseif ($all['status'] == 10){      // 未支付
                $where[] = ['status',10];
            }elseif ($all['status'] == 70){      // 已退款
                $where[] = ['status',70];
            }
        }else{
            $status = 0;
        }
        if(!empty($i)){
            // 如果开店，则能够看到当前店铺用户订单信息
            // 查询数据库数据
            if(!empty($all['name'])){
                // 条件查询
                $data=Db::table('foods_user_ordering') -> where('phone','like','%'.$all['name'].'%')->orderBy('pay_time','desc') -> where('merchant_id',$i->id) ->paginate(10);
                if(count($data) == 0){
                    $data=Db::table('foods_user_ordering')-> where('user_name','like','%'.$all['name'].'%')->orderBy('pay_time','desc') -> where('merchant_id',$i->id) ->paginate(10);
                    if(count($data) == 0){
                        $data=Db::table('foods_user_ordering') -> where('order_sn','like','%'.$all['name'].'%')->orderBy('pay_time','desc') -> where('merchant_id',$i->id) ->paginate(10);
                    }
                }
                $name = $all['name'];
                $id = "";
            }else{
                // 跳转页面
                $data=Db::table('foods_user_ordering')->where($where)->orderBy('pay_time','desc') -> where('merchant_id',$i->id)->paginate(10);
                $name = "";
                $id = "";
            }
        }else{
            // 如果未开店，则为超级管理员，能够看见所有的数据
            // 查询数据库数据
            // 判断是否执行条件查询
            if(!empty($all['name'])){
                // 条件查询
                $data=Db::table('foods_user_ordering') -> where('phone','like','%'.$all['name'].'%')->orderBy('pay_time','desc') ->paginate(10);
                if(count($data) == 0){
                    $data=Db::table('foods_user_ordering')-> where('user_name','like','%'.$all['name'].'%')->orderBy('pay_time','desc') ->paginate(10);
                    if(count($data) == 0){
                        $data=Db::table('foods_user_ordering') -> where('order_sn','like','%'.$all['name'].'%')->orderBy('pay_time','desc') ->paginate(10);
                    }
                }
                $name = $all['name'];
                $id = "";
            }else{
                // 跳转页面
                $data=Db::table('foods_user_ordering')->where($where)->orderBy('pay_time','desc')->paginate(10);
                $name = "";
                $id = "";
            }
        }

        return $this -> view('',['data'=>$data,'id'=>$id,'name'=>$name,'status'=>$status]);
    }

    // 新增 and 修改订单
    public function orderschange(){
        $all = \request() -> all();
        if(\request() -> isMethod("get")){
            // 判断是跳转新增界面还是跳转修改界面
             if(empty($all['id'])){
                 // 查询菜品详情表
                 $information = [];
                 $information = DB::table("foods_information") -> get();
                 $particulars = [];
                 $order = (object)[
                     'method' => ""
                 ];
                 // 定义一个数组来用于接收上传数据
                 $arr = [
                     'order' => $order,
                     'information' => $information
                 ];
                 // 跳转新增界面
                 return $this -> view('',$arr);
             }else{// 查询菜品详情表
                 $information = [];
                 $information = DB::table("foods_information") -> get();
                 // 根据提交的id，查询数据库订单表中的内容
                 $order = DB::table("foods_user_ordering") -> where('id',$all['id']) -> first();
                 $order_goods = json_decode($order -> foods_id,true);
                 // 根据id 查询菜品信息
                 foreach ($order_goods as $k =>$v) {
                     $goods_information[]  = DB::table('foods_information')->where('id', $v['id'])->first();
                     if(!empty($goods_information[$k])){
                         $goods_information[$k] -> shuliang = $v['num'];
                     }
                 }
                 // 根据提交的id，查询数据库订单菜品表中的内容
                 $particulars = DB::table("foods_order_particulars") -> where('order_id',$all['id']) -> get();
                 $arr = [
                     'order' => $order,
                     'goods_information' => $goods_information,
                     'particulars' => $particulars,
                     'information' => $information
                 ];
//                 return dd($goods_information);
                 // 跳转修改界面
                 return $this -> view('',$arr);
             }
        }else{
            if(empty($all['id'])){
                // 执行新增操作
                // 获取提交的值
                $ids = $all['ids'];
                $num = $all['num'];
                $datas = [
                    'merchant_id' => Auth::id(),
                    'user_id' => Auth::id(),
                    'user_name' => $all['user_name'],
                    'phone' => $all['phone'],
                    'people' => $all['people'],
                    'orderingtime' => date("Y-m-d h:m:s"),
                    'dinnertime' => $all['dinnertime'],
                    'remark' => $all['remark'],
                    'prices' => $all['prices'],
                    'method' => $all['method'],
                ];

                // 开启事务
                DB::beginTransaction();
                try{
                    // 链接数据库，执行新增操作，并获取当前新增的id
                    $id = DB::table("foods_user_ordering") -> insertGetId($datas);
                    foreach($num as $k => $v){
                        if($v != ""){
                            $data = [
                                'order_id' => $id,
                                'foods_id' => $ids[$k],
                                'num' => $v
                            ];
                            // 链接数据库 新增数据
                            $i = DB::table("foods_order_particulars") -> insert($data);
                        }
                    }
                    if($i){
                        // 提交事务
                        DB::commit();
                        flash('新增成功') -> success();
                        return redirect()->route('foods.orders');
                    }else{
                        // 回滚事务
                        DB::rollBack();
                        flash('新增失败1') -> error();
                        return redirect()->route('foods.orders');
                    }
                }catch (\Exception $e){
                    // 回滚事务
                    DB::rollBack();
                    flash('新增失败2') -> error();
                    return redirect()->route('foods.orders');
                }

            }else{
                // 执行修改操作
                return "doUpdate";
            }
        }
    }


    /*
     *      菜品详情
     * */
    //跳转菜品详情界面
    public function information(){
        $all = \request() -> all();
        $id = Auth::id();
        // 判断该用户，是否开店
        $i = DB::table('merchants') -> where("user_id",$id)
            -> where('merchant_type_id',4) -> first();
        // 判断是否执行条件查询
        if(!empty($all['name'])){
            // 条件查询
            $where[] = ['foods_information.name', 'like', '%'.$all['name'].'%'];
            $name = $all['name'];
        }else{
            // 跳转页面
            $where[] = ['foods_information.name', 'like', '%'."".'%'];
            $name = "";
        }

        if(!empty($i)){
            // 如果开店，则能够看到自己的菜品详情
            // 查询数据库数据
            $data = DB::table("foods_information")
                -> join('foods_classification','foods_information.classification_id','=','foods_classification.id')
                -> join('merchants','foods_information.merchant_id','=','merchants.id')
                -> where('merchant_id',$i->id)
                -> where($where)
                -> select(['foods_information.id','foods_information.status','merchants.name as merchants_name','foods_classification.name as class_name','foods_information.name as info_name','price','image','specifications','remark','quantitySold','num'])
                -> paginate(10);
        }else{
            // 如果开店，则为超级管理员，能够看见所有的数据
            // 查询数据库数据
            $data = DB::table("foods_information")
                -> join('foods_classification','foods_information.classification_id','=','foods_classification.id')
                -> join('merchants','foods_information.merchant_id','=','merchants.id')
                -> where($where)
                -> select(['foods_information.id','foods_information.status','merchants.name as merchants_name','foods_classification.name as class_name','foods_information.name as info_name','price','image','specifications','remark','quantitySold','num'])
                -> paginate(10);
            $id = "";
        }
        return $this -> view('',['data'=>$data,'id'=>$id,'name'=>$name]);
    }
    // 新增菜品详情
    public function informationadd(){
        // 获取当前商户id
        $id = Auth::id();
        $data = DB::table('merchants') -> where("user_id",$id)-> where('merchant_type_id',4) -> first();
        if(\request()->isMethod("get")){
            $all = \request() -> all();
            // 判断跳转新增界面还是修改界面
            if(empty($all['id'])) {
                // 跳转新增界面
                // 链接数据库，查询菜品分类
                $type = DB::table("foods_classification") ->where('merchants_id',$data -> id) -> get();
                // 链接数据库，查询菜品规格
                if(!empty($i)){
                    $spec = DB::table("foods_spec") ->where('merchant_id',$data -> id) -> get();
                }else{
                    $spec = DB::table("foods_spec") -> get();
                }
                $data = (object)[
                    "classification_id" => 0,
                    "specifications" => []
                ];
                // 定义一个传值用的数组
                $arr = [
                    'data' => $data,
                    'type' => $type,
                    'spec' => $spec
                ];
                return $this -> view('',$arr);
            }else{
                // 跳转修改界面
                // 根据获取的id 查询数据库中的值
                $data = DB::table("foods_information") -> where("id",$all['id']) -> first();
                // 链接数据库，查询菜品分类
                $type = DB::table("foods_classification") -> get();
                // 链接数据库，查询菜品规格
                if(!empty($i)){
                    $spec = DB::table("foods_spec") -> where('merchant_id',$data -> id) -> get();
                }else{
                    $spec = DB::table("foods_spec") -> get();
                }
                // 获得查询出来的菜品规格
                $data->specifications =explode(",",$data->specifications);
                // 定义一个传值用的数组
                $arr = [
                    'data' => $data,
                    'type' => $type,
                    'spec' => $spec
                ];
                return $this -> view('',$arr);
            }

        }else{
            $all = \request() -> all();
            // 判断用户执行的是新增操作还是修改操作
            if(empty($all['id'])){
                // 执行新增操作
//                $spec = $all['specifications'];
//                // 根据获取的id查询规格表中数据库
//                foreach ($spec as $v){
//                    $specdata[] = json_decode(json_encode(DB::table('foods_spec') -> where('id',$v)->select(['name']) -> first()),true);
//                }
//                 将数组转换成字符串
//                $specs = implode(",",array_column($specdata,'name'));
                // 定义一个数组用于接收需要上传数据库的值
                $data = [
                    "merchant_id" => $data -> id,
                    "classification_id" => $all['classification_id'],
                    "name" => $all['name'],
                    "price" => $all['price'],
                    "image" => $all['img'],
//                    "specifications" => $specs,
                    "remark" => $all['remark']
                ];
                // 链接数据库，新增数据
                $i = DB::table("foods_information") -> insert($data);
                if($i){
                    flash('新增成功') -> success();
                    return redirect()->route('foods.information');
                }else{
                    flash('新增失败') -> error();
                    return redirect()->route('foods.information');
                }
            }else{
                // 执行修改操作
                // 获取提交的值
//                $spec = $all['specifications'];
//                // 根据获取的id查询规格表中数据库
//                foreach ($spec as $v){
//                    $specdata[] = json_decode(json_encode(DB::table('foods_spec') -> where('id',$v)->select(['name']) -> first()),true);
//                }
//                // 将数组转换成字符串
//                $specs = implode(",",array_column($specdata,'name'));
                // 定义一个数组用于接收需要上传数据库的值
                $data = [
                    "merchant_id" => $data -> id,
                    "classification_id" => $all['classification_id'],
                    "name" => $all['name'],
                    "price" => $all['price'],
                    "image" => $all['img'],
//                    "specifications" => $specs,
                    "remark" => $all['remark'],
                    "quantitySold" => $all['quantitySold'],
                    "num" => $all['num']
                ];
                // 执行修改操作
                $i = DB::table("foods_information") -> where("id",$all['id']) -> update($data);
                if($i){
                    flash('修改成功') -> success();
                    return redirect()->route('foods.information');
                }else{
                    flash('修改失败') -> error();
                    return redirect()->route('foods.information');
                }
            }

        }

    }

    // 删除菜品信息
    public function informationdel(){
        // 获取传入的数据
        $all = \request()->all();
        // 根据id 删除表中数据
        $i = DB::table("foods_information") -> where("id",$all['id']) -> delete();
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('foods.information');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('foods.information');
        }
    }


    /*
     *      菜品规格
     * */
    // 跳转菜品规格
    public function spec(){
        $all = \request() -> all();
        $id = Auth::id();
        // 判断该用户，是否开店
        $i = DB::table('merchants') -> where("user_id",$id) -> first();
        // 判断是否执行条件查询
        if(!empty($all['name'])){
            // 条件查询
            $where[] = ['merchants.name', 'like', '%'.$all['name'].'%'];
            $name = $all['name'];
        }else{
            // 跳转页面
            $where[] = ['merchants.name', 'like', '%'."".'%'];
            $name = "";
        }

        if(!empty($i)){
            // 如果开店，则能够看到自己的菜品规格
            // 查询数据库数据
            $data = DB::table("foods_spec")
                -> join('merchants','foods_spec.merchant_id','=','merchants.id')
                -> where($where)
                -> where('merchants.id',$id)
                -> select(['foods_spec.id','foods_spec.name as spec_name','merchants.name as merchants_name','foods_spec.price as spec_price'])
                -> paginate(5);
//            print_r($data);die();
        }else{
            // 如果开店，则为超级管理员，能够看见所有的数据
            // 查询数据库数据
            $data = DB::table("foods_spec")
                -> join('merchants','foods_spec.merchant_id','=','merchants.id')
                -> where($where)
                -> select(['foods_spec.id','foods_spec.name as spec_name','merchants.name as merchants_name','foods_spec.price as spec_price'])
                -> paginate(5);
            $id = "";

        }

        return $this -> view('',['data'=>$data,'id'=>$id,'name'=>$name]);
    }
    // 新增 and 修改，菜品规格
    public function specadd(){
        if(\request()->isMethod("get")){
            $all = \request() -> all();
            // 判断跳转新增界面还是修改界面
            if(empty($all['id'])){
                // 跳转新增界面
                return $this -> view('');
            }else{
                // 跳转修改界面
                // 根据当前获取的id，查询数据库中的值
                $data = DB::table("foods_spec") -> where("id",$all['id']) -> first();
                // 定义一个数组用于向前台传输数据
                $arr = [
                    'data'=>$data,
                    'id'=>$all['id']
                ];
                return $this -> view('',$arr);
            }

        }else{
            // 获取传入的值
            $all = \request()->all();
            // 判断用户执行的是新增操作还是修改操作
            if(empty($all['id'])){
                // 执行新增操作
                // 获取当前商户id
                $id = Auth::id();
                // 判断数据库是否存在同样的规格
                $m = DB::table("foods_spec")->where('merchant_id',$id)->where('name',$all['name']) -> first();
                if(empty($m)){
                    // 数据库不存在该值,则进行新增操作
                    // 定义一个数组，用于向数据库添加数据
                    $data = [
                        'merchant_id' => $id,
                        'name' => $all['name'],
                        'price' => $all['price']
                    ];
                    //链接数据库，执行新增操作
                    $i = DB::table("foods_spec") ->insert($data);
                    if($i){
                        flash('新增成功') -> success();
                        return redirect()->route('foods.spec');
                    }else{
                        flash('新增失败') -> error();
                        return redirect()->route('foods.spec');
                    }
                }else{
                    // 数据库存在该值，则提示商户，该规格已存在
                    flash('新增失败，该规格已存在！') -> error();
                    return redirect()->route('foods.spec');
                }

            }else{
                // 执行修改操作

                // 判断数据库是否存在同样的规格
//                print_r($all['spec_id']);die();
                $m = DB::table("foods_spec")->where('id','!=',$all['id'])->where('name',$all['name']) -> first();
                if(empty($m)){

                    // 数据库不存在该值,则进行修改操作
                    // 定义一个数组，用于向数据库添加数据
                    $data = [
                        'name' => $all['name'],
                        'price' => $all['price']
                    ];
                    $i = DB::table("foods_spec") -> where("id",$all['id']) -> update($data);
                    if($i){
                        flash('修改成功') -> success();
                        return redirect()->route('foods.spec');
                    }else{
                        flash('修改失败') -> error();
                        return redirect()->route('foods.spec');
                    }

                }else{
                    // 数据库存在该值，则提示商户，该规格已存在
                    flash('修改失败，该规格已存在！') -> error();
                    return redirect()->route('foods.spec');
                }
            }
        }
    }
    // 删除菜品规格
    public function specdel(){
        // 获取传入的数据
        $all = \request()->all();
        // 根据id 删除表中数据
        $i = DB::table("foods_spec") -> where("id",$all['id']) -> delete();
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('foods.spec');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('foods.spec');
        }
    }



    /**
     *      商户菜品分类
     */
    // 跳转菜品分类模块
    public function index()
    {
        $all = \request() -> all();
        // 判断当前用户是否是酒店用户
        $id = Auth::id();
        // 判断该用户，是否开店
        $i = DB::table('merchants') -> where("user_id",$id)-> where('merchant_type_id',4) -> first();
        // 判断是否执行条件查询
        if(!empty($all['name'])){
            // 条件查询
            $where[] = ['merchants.name', 'like', '%'.$all['name'].'%'];
            $name = $all['name'];
        }else{
            // 跳转页面
            $where[] = ['merchants.name', 'like', '%'."".'%'];
            $name = "";
        }
        if(!empty($i)){
            // 如果是酒店用户，只能看见自己的菜品分类
            $data = DB::table("foods_classification")
                -> join('merchants','foods_classification.merchants_id','=','merchants.id')
                -> where('merchants_id',$i -> id)
                -> where($where)
                -> select(['foods_classification.id','foods_classification.name as class_name','merchants.name as merchants_name'])
                -> paginate(10);
        }else{
            // 查询数据库中商户菜品分类表
            $data = DB::table("foods_classification")
                -> join('merchants','foods_classification.merchants_id','=','merchants.id')
                -> where($where)
                -> select(['foods_classification.id','foods_classification.name as class_name','merchants.name as merchants_name'])
                -> paginate(10);
            $id = "";
        }
        return $this->view('',['data'=>$data,'id'=>$id,'name'=>$name]);
    }

    // 新增菜品分类
    public function add(){
        $all = \request()->all();
        if(\request()->isMethod("get")){
            // 判断是跳转新增界面，跳转修改界面
            if(!empty($all)){
                // 跳转修改界面
                // 获取传入的id
                $id = $all['id'];
                // 链接数据库，根据id查询数据
                $data = DB::table("foods_classification") -> where("id",$id) -> first();
                // 将获取的数据传入前台
                return $this->view('',['data'=>$data]);
            }else{
                // 跳转新增界面
                //获取当前商户id
                $id = Auth::id();
                //跳转新增菜品界面
                return $this -> view('');
            }
        }else{
            // 获取传入的值
            $all = \request()->all();
            $name = $_POST['name'];
            // 判断用户执行的是新增操作还是修改操作
            if(empty($all['id'])){
                // 新增操作
                $id = DB::table('merchants') -> where("user_id",Auth::id()) -> where('merchant_type_id',4) -> first();
                // 判断数据库是否存在同样的菜品分类
                $m = DB::table("foods_classification") -> where("name",$name) -> where('merchants_id',$id -> id) -> first();
                if(empty($m)){
                    // 该分类不存在可以新增
                    // 定义一个数组存放获取的值
                    $data = [
                        "merchants_id" => $id -> id,
                        "name" => $name
                    ];
                    // 链接数据库，新增数据
                    $i = DB::table("foods_classification") -> insert($data);
                    if($i){
                        flash('新增成功') -> success();
                        return redirect()->route('foods.index');
                    }else{
                        flash('新增失败') -> error();
                        return redirect()->route('foods.index');
                    }
                }else{
                    // 该分类已经存在，不能新增
                    flash('新增失败，该分类已经存在！') -> error();
                    return redirect()->route('foods.index');
                }

            }else{
                // 修改操作
                // 判断数据库是否存在同样的菜品分类
                $m = DB::table("foods_classification") -> where("name",$name) -> first();
                if(empty($m)){
                    // 定义一个数组存放获取的值
                    $data = [
                        "name" => $name
                    ];
                    // 链接数据库，执行修改操作
                    $i = DB::table("foods_classification") -> where("id",$all['id']) -> update($data);
                    if($i){
                        flash('修改成功') -> success();
                        return redirect()->route('foods.index');
                    }else{
                        flash('修改失败') -> error();
                        return redirect()->route('foods.index');
                    }
                }else{
                    flash('修改失败，该分类已经存在！') -> error();
                    return redirect()->route('foods.index');
                }

            }

        }
    }

    // 删除菜品分类
    public function del(){
        // 获取传入的id
        $id = $_GET['id'];
        // 根据id 删除表中数据
        $i = DB::table("foods_classification")->where('id',$id)->delete();
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('foods.index');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('foods.index');
        }
    }

    //饭店分类管理
    public function classification(){
        $id = Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            $data = DB::table('hotel_category')
                ->where('status','=','1')
                ->where('type_id','=','2')
                ->orderBy('sort','asc')
                ->get();
        }else{
            // 查询酒店分类表
            $data = DB::table('hotel_category')
                ->where('status','=','1')
                ->where('type_id','=','2')
                ->orderBy('sort','asc')
                ->get();
        }
        $data = json_decode(json_encode($data),true);
        return $this->view('',['list' => $data]);
    }

    //饭店分类添加
    public function classAdd ()
    {
        return $this->view('classAdd');
    }

    //添加饭店分类
    public function addClass(Request $request){
        $validate = Validator::make($request->all(),[
            'name' => 'required',
            'sort' => 'required|numeric',
            'img' => 'required',
        ],[
            'name.required'=>'名称必须',
            'sort.numeric'=>'排序必须是数字',
            'img.required'=>'请上传图片',
        ]);


        if ($validate->fails()) {
            flash($validate->errors()->first())->error()->important();
            return redirect()->route('hotel.classAdd');
        }
        $data["name"] = $request->input('name');
        $data["img"] = $request->input('img');
        $data["sort"] = $request->input('sort');
        $data['type_id'] = 2;
        $data['created_at'] = date('Y-m-d H:i:s');
        $i = DB::table('hotel_category') -> insert($data);
        if ($i){
            return   redirect()->route('foods.classAdd');
        }else{
            return  viewError('操作失败','foods.classAdd');
        }

    }

    //饭店分类删除
    public function classDel(Request $request,$id){
        $i = DB::table('hotel_category')->delete($id);
        if ($i){
            return redirect()->route('foods.classification');
        }
        return viewError('已删除或者删除失败');
    }

    //饭店分类修改
    public function classEdit(Request $request,$id){
        $list = DB::table('hotel_category')
            ->where('status','=','1')
            ->where('id','=',"$id")
            ->first();
        return $this->view('classEdit',['list'=>$list]);
    }

    //修改饭店分类
    public function editClass(Request $request){
        $validate = Validator::make($request->all(),[
            'name' => 'required',
            'sort' => 'required|numeric',
            'img' => 'required',
        ],[
            'name.required'=>'名称必须',
            'sort.numeric'=>'排序必须是数字',
            'img.required'=>'请上传图片',
        ]);

        if ($validate->fails()) {
            flash($validate->errors()->first())->error()->important();
            return redirect()->route('foods.editClass');
        }
        $id = $request->input('id');
        $data['name'] = $request->input('name');
        $data['img'] = $request->input('img');
        $data['sort'] = $request->input('sort');
        $i = DB::table('hotel_category')
            ->where('id','=',"$id")
            ->update($data);
        if ($i) {
            return   redirect()->route('foods.classification');
        }
        return  viewError('操作失败','foods.classification');
    }


}