<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\HotelRequest;
use App\Services\ActionLogsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\HotelService;
use App\Repositories\HotelRepository;
use App\Http\Controllers\Controller;
use Auth;

class HotelController extends BaseController
{
    /*
     *      确认退款
     * */
    public function return_money(){
        $all = \request() -> all();
        // 获取订单数据
        $books_data = DB::table('books') -> where('id',$all['id']) -> first();
        // 获取用户信息
        $user_data = DB::table('users') -> where('id',$books_data -> user_id) -> first();
        // 判断用户支付方式
        if($books_data -> pay_way == 4){    // 余额支付
            DB::beginTransaction();
            try{
                $money = $user_data -> money + $books_data -> money;
                $i = DB::table('users') -> where('id',$books_data -> user_id) -> update(['money' => $money]);
                // 修改副表订单状态
                $m = DB::table('books') -> where('id',$all['id']) -> update(['status'=>70]);
                // 修改主表订单状态
                $j = DB::table('orders') -> where('order_sn',$books_data -> book_sn) -> first();
                if(empty($j)){
                    DB::rollBack();
                    flash('退款失败，订单表中未查询到相关订单') -> error();
                    return redirect()->route('hotel.books');
                }
                DB::table('orders') -> where('order_sn',$books_data -> book_sn) -> update(['status'=>70]);
                if ($i) {
                    DB::commit();
                    flash('退款成功') -> success();
                    return redirect()->route('hotel.books');
                }else{
                    DB::rollBack();
                    flash('退款失败，请稍后重试') -> error();
                    return redirect()->route('hotel.books');
                }
            }catch (\Exception $exception){
                DB::rollBack();
                flash('退款失败，请稍后重试') -> error();
                return redirect()->route('hotel.books');
            }

        }else if($books_data -> pay_way == 1){
            // 微信退款
            require_once base_path()."/wxpay/lib/WxPay.Api.php";
            require_once base_path()."/wxpay/example/WxPay.NativePay.php";
            //查询订单,根据订单里边的数据进行退款
            $order = json_decode(json_encode(DB::table('books') -> where('id',$all['id']) -> first()),true);
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
                //退款成功
                DB::beginTransaction();
                try{
                    // 修改副表订单状态
                    $m = DB::table('books') -> where('id',$all['id']) -> update(['status'=>70]);
                    // 修改主表订单状态
                    $j = DB::table('orders') -> where('order_sn',$books_data -> book_sn) -> first();
                    if(empty($j)){
                        DB::rollBack();
                        flash('退款失败，订单表中未查询到相关订单') -> error();
                        return redirect()->route('hotel.books');
                    }
                    DB::table('orders') -> where('order_sn',$books_data -> book_sn) -> update(['status'=>70]);
                    DB::commit();
                    flash('退款成功') -> success();
                    return redirect()->route('hotel.books');
                }catch (\Exception $exception){
                    DB::rollBack();
                    flash('退款失败，请稍后重试') -> error();
                    return redirect()->route('hotel.books');
                }
            }else if(($result['return_code']=='FAIL') || ($result['result_code']=='FAIL')){
                //退款失败
                //原因
                $reason = (empty($result['err_code_des'])?$result['return_msg']:$result['err_code_des']);
                flash($reason) -> error();
                return redirect()->route('hotel.books');
            }else{
                //失败
                flash("退款失败请稍后重试") -> error();
                return redirect()->route('hotel.books');
            }
        }
    }

    /*
     *      核销用户状态
     * */
    public function write_off(){
        $all = \request() -> all();
        // 获取当前订单的状态
        $books_data = DB::table('books') -> where('id',$all['id']) -> first();
        // 如果当前处于待入住状态，则将其更改未已入住状态
        if($books_data -> status == 20){
            $data = [
                'status' => 30
            ];
        }else if($books_data -> status == 30){  // 如果当前状态处于已入住状态 则将其更改未已完成
            $data = [
                'status' => 40
            ];
        }
        $i = DB::table('books') -> where('id',$all['id']) -> update($data);
        if ($i) {
            flash('用户核销成功') -> success();
            return redirect()->route('hotel.books');
        }else{
            flash('用户核销失败，请稍后重试') -> error();
            return redirect()->route('hotel.books');
        }
    }

    /*
     *      酒店分类
     * */
    public function classification(){
        $id = Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            $data = DB::table('hotel_category')
                ->where('status','=','1')
                ->where('type_id','=','1')
                ->orderBy('sort','asc')
                ->get();
        }else{
            // 查询酒店分类表
            $data = DB::table('hotel_category')
                ->where('status','=','1')
                ->where('type_id','=','1')
                ->orderBy('sort','asc')
                ->get();
        }
        $data = json_decode(json_encode($data),true);
        return $this->view('',['list' => $data]);
    }

    /*
     *      酒店分类添加
     * */
    public function classAdd ()
    {
        return $this->view('classAdd');
    }

    /*
     *      添加酒店分类
     * */
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
        $data['type_id'] = 1;
        $data['created_at'] = date('Y-m-d H:i:s');
        $i = DB::table('hotel_category') -> insert($data);
        if ($i){
            return   redirect()->route('hotel.classAdd');
        }else{
            return  viewError('操作失败','hotel.classAdd');
        }

    }
    /*
     *      酒店分类修改
     * */
    public function classEdit(Request $request,$id){
        $list = DB::table('hotel_category')
            ->where('status','=','1')
            ->where('id','=',"$id")
            ->first();
        return $this->view('classEdit',['list'=>$list]);
    }

    /*
     *      修改酒店分类
     * */
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
            return redirect()->route('hotel.classEdit');
        }
        $id = $request->input('id');
        $data['name'] = $request->input('name');
        $data['img'] = $request->input('img');
        $data['sort'] = $request->input('sort');
        $i = DB::table('hotel_category')
            ->where('id','=',"$id")
            ->update($data);
        if ($i) {
            return   redirect()->route('hotel.classification');
        }
        return  viewError('操作失败','hotel.classification');
    }

    /*
     *      删除酒店分类
     * */
    public function classDel(Request $request,$id){
        $i = DB::table('hotel_category')->delete($id);
        if ($i){
            return redirect()->route('hotel.classification');
        }
        return viewError('已删除或者删除失败');
    }


    /*
     *      酒店评论
     * */
    public function commnets(){
        $id = Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            // 查询，酒店评论
            $data = DB::table('order_commnets')
                -> join('users','order_commnets.user_id','=','users.id')     // 链接用户表
                -> join('hotel_room','order_commnets.goods_id','=','hotel_room.id')     // 链接商品表
                -> where('type',1)
                -> where('merchants_id',$id)
                -> where('order_commnets.is_del',0)
                -> select(['order_commnets.id','users.name as username','hotel_room.house_name as goodsname','stars','order_commnets.content','order_commnets.created_at'])
                -> paginate(10);
        }else{
            // 查询，酒店评论
            $data = DB::table('order_commnets')
                -> join('users','order_commnets.user_id','=','users.id')     // 链接用户表
                -> join('hotel_room','order_commnets.goods_id','=','hotel_room.id')     // 链接商品表
                -> where('type',1)
                -> where('order_commnets.is_del',0)
                -> select(['order_commnets.id','users.name as username','hotel_room.house_name as goodsname','stars','order_commnets.content','order_commnets.created_at'])
                -> paginate(10);
        }
        return $this->view('',['data' => $data]);
    }
    // 新增酒店评论
    public function commnetsAdd(){
        $id = Auth::id();
        if(\request() -> isMethod("get")){
            // 查询商品列表
            $goodsData = DB::table("hotel_room") -> get();
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
                'type' => 1,
                'merchants_id' => $id,
                'stars' => $all['stars'],
                'content' => $all['content'],
                'created_at' => date("Y-m-d H:i:s")
            ];
            // 链接数据库，新增内容
            $i = DB::table('order_commnets') -> insert($data);
            if($i){
                flash('新增成功') -> success();
                return redirect()->route('hotel.commnets');
            }else{
                flash('新增失败') -> error();
                return redirect()->route('hotel.commnets');
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
            return redirect()->route('hotel.commnets');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('hotel.commnets');
        }
    }

    /**房间列表
     * [index description]
     * @return [type] [description]
     */
    public function index ()
    {   $all = request()->all();
        $admin = Auth::guard('admin')->user();
        $where = [];
        if (!empty($all['merchant_id'])) {
            $where[]=['merchant_id',$all['merchant_id']];
        }
        $user_id=$admin->id;
        $datas=Db::table('merchants')->where('user_id',$user_id)->first();
        if (!empty($datas)) {
            $role=$datas->id;
            $where[]=['merchant_id',$datas->id];
        }else{
            $role=0;
        }
        $data=Db::table('hotel_room')->where($where)->paginate(20);
        foreach ($data as $key => $value) {
            $data[$key]->merchant_id=Db::table('merchants')->where('id',$value->merchant_id)->pluck('name')[0];
            $data[$key]->user_id=Db::table('users')->where('id',$value->user_id)->pluck('name')[0];
        }
        return $this->view('',['data'=>$data],['role'=>$role]);
    }
    /**事务测试
     * [add description]
     * @param string $value [description]
     */
    public function text()
    {
        DB::beginTransaction(); //开启事务
        $data['status']=0;
        $re=DB::table('users')->where('id','1')->update($data);
        $re2 =DB::table('users')->where('id','2')->update($data);
        if ($re&&$re2) {
            DB::commit();
            echo 1;
        }else{
            DB::rollback();
            echo 0;
        }
    }
    /**添加修改房间
     * [add description]
     * @param string $value [description]
     */
    public function add($value='',Request $request)
    {
        $all = request()->all();
        $admin = Auth::guard('admin')->user();
        if (request()->isMethod('post')) {
            $save['house_name']=$all['house_name'];
            if (!empty($all['desc'])) {
                $save['desc']=implode(',',$all['desc']);
            }
            $save['price']=$all['price'];
            $save['updated_at']=date('Y-m-d H:i:s',time());
            $data['areas']=$all['areas'];
            $data['has_window']=$all['has_window'];
            $data['wifi']=$all['wifi'];
            $data['num']=$all['num'];
            $data['num_people']=$all['num_people'];
            $data['has_breakfast']=$all['has_breakfast'];
            $data['bed_type']=$all['bed_type'];
            $data['other_sets']=$all['other_sets'];
            // 判断是否上传新文件
            $choose_file = $_FILES['choose-file'];
            // 如果第一个文件为空，则未上传新文件
            if($choose_file['name'][0] == ""){
                // 判断是否传值
                $validate = Validator::make($request->all(),[
                    'choose_file'=>'required'
                ],[
                    'choose_file.required'=>'缺少房间图片'
                ]);

                if ($validate->fails()) {
                    flash($validate->errors()->first())->error()->important();
                    return redirect()->route('hotel.index');
                }
                // 如果未上传新文件，则获取当前文件内容
                $album = json_encode($all['choose_file']);
            }else{
                // 如果上传了文件
                //判断保存文件的路径是否存在
                $dir = $_SERVER['DOCUMENT_ROOT']."/shop/shopImage/";
                // 如果文件不存在，则创建
                if (!is_dir($dir)) {
                    mkdir($dir,0777,true);
                }
                // 声明支持的文件类型
                $types = array("png", "jpg", "webp", "jpeg", "gif");
                // 执行文件上传操作
                for ($i = 0; $i < count($choose_file['name']); $i++) {
                    //在循环中取得每次要上传的文件名
                    $name = $choose_file['name'][$i];
                    // 将上传的文件名，分割成数组
                    $end = explode(".", $name);
                    //在循环中取得每次要上传的文件类型
                    $type = strtolower(end($end));
                    // 判断上传的文件是否正确
                    if (!in_array($type, $types)) {
                        return '第'.($i + 1).'个文件类型错误';
                    } else {
                        //在循环中取得每次要上传的文件的错误情况
                        $error = $choose_file['error'][$i];
                        if ($error != 0) {
                            flash("第" . ($i + 1) . "个文件上传错误") -> error();
                            return redirect()->route('hotel.index');
                        } else {
                            //在循环中取得每次要上传的文件的临时文件
                            $tmp_name = $choose_file['tmp_name'][$i];
                            if (!is_uploaded_file($tmp_name)) {
                                return "第" . ($i + 1) . "个临时文件错误";
                            } else {
                                // 给上传的文件重命名
                                $newname = $dir.date("YmdHis") . rand(1, 10000) . "." . $type;
                                $img_array[$i] = substr($newname,strpos($newname,'/shop/shopImage/'));
                                //对文件执行上传操作
                                if (!move_uploaded_file($tmp_name, $newname)) {
                                    return "第" . ($i + 1) . "个文件上传失败";
                                }
                            }
                        }
                    }
                }
                // 获取上传的图片路径
                $img_array = json_encode($img_array);
                if(empty($all['choose_file'])){
                    $al = "";
                }else{
                    $al = json_encode($all['choose_file']);
                }
                // 查询原来的值是否删除
                $album = $img_array.$al;
            }
            if (empty($all['id'])) {
                $save['user_id']=$admin->id;
                $save['merchant_id']=Db::table('merchants')->where('user_id',$admin->id)->pluck('id')[0];
                $save['status']=0;
                $save['img']=$album;
                $save['created_at']=date('Y-m-d H:i:s',time());
                $ids=Db::table('hotel_room')->insertGetId($save);
                $data['hotel_room_id']=$ids;
                $re=Db::table('hotel_attr_value')->insertGetId($data);
            }else{
                $save['img']=$album;
                $re=Db::table('hotel_room')->where('id',$all['id'])->update($save);
                $res=Db::table('hotel_attr_value')->where('hotel_room_id',$all['id'])->update($data);
            }
            if ($re || $res) {
                flash("新增成功") -> success();
                return redirect()->route('hotel.index');
            }else{
                flash("新增失败") -> error();
                return redirect()->route('hotel.index');
            }
        }else{
            if (empty($all['id'])) {
                $data = (object)[];
                $data->desc=array();
                $data->has_breakfast=1;
                $data->wifi=1;
                $data->has_window=1;
                $data->img=array();
            }else{
                $data=Db::table('hotel_room as hr')
                    ->select('hr.*','hv.id as tid','hv.hotel_room_id','hv.areas','hv.has_window','hv.wifi',
                        'hv.num','hv.num_people','hv.has_breakfast','hv.bed_type','hv.other_sets')
                    ->join('hotel_attr_value as hv','hr.id','=','hv.hotel_room_id')
                    ->where('hr.id',$all['id'])
                    ->first();
                if (empty($data->desc)) {
                    $data->desc=array();
                }else{
                    $data->desc=explode(',',$data->desc);
                    $data->img=json_decode($data->img);
                }
            }

            $desc=Db::table('hotel_faci')->get();
            return $this->view('',['data'=>$data],['desc'=>$desc]);
        }
    }
    /**删除房间
     * [del description]
     * @param string $value [description]
     */
    public function del()
    {
        $all = request()->all();
        $id=$all['id'];
        $re=Db::table('hotel_room')->where('id',$id)->delete();
        $res=Db::table('hotel_attr_value')->where('hotel_room_id',$id)->delete();
        flash('删除成功')->success();
        return redirect()->route('hotel.index');
    }
    /**删除配置
     * [faciDel description]
     * @param string $value [description]
     */
    public function faciDel()
    {
        $all = request()->all();
        $id=$all['id'];
        $where[]=['desc', 'like', '%'.$id.'%'];
        $res=Db::table('hotel_room')->where($where)->first();
        if (!empty($res)) {
            flash('该配置有房间使用不能删除')->error();
            return redirect()->route('hotel.faci');
        }
        $re=Db::table('hotel_faci')->where('id',$id)->delete();
        // $res=Db::table('hotel_attr_value')->where('hotel_room_id',$id)->delete();
        flash('删除成功')->success();
        return redirect()->route('hotel.faci');
    }
    /**修改房间状态
     * [status description]
     * @return [type] [description]
     */
    public function status()
    {
        $all = request()->all();
        $save['status']=$all['status'];
        $id=$all['id'];
        $re=Db::table('hotel_room')->where('id',$id)->update($save);
        if ($re) {
            flash('修改成功')->success();
            return redirect()->route('hotel.index');
        }else{
            flash('修改失败')->error();
            return redirect()->route('hotel.index');
        }
    }
    public function faci()
    {
        $data=Db::table('hotel_faci')
            ->join('merchants','hotel_faci.merchant_id','=','merchants.id')
            ->select('hotel_faci.id','hotel_faci.name','merchants.name as nickname')
            ->paginate(20);
        return $this->view('',['data'=>$data]);
    }
    /**新增修改酒店配置
     * [faciAdd description]
     * @return [type] [description]
     */
    public function faciAdd()
    {
        $all = request()->all();
        $user_id = Auth::id();
        $arr = DB::Table('merchants')->where('user_id',$user_id)->where('merchant_type_id',3)->first();
        if (request()->isMethod('post')) {
            $save=['name'=>$all['name'],'merchant_id'=>$arr['id']];
            if (empty($all['id'])) {
                $re=Db::table('hotel_faci')->insert($save);
            }else{
                $re=Db::table('hotel_faci')->where('id',$all['id'])->update($save);
            }
            if ($re) {
                flash('编辑成功')->success();
                return redirect()->route('hotel.faci');
            }else{
                flash('编辑失败')->error();
                return redirect()->route('hotel.faci');
            }
        }else{
            if (empty($all['id'])) {
                $data = (object)[];
                $data->type_name='';
            }else{
                $data=Db::table('hotel_faci')->where('id',$all['id'])->first();
            }
            return $this->view('',['data'=>$data]);
        }
    }
    /**酒店预定列表
     * [books description]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function books($value='')
    {
        $all = \request() -> all();
        $admin = Auth::guard('admin')->user();
        $user_id=$admin->id;
        $datas=Db::table('merchants')->where('user_id',$user_id) -> where('user_id','!=','1') ->first();

        $where=[];
        if (!empty($datas)) {
            $wheres['role']=$datas->id;
            $where[]=['merchant_id',$datas->id];
            // $data=Db::table('books')->where('merchant_id',$datas->id)->paginate(20);
        }else{
            $wheres['role']=0;
        }
        // 判断条件查询
        if(!empty($all['status'])){
            $status = $all['status'];
            if($all['status'] == 20){            // 待入住
                $where[] = ['status',20];
            }elseif ($all['status'] == 30){      // 已入住
                $where[] = ['status',30];
            }
        }else{
            $status = 0;
        }
        if (!empty($all['merchant_id'])) {
            $where[]=['merchant_id',$all['merchant_id']];
            $wheres['merchant_id']=$all['merchant_id'];
        }
        if(!empty($all['book_sn'])){
            $wheres['book_sn']=$all['book_sn'];
            $data=Db::table('books')
                -> where('book_sn','like','%'.$all['book_sn'].'%')
                -> where('status','!=',10)
                ->orderBy('pay_time','desc')
                ->paginate(10);
            if(count($data) == 0){
                $data=Db::table('books')
                    -> where('real_name','like','%'.$all['book_sn'].'%')
                    -> where('status','!=',10)
                    ->orderBy('pay_time','desc')
                    ->paginate(10);
                if(count($data) == 0){
                    $data=Db::table('books')
                        -> where('mobile','like','%'.$all['book_sn'].'%')
                        -> where('status','!=',10)
                        -> orderBy('pay_time','desc')
                        ->paginate(10);
                }
            }
        }else{
            $data=Db::table('books')
                ->where($where)
                -> where('status','!=',10)
                ->orderBy('pay_time','desc')
                ->paginate(10);
        }
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $user=Db::table('users')->where('id',$value->user_id)->first();
                if (!empty($user)) {
                    $data[$key]->user_id=$user->name;
                }else{
                    $data[$key]->user_id='用户不存在';
                }
                $merchants=Db::table('merchants')->where('id',$value->merchant_id)->first();
                if (!empty($merchants)) {
                    $data[$key]->merchant_id=$merchants->name;
                }else{
                    $data[$key]->merchant_id='商户不存在';
                }
                $hotel=Db::table('hotel_room')->where('id',$value->hotel_room_id)->first();
                if (!empty($hotel)) {
                    $data[$key]->hotel_room_id=$hotel->house_name;
                }else{
                    $data[$key]->hotel_room_id='房间不存在';
                }
            }
        }
        return $this->view('',['data'=>$data,'status'=>$status],['wheres'=>$wheres]);
    }
    // 跳转酒店商户
    public function merchant()
    {
        $all = request()->all();
        $id = \Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            $where[]=['id','>','0'];
            $where[]=['merchant_type_id',3];
            $screen['merchant_type_id'] = 3;
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
            $where[]=['merchant_type_id',3];
            $screen['merchant_type_id'] = 3;
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
    public function hotelStatus(){
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
            return redirect()->route('hotel.merchant');
        }else{
            flash("状态更新失败") -> error();
            return redirect()->route('hotel.merchant');
        }
    }

    //环境设施
    public function  decoration(){
        $id = Auth::id();     // 当前登录用户的id
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('id',$id)
            -> where('is_reg',1)
            -> first();
        if ($i){
            $list = DB::table("merchants")
                ->where('id',$id)
                ->first(['facilities','goods_img','id']);
            $data = json_decode($list->facilities);
            return $this->view('decoration',['list'=>$list,'id'=>$list->id,'data'=>$data,'i'=>$i]);
        }else{
            return $this->view('decoration');
        }

    }

    //新增&修改环境设施
    public function addDecoration(Request $request)
    {
        $input = $request->all();
        if(!empty($input['id'])){
            //修改
            $choose_file = $_FILES['choose-file'];
            // 如果第一个文件为空，则未上传新文件
            if($choose_file['name'][0] == ""){
                // 判断是否传值
                $validate = Validator::make($request->all(),[
                    'choose_file'=>'required'
                ],[
                    'choose_file.required'=>'缺少环境设施图片'
                ]);

                if ($validate->fails()) {
                    flash($validate->errors()->first())->error()->important();
                    return redirect()->route('hotel.decoration');
                }
                // 如果未上传新文件，则获取当前文件内容
                $album = json_encode($input['choose_file']);
            }else{
                // 如果上传了文件
                //判断保存文件的路径是否存在
                $dir = $_SERVER['DOCUMENT_ROOT']."/shop/shopImage/";
                // 如果文件不存在，则创建
                if (!is_dir($dir)) {
                    mkdir($dir,0777,true);
                }
                // 声明支持的文件类型
                $types = array("png", "jpg", "webp", "jpeg", "gif");
                // 执行文件上传操作
                for ($i = 0; $i < count($choose_file['name']); $i++) {
                    //在循环中取得每次要上传的文件名
                    $name = $choose_file['name'][$i];
                    // 将上传的文件名，分割成数组
                    $end = explode(".", $name);
                    //在循环中取得每次要上传的文件类型
                    $type = strtolower(end($end));
                    // 判断上传的文件是否正确
                    if (!in_array($type, $types)) {
                        return '第'.($i + 1).'个文件类型错误';
                    } else {
                        //在循环中取得每次要上传的文件的错误情况
                        $error = $choose_file['error'][$i];
                        if ($error != 0) {
                            flash("第" . ($i + 1) . "个文件上传错误") -> error();
                            return redirect()->route('hotel.decoration');
                        } else {
                            //在循环中取得每次要上传的文件的临时文件
                            $tmp_name = $choose_file['tmp_name'][$i];
                            if (!is_uploaded_file($tmp_name)) {
                                return "第" . ($i + 1) . "个临时文件错误";
                            } else {
                                // 给上传的文件重命名
                                $newname = $dir.date("YmdHis") . rand(1, 10000) . "." . $type;
                                $img_array[$i] = substr($newname,strpos($newname,'/shop/shopImage/'));
                                //对文件执行上传操作
                                if (!move_uploaded_file($tmp_name, $newname)) {
                                    return "第" . ($i + 1) . "个文件上传失败";
                                }
                            }
                        }
                    }
                }
                // 获取上传的图片路径
                $img_array = json_encode($img_array);
                if(empty($all['choose_file'])){
                    $al = "";
                }else{
                    $al = json_encode($input['choose_file']);
                }
                // 查询原来的值是否删除
                $album = $img_array.$al;
            }
            $updData= DB::table("merchants")->where('id',$input['id'])->update(['facilities'=>$album]);
            if ($updData){
                flash('修改成功') -> success();
                return redirect()->route('hotel.decoration');
            }else{
                flash('修改失败') -> error();
                return redirect()->route('hotel.decoration');
            }
        }else{
            $choose_file = $_FILES['choose-file'];
            if ($choose_file['name'][0] == "") {
                flash("请选择环境设施图片") -> error();
                return redirect()->route('shop.create');
            }
            // 判断保存文件的路径是否存在
            $dir = $_SERVER['DOCUMENT_ROOT']."/shop/shopImage/";
            // 如果文件不存在，则创建
            if (!is_dir($dir)) {
                mkdir($dir,0777,true);
            }
            // 声明支持的文件类型
            $types = array("png", "jpg", "webp", "jpeg", "gif");
            // 执行文件上传操作
            for ($i = 0; $i < count($choose_file['name']); $i++) {
                //在循环中取得每次要上传的文件名
                $name = $choose_file['name'][$i];
                // 将上传的文件名，分割成数组
                $end = explode(".", $name);
                //在循环中取得每次要上传的文件类型
                $type = strtolower(end($end));
                // 判断上传的文件是否正确
                if (!in_array($type, $types)) {
                    return '第'.($i + 1).'个文件类型错误';
                } else {
                    //在循环中取得每次要上传的文件的错误情况
                    $error = $choose_file['error'][$i];
                    if ($error != 0) {
                        flash("第" . ($i + 1) . "个文件上传错误") -> error();
                        return redirect()->route('shop.create');
                    } else {
                        //在循环中取得每次要上传的文件的临时文件
                        $tmp_name = $choose_file['tmp_name'][$i];
                        if (!is_uploaded_file($tmp_name)) {
                            return "第" . ($i + 1) . "个临时文件错误";
                        } else {
                            // 给上传的文件重命名
                            $newname = $dir.date("YmdHis") . rand(1, 10000) . "." . $type;
                            $img_array[$i] = substr($newname,strpos($newname,'/shop/shopImage/'));
                            //对文件执行上传操作
                            if (!move_uploaded_file($tmp_name, $newname)) {
                                return "第" . ($i + 1) . "个文件上传失败";
                            }
                        }
                    }
                }
            }
            $img_array = json_encode($img_array);
            $addData= DB::table("merchants")->insert(['facilities'=>$img_array]);
            if ($addData){
                flash('新增成功') -> success();
                return redirect()->route('hotel.decoration');
            }else{
                flash('新增失败') -> error();
                return redirect()->route('hotel.decoration');
            }
        }


    }


//    public function
}
