<?php

namespace App\Http\Controllers\Admin;

use App\Models\District;
use App\Models\ExpressAttr;
use App\Models\ExpressModel;
use App\Models\GoodsType;
use App\Models\Ogoods;
use App\Models\Order;
use App\Models\Orders;
use App\Models\Statics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Handlers\Tree;
use App\Models\GoodBrands;
use App\Models\Goods;
use App\Models\GoodsAttr;
use App\Models\GoodsAttrValue;
use App\Models\GoodsCate;
use App\Models\Merchant;
use App\Libraires\ApiResponse;
use Auth;


class ShopController extends BaseController
{
    // 排序
    public function sort(){
        $all = \request() -> all();
        // 判断是按照什么排序 1销量 2价格
        if($all['id'] == 1){    // 销量
            $sort = "volume";
        }else if($all['id'] == 2){ // 价格
            $sort = "price";
        }
        $id = Auth::id();     // 当前登录用户的id
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('is_reg',1)
            -> select('id')
            -> first();
        if(!empty($all['status']))
        {
            if($all['status'] == 2){
                $where[] = ['goods.is_sale',0];
            }else{
                $where[] = ['goods.is_sale',$all['status']];
            }
        }
        $where[] = ['goods.is_del',0];
        // 如果当前用户是商家，则查询当前商户的商品
        if($i){
            $goods = DB::table('goods')
                -> join('merchants','goods.merchant_id','=','merchants.id')
                -> where('goods.merchant_id',$i -> id)
                -> where($where)
                -> orderBy($sort,'desc')
                -> select(['merchants.name as merchant_name','goods.id','goods.pv','goods.created_at','goods.updated_at',
                    'goods.goods_cate_id','goods.name as goods_name','goods.img','goods.desc','goods.is_hot','goods.is_recommend','goods.is_sale',
                    'goods.is_bargain','goods.dilivery','goods.volume','goods.price'])
                -> paginate(10);
            foreach ($goods as $k => $v){
                $goods_cate_id  = explode(',',$v->goods_cate_id);
                unset($goods_cate_id[0]);
                array_pop($goods_cate_id);
                $name=[];
                foreach ($goods_cate_id as $item) {
                    $name[]=Db::table('goods_cate')->select('name')->where('id',$item)->first()->name ?? '';
                }
                $goods[$k]->goods_cate_id=implode(',',$name);
            }
        }else{
            $goods = DB::table('goods')
                -> join('merchants','goods.merchant_id','=','merchants.id')
                -> where($where)
                -> orderBy($sort,'desc')
                -> select(['merchants.name as merchant_name','goods.id','goods.name as goods_name','goods.pv',
                    'goods.created_at','goods.name as goods_name','goods.updated_at','goods.goods_cate_id','goods.img','goods.desc','goods.is_hot',
                    'goods.is_recommend','goods.is_sale','goods.is_bargain','goods.dilivery','goods.volume','goods.price'])
                -> paginate(10);
            foreach ($goods as $k => $v){
                $goods_cate_id  = explode(',',$v->goods_cate_id);
                unset($goods_cate_id[0]);
                array_pop($goods_cate_id);
                $name=[];
                foreach ($goods_cate_id as $item) {
                    $name[]=Db::table('goods_cate')->select('name')->where('id',$item)->first()->name ?? '';
                }
                $goods[$k]->goods_cate_id=implode(',',$name);
            }
        }
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            // 链接数据库，查询商户的商品分类
            $datas = DB::table('merchants_goods_type')
                -> join('merchants','merchants_goods_type.merchant_id','=','merchants.id')
                -> where('is_del',1)
                -> where('merchants_goods_type,merchant_id',$i -> id)
                -> select('merchants_goods_type.id','merchants.name as merchants_name','merchants_goods_type.name as name','pid','num')
                -> get();
        }else{
            // 链接数据库，查询商户的商品分类
            $datas = DB::table('merchants_goods_type')
                -> join('merchants','merchants_goods_type.merchant_id','=','merchants.id')
                -> where('is_del',1)
                -> select('merchants_goods_type.id','merchants.name as merchants_name','merchants_goods_type.name as name','pid','num')
                -> get();
        }
        $data = Tree::tree(json_decode(json_encode($datas),true),'name','id','pid');
        $goods_sku = DB::select("select goods_id,SUM(store_num) as total from `goods_sku` group by `goods_id`");
        return $this->view('goods',['list'=>$goods,'data'=>$data,'goods_sku'=>json_decode(json_encode($goods_sku),true)]);
    }
    // 批量删除商品
    public function deleteAll(){
        $all = \request() -> all();
        DB::beginTransaction();
        try{
            $data = [
                'is_del' => 1
            ];
            // 循环删除数据
            foreach ($all['ids'] as $id){
                DB::table('goods') -> where('id',$id) -> update($data);
            }
            DB::commit();
            return 1;
        }catch (\Exception $e){
            DB::rollBack();
        }
    }

    // 商城商家管理
    public function shopMerchant(){
        $all = request()->all();
        $id = \Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            $where[]=['id','>','0'];
            if (!empty($all['merchant_type_id'])) {
                $where[]=['merchant_type_id',$all['merchant_type_id']];
                $screen['merchant_type_id'] = $all['merchant_type_id'];
            }else{
                $screen['merchant_type_id']='';
            }
            if (!empty($all['name'])) {
                $where[]=['name', 'like', '%'.$all['name'].'%'];
                $screen['name']=$all['name'];
            }else{
                $screen['name']='';
            }
            $data=DB::table('merchants')
                -> where('user_id',$id)
                -> where($where)
                -> where('is_reg',1)
                -> orWhere('is_reg',2)
                -> orderBy('created_at','desc')
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
            if (!empty($all['merchant_type_id'])) {
                $where[]=['merchant_type_id',$all['merchant_type_id']];
                $screen['merchant_type_id'] = $all['merchant_type_id'];
            }else{
                $screen['merchant_type_id']='';
            }
            if (!empty($all['name'])) {
                $where[]=['name', 'like', '%'.$all['name'].'%'];
                $screen['name']=$all['name'];
            }else{
                $screen['name']='';
            }
            $data=DB::table('merchants')
                ->where($where)
                -> where('is_reg',1)
                -> orWhere('is_reg',2)
                -> orderBy('created_at','desc')
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
        return $this->view('',['data'=>$data,'i'=>$i],['wheres'=>$wheres]);
    }
    // 待审核商家
    public function waitExamine(){
        $all = request()->all();
        $id = \Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            $where[]=['id','>','0'];
            if (!empty($all['merchant_type_id'])) {
                $where[]=['merchant_type_id',$all['merchant_type_id']];
                $screen['merchant_type_id'] = $all['merchant_type_id'];
            }else{
                $screen['merchant_type_id']='';
            }
            if (!empty($all['name'])) {
                $where[]=['name', 'like', '%'.$all['name'].'%'];
                $screen['name']=$all['name'];
            }else{
                $screen['name']='';
            }
            $data=DB::table('merchants')
                -> where('user_id',$id)
                -> where('is_reg',0)
                -> where($where)
                -> orderBy('created_at','desc')
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
            if (!empty($all['merchant_type_id'])) {
                $where[]=['merchant_type_id',$all['merchant_type_id']];
                $screen['merchant_type_id'] = $all['merchant_type_id'];
            }else{
                $screen['merchant_type_id']='';
            }
            if (!empty($all['name'])) {
                $where[]=['name', 'like', '%'.$all['name'].'%'];
                $screen['name']=$all['name'];
            }else{
                $screen['name']='';
            }
            $data=DB::table('merchants')
                ->where('is_reg',0)
                ->where($where)
                -> orderBy('created_at','desc')
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
        return $this->view('shopMerchant',['data'=>$data,'i'=>$i],['wheres'=>$wheres]);
    }

    // 跳转订单管理
    public function shopMerchantOrder(){
        $all = \request() -> all();
        $list = DB::table('orders')
            -> join('users','orders.user_id','=','users.id')
            -> where('orders.is_del',0)
            -> where('orders.user_id',$all['id'])
            -> select(['orders.id','orders.order_sn','orders.pay_way','orders.pay_money','orders.order_money','orders.status','orders.shipping_free','orders.remark','orders.auto_receipt','orders.pay_time','users.name'])
            -> paginate(10);
        return $this->view('orders',['list'=>$list]);
    }
    // 跳转订单管理
    public function shopMerchantMoney(){
        $all = \request() -> all();
        $data = DB::table('user_logs')
            -> join("users","user_logs.user_id","=","users.id")
            -> where('source',0)
            -> where('merchant_id',$all['id'])
            -> where('user_logs.is_del',0)
            -> orderBy('type_id')
            -> select(['user_logs.id','users.name','user_logs.price','user_logs.describe','user_logs.state','user_logs.type_id','user_logs.create_time'])
            -> paginate(10);
        return $this->view('',['data'=>$data]);
    }
    // 商家详情
    public function information(){
        $all = \request() -> all();
        if(\request() -> isMethod("get")){
            // 通过传入的id 查询商户信息
            $data = DB::table('merchants')
                -> join('merchant_type','merchants.merchant_type_id','=','merchant_type.id')
                -> where('merchants.id',$all['id'])
                -> select(['merchant_type.type_name','merchants.id',
                    'merchants.name','merchants.desc',
                    'merchants.province_id','merchants.city_id',
                    'merchants.area_id','merchants.address',
                    'merchants.tel','merchants.user_name',
                    'merchants.management_type','merchants.management_type',
                    'merchants.banner_img','merchants.logo_img',
                    'merchants.door_img','merchants.management_img',
                    'merchants.goods_img','merchants.merchant_type_id','merchants.is_reg'])
                -> first();
//            return dd($data);
            return $this->view('',['data'=>$data]);
        }else{
            $save['is_reg']=$all['is_reg'];
            $id=$all['id'];
            if(empty($all['url'])){
                $url='merchants.index';
            }else{
                $url=$all['url'];
            }
            $data=Db::table('merchants')->where('id',$id)->first();

            if($save['is_reg']==1 && !empty($data)){
                $res['allow_in']=1;
                $res['status']=1;
                $re=Db::table('users')->where('id',$data->user_id)->update($res);
                $role=Db::table('merchant_type')->where('id',$data->merchant_type_id)->first();

                $datas['role_id']=$role->role_id;
                $datas['user_id']=$data->user_id;
                $datas['created_at']=date('Y-m-d H:i:s',time());
                $datas['updated_at']=date('Y-m-d H:i:s',time());
                $ress=Db::table('user_role')->insert($datas);
            }else{
                return "商家审核失败,请稍后重试";
            }
            $re=Db::table('merchants')->where('id',$id)->update($save);
            if($re){
                return 1;
            }else{
                return "商家审核失败,请稍后重试";
            }
        }
    }

    // 平台优惠
    public function shopDiscount(){
        return "模块功能开发中";
    }

    //查看订单
    public function ordersUpd()
    {
        $id = Auth::id();     // 当前登录用户的id
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('is_reg',1)
            -> first();
        $all = \request()-> all();
        $ids= $all['id'];
        $courier_num = $all['courier_num'];  //快递公司
        $express_id = $all['express_id'];    //快递单号
        // 如果当前用户是商家，则查询当前商户的商品  基本信息
        if($i){
            $data = DB::table('order_goods')
                -> join('users','order_goods.user_id','=','users.id')
                -> where('order_goods.is_del',0)
                -> where('order_goods.merchant_id',$id)
                ->where('order_goods.id',$ids)
                ->first(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total',
                    'order_goods.pay_way','order_goods.status as statuss','users.name as user_name','users.mobile','order_goods.order_source','order_goods.express_id','order_goods.courier_num','order_goods.order_id','order_goods.order_types'
                ]);
        }else{
            $data = DB::table('order_goods')
                -> join('users','order_goods.user_id','=','users.id')
                -> where('order_goods.is_del',0)
                ->where('order_goods.id',$ids)
                ->first(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total',
                    'order_goods.pay_way','order_goods.status as statuss','users.name as user_name','users.mobile','order_goods.order_source','order_goods.express_id','order_goods.courier_num','order_goods.order_id','order_goods.order_types'
                ]);
        }

//        //收货人信息
        if($i){
            $uid = DB::table('order_goods')
                ->where('id',$ids)
                ->first(['user_id','order_id','express_id']);
            $address =DB::table("user_address")
                ->where('user_id',$uid->user_id)
                ->first(['name','mobile','address']);
        }else{
            $uid = DB::table('order_goods')
                ->where('id',$ids)
                ->first(['user_id','express_id','order_id']);  //快递公司
            //收货人
            $address =DB::table("user_address")
                ->where('user_id',$uid->user_id)
                ->first(['name','mobile','address']);
        }
        //商品信息
        $goodInfo = DB::table("order_goods")
            ->join('goods','order_goods.goods_id','=','goods.id')
            ->join('goods_sku','order_goods.goods_id','=','goods_sku.goods_id')
            ->where('order_goods.goods_id',18)
            ->get(['goods.img','goods.name','goods.price','goods.good_num','order_goods.num','goods_sku.attr_value','goods_sku.store_num','goods.good_num']);
        //总计
        $sum = DB::table("order_goods")
            ->join('goods','order_goods.goods_id','=','goods.id')
            ->join('goods_sku','order_goods.goods_id','=','goods_sku.goods_id')
            ->where('order_goods.goods_id',18)
            ->get(['goods.price']);
        $json =json_encode($sum);
        $sumPrice = json_decode($json,true);
        $sumNum = array_column($sumPrice,'price');

             //发票信息
              $tick = DB::table("order_invoice")
                  ->where('order_id',$uid->order_id)
                  ->get(['is_vat','invoice_title','invoice_content','order_id']);
//              var_dump($tick);die;
            if(!empty($tick[0]->order_id)){
                $goods =  DB::table("order_goods")
                    ->where('order_id',$tick[0]->order_id)
                    ->get(['user_id']);

                $user = DB::table("users")
                    ->where('id',$goods[0]->user_id)
                    ->get(['mobile']);
            }else{
                $user=null;
            }



        return $this->view('ordersUpd',['id'=>$ids,'data'=>$data,'address'=>$address,'uid'=>$uid,'good'=>$goodInfo,'num'=>$sumNum,'courier_num'=>$courier_num,'express_id'=>$express_id,'user'=>$user,'tick'=>$tick]);
    }
    //订单修改提交
    public function ordersUpds()
    {
        $status = input::post('status');
        $id = input::post('id');
        $res = Orders::where('id',$id)->update(['status' => $status]);
        if ($res){
            return redirect()->route('shop.orders');
        }
        return viewError('已修改或者修改失败');
    }

    // 商品参数
    public function storeComplateAttrs(){
        // 获得提交的数据
        $all = \request() -> all();
        if(\request() -> isMethod("get")){
            if(count($all) == 3){
                flash("未选择商品参数，请选择后重试") -> error();
                return redirect()->route('shop.goods');
            }
            foreach ($all['attrname'] as $item) {
                // 通过该id 在商品参数中去找值
                $name = DB::table('goods_attr') -> where('id',$item)-> select(['name']) -> first();
                $num = 0;
                if(!empty($all['attrvalue_'.$item.''])){
                    $data[] =[
                        'name' => json_decode(json_encode($name),true)['name'],
                        'value' => $all['attrvalue_'.$item.'']
                    ];
                }
            }
            foreach ($data as $k=>$v){
                foreach ($v['value'] as $kk =>$item) {
                    $a[$k][$kk] = $item;
                }
            }
            // 笛卡尔积拼接数组
            $arr1 = [];
            $result = array_shift($a);
            while($arr2 = array_shift($a)){
                $arr1 = $result;
                $result = [];
                foreach ($arr1 as $v){
                    foreach ($arr2 as $v2){
                        if(!is_array($v))$v = array($v);
                        if(!is_array($v2))$v2 = array($v2);
                        $result[] = array_merge_recursive($v,$v2);
                    }
                }
            }
            // 取出属性名称
            foreach ($data as $v){
                $dataname[] = $v['name'];
            }
            return $this->view('',['data'=>$result,'dataname'=>$dataname,'goods_id'=>$all['goods_id']]);
        }else{
            // 属性名称
            $attr_name = $all['attr_name'];
            // 库存
            $num = $all['num'];
            // 价格
            $price = $all['price'];
            // 通过传入的id，查询数据库中是否存在该id，如果存在，执行修改操作，如果不存在，执行新增操作
            $s = DB::table('goods_sku') -> where('goods_id',$all['goods_id']) -> first();
            if(empty($s)){
                // 不存在该id,执行新增
                for($i = 1;$i<=count($all)-5;$i++){
                    $value[] = [
                        'name' => $attr_name,
                        'value' => $all['value_'.$i.'']
                    ];
                    $values[] = json_encode($value,JSON_UNESCAPED_UNICODE);
                    $value = [];
                }
                DB::beginTransaction();
                try{
                    foreach ($values as $k =>$v){
                        $data = [
                            'goods_id' => $all['goods_id'],
                            'attr_value' => $values[$k],
                            'price' => $price[$k],
                            'store_num' => $num[$k]
                        ];
                        $i = DB::table('goods_sku') -> insert($data);
                    }
                    if($i){
                        flash('保存成功') -> success();
                        return redirect()->route('shop.goods');
                        DB::commit();
                    }else{
                        flash('保存失败') -> error();
                        return redirect()->route('shop.goods');
                        DB::rollBack();
                    }
                }catch (\Exception $e){
                    DB::rollBack();
                }
            }else{
                // 存在该id，执行修改
                // 将该表中存在该id的所有数据删除，再重新新增
                DB::beginTransaction();
                try{
                    // 删除表中，用该商品id的所有数据
                    DB::table('goods_sku') -> where('goods_id',$all['goods_id']) -> delete();
                    // 执行新增
                    for($i = 1;$i<=count($all)-5;$i++){
                        $value[] = [
                            'name' => $attr_name,
                            'value' => $all['value_'.$i.'']
                        ];
                        $values[] = json_encode($value,JSON_UNESCAPED_UNICODE);
                        $value = [];
                    }
                    try{
                        foreach ($values as $k =>$v){
                            $data = [
                                'goods_id' => $all['goods_id'],
                                'attr_value' => $values[$k],
                                'price' => $price[$k],
                                'store_num' => $num[$k]
                            ];
                            $i = DB::table('goods_sku') -> insert($data);
                        }
                        if($i){
                            flash('修改成功') -> success();
                            return redirect()->route('shop.goods');
                            DB::commit();
                        }else{
                            flash('修改失败') -> error();
                            return redirect()->route('shop.goods');
                            DB::rollBack();
                        }
                    }catch (\Exception $e){
                        DB::rollBack();
                    }

                }catch (\Exception $e){
                    DB::rollBack();
                }

            }

        }

    }

    // 活动管理
    public function activity(){
        $id = Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            // 查询活动表中数据
            $data = DB::table('goods_activity')
                -> join('merchants','goods_activity.merchant_id','=','merchants.id')
                -> where('merchant_id',$id)
                -> where('is_del',0)
                -> select(['goods_activity.id','merchants.name as merchants_name','goods_activity.name as activity_name','goods_activity.goods','goods_activity.create_time','goods_activity.end_time','goods_activity.status'])
                -> paginate(10);
        }else{
            // 未开店，则为管理员
            // 查询活动表中数据
            $data = DB::table('goods_activity')
                -> join('merchants','goods_activity.merchant_id','=','merchants.id')
                -> where('is_del',0)
                -> select(['goods_activity.id','merchants.name as merchants_name','goods_activity.name as activity_name','goods_activity.goods','goods_activity.create_time','goods_activity.end_time','goods_activity.status'])
                -> paginate(10);
        }
        return $this->view('',['data'=>$data]);
    }
    // 新增 and 修改 活动管理
    public function activityChange(){
        $all = \request() -> all();
        if(\request() -> isMethod("get")){
            // 判断是跳转新增界面 还是 跳转修改界面
            if(empty($all['id'])){
                // 跳转新增界面
                // 查询数据库，获取商品数据
                $goodsdata = DB::table('goods') -> get();
                $arr = [
                    'goodsdata'=>$goodsdata,
                    'activityid' => [],
                    'activitydata' => (object)[
                        'status' => ''
                    ]
                ];
                return $this->view('',$arr);
            }else{
                // 跳转修改界面
                // 查询数据库，获取商品数据
                $goodsdata = DB::table('goods') -> get();
                // 根据获取的id 查询活动表中的数据
                $activitydata = DB::table('goods_activity') -> where('id',$all['id']) ->first();
                // 将获取的对象转换为数组
                $one = json_decode(json_encode($activitydata),true);
                // 提取商品id
                $activityid = array_column(json_decode($one['goods']),'id');
                $arr = [
                    'goodsdata'=>$goodsdata,
                    'activitydata'=>$activitydata,
                    'activityid'=>$activityid
                ];
                return $this->view('',$arr);
            }
        }else{
            // 判断是执行新增操作 还是 执行修改操作
            if(empty($all['id'])){
                // 跳转新增界面
                $goodsid = $all['goods'];
                // 根据当前获得的id，查询数据库中，商品的id
                foreach ($goodsid as $v){
                    $name = DB::table('goods') -> where('id',$v)->select(['name']) -> first();
                    $goodsname[] = json_decode(json_encode($name),true);
                }
                // 封装数据
                foreach ($goodsname as $k =>$v){
                    $goods[]=[
                        'id' => $goodsid[$k],
                        'names' => $v['name']
                    ];
                }
                // 将封装的数据，转码成字符串
                $goods = json_encode($goods,JSON_UNESCAPED_UNICODE);        // JSON_UNESCAPED_UNICODE：保留中文字符，不被转码

                // 获取提交的数据
                $data = [
                    'merchant_id' => Auth::id(),
                    'name' => $all['name'],
                    'goods' => $goods,
                    'create_time' => $all['create_time'],
                    'end_time' => $all['end_time'],
                    'status' => $all['status'],
                ];
                // 执行新增操作
                $i = DB::table('goods_activity') -> insert($data);
                if($i){
                    flash('新增成功') -> success();
                    return redirect()->route('shop.activity');
                }else{
                    flash('新增失败') -> error();
                    return redirect()->route('shop.activity');
                }
            }else{
                // 执行修改操作
                $goodsid = $all['goods'];
                // 根据当前获得的id，查询数据库中，商品的id
                foreach ($goodsid as $v){
                    $name = DB::table('goods') -> where('id',$v)->select(['name']) -> first();
                    $goodsname[] = json_decode(json_encode($name),true);
                }
                // 封装数据
                foreach ($goodsname as $k =>$v){
                    $goods[]=[
                        'id' => $goodsid[$k],
                        'names' => $v['name']
                    ];
                }
                // 将封装的数据，转码成字符串
                $goods = json_encode($goods,JSON_UNESCAPED_UNICODE);        // JSON_UNESCAPED_UNICODE：保留中文字符，不被转码

                // 获取提交的数据
                $data = [
                    'merchant_id' => Auth::id(),
                    'name' => $all['name'],
                    'goods' => $goods,
                    'create_time' => $all['create_time'],
                    'end_time' => $all['end_time'],
                    'status' => $all['status'],
                ];
                // 链接数据库执行修改操作
                $i = DB::table('goods_activity') -> where('id',$all['id']) -> update($data);
                if($i){
                    flash('修改成功') -> success();
                    return redirect()->route('shop.activity');
                }else{
                    flash('修改失败，未修改任何内容') -> error();
                    return redirect()->route('shop.activity');
                }
            }
        }
    }
    // 删除活动表
    public function activityDel(){
        // 获取传入的id
        $all = \request() -> all();
        $data = [
            'is_del' => 1
        ];
        // 根据id 对数据进行删除
        $i = DB::table('goods_activity') -> where('id',$all['id']) -> update($data);
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('shop.activity');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('shop.activity');
        }
    }

    // 商品分类
    public function merchants_goods_type(){
        $id = Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            // 链接数据库，查询商户的商品分类
            $datas = GoodsType::where('is_del',1)->get(['id','merchants_name','name','pid','num'])->toArray();
        }else{
            // 链接数据库，查询商户的商品分类
            $datas = GoodsType::where('is_del',1)->get(['id','merchants_name','name','pid','num'])->toArray();
        }
        $data = Tree::tree($datas,'name','id','pid');
        return $this->view('',['data'=>$data]);

    }

    /**
     * @descript 商品分类批删
     * @jsy
     */

      public function goodsAlldel(){
          $all = \request() -> all();
          DB::beginTransaction();
          try{
              $data = [
                  'is_del' => 0
              ];
              // 循环删除数据
              foreach ($all['ids'] as $id){
                  DB::table('merchants_goods_type') -> where('id',$id) -> update($data);
              }
              DB::commit();
              return 1;
          }catch (\Exception $e){
              DB::rollBack();
          }
      }

    /**
     * @descript 订单删除
     * @jsy
     */

    public function orderDl(){
        $all = \request() -> all();

        $data = [
            'is_del' => 1
        ];
        // 循环删除数据
        foreach ($all['ids'] as $id){
            $datas = DB::table('order_goods') -> where('id',$id) -> update($data);
        }
        return 1;

    }

    // 新增 and 修改 商品分类
    public function merchants_goods_typeChange(Request $request){
        $all = \request() -> all();
        if(\request() -> isMethod("get")){
            if(empty($all['id'])){
            $list = GoodsType::where('is_del',1)->get(['pid','merchants_name','num','id','name'])->toArray();
            $list = Tree::tree($list,'name','id','pid');
                // 跳转新增界面
                return $this->view('',['list'=>$list]);
            }else{
                // 跳转修改界面
                // 根据传入的id 查询数据库中的值
//                $list = GoodsType::where('id',$all['id'])->get(['pid','merchants_name','num','id','name'])->toArray();
                $list = GoodsType::where('is_del',1)->get(['id','merchants_name','name','pid','num'])->toArray();
                $list = Tree::tree($list,'name','id','pid');
                $model = new GoodsType();
                $data = GoodsType::where('id',$all['id'])->first(['pid','merchants_name','num','id','name','roots','level'])->toArray();
                return $this->view('',['data'=>$data,'list'=>$list]);
            }
        }else{
            if(empty($all['id'])){
                // 执行新增操作
                // 获取提交的内容
                if ($all['pid']==0){
                    $data  = [
                        'name'        => $all['name'],
                        'merchant_id' => Auth::id(),
                        'pid'         => $all['pid'],
                        'roots'       => 0,
                        'num'         => $all['num']
                    ];
                    // 链接数据库，新增内容
                    $i = DB::table('merchants_goods_type') -> insert($data);
                    if($i){
                        flash('新增成功') -> success();
                        return redirect()->route('shop.merchants_goods_type');
                    }else{
                        flash('新增失败') -> error();
                        return redirect()->route('shop.merchants_goods_type');
                    }
                }else{
                    $data  = [
                        'name'        => $all['name'],
                        'merchant_id' => Auth::id(),
                        'pid'         => $all['pid'],
                        'roots'       =>"0".",".$all['pid'],
                        'level'       => 2,
                        'num'         => $all['num']
                    ];
                    // 链接数据库，新增内容
                    $i = DB::table('merchants_goods_type') -> insert($data);
                    if($i){
                        flash('新增成功') -> success();
                        return redirect()->route('shop.merchants_goods_type');
                    }else{
                        flash('新增失败') -> error();
                        return redirect()->route('shop.merchants_goods_type');
                    }
                }

            }else{
                // 执行修改操作
                // 获取提交的内容
                $data  = [
                    'name' => $all['name'],
                    'num'  => $all['num'],
                    'pid'  => $all['pid'],
                    'roots'=>"0".",".$all['pid'],
                    'level'=>2
                ];
                $i = DB::table('merchants_goods_type') -> where('id',$all['id']) -> update($data);
                if($i){
                    flash('修改成功') -> success();
                    return redirect()->route('shop.merchants_goods_type');
                }else{
                    flash('修改失败，未修改任何内容。') -> error();
                    return redirect()->route('shop.merchants_goods_type');
                }
            }

        }
    }
    // 删除 商品分类
    public function merchants_goods_typeDel(){
        // 获取传入的id
        $all = \request() -> all();
        $data = [
            'is_del' => 0
        ];
        // 根据id 删除数据表中内容
        $i = DB::table('merchants_goods_type') -> where('id',$all['id']) -> update($data);
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('shop.merchants_goods_type');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('shop.merchants_goods_type');
        }
    }

    // 商品评论
    public function commnets(){
        $id = Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            $data = DB::table('order_commnets')
                -> join('users','order_commnets.user_id','=','users.id')     // 链接用户表
                -> join('goods','order_commnets.goods_id','=','goods.id')     // 链接商品表
                -> where('type',2)
                -> where('merchants_id',$id)
                -> where('order_commnets.is_del',0)
                -> select(['order_commnets.id','users.name as username','goods.name as goodsname','stars','order_commnets.content','order_commnets.created_at'])
                -> paginate(10);
        }else{
            // 反之则为。管理员
            // 查询，商城评论
            $data = DB::table('order_commnets')
                -> join('users','order_commnets.user_id','=','users.id')     // 链接用户表
                -> join('goods','order_commnets.goods_id','=','goods.id')     // 链接商品表
                -> where('type',2)
                -> where('order_commnets.is_del',0)
                -> select(['order_commnets.id','users.name as username','goods.name as goodsname','stars','order_commnets.content','order_commnets.created_at'])
                -> paginate(10);
        }
        return $this->view('',['data' => $data]);

    }
    // 新增商品评论
    public function commnetsAdd(){
        $id = Auth::id();
        if(\request() -> isMethod("get")){
            // 查询商品列表
            $goodsData = DB::table("goods") -> get();
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
                'type' => 2,
                'merchants_id' => $id,
                'stars' => $all['stars'],
                'content' => $all['content'],
                'created_at' => date("Y-m-d H:i:s")
            ];
            // 链接数据库，新增内容
            $i = DB::table('order_commnets') -> insert($data);
            if($i){
                flash('新增成功') -> success();
                return redirect()->route('shop.commnets');
            }else{
                flash('新增失败') -> error();
                return redirect()->route('shop.commnets');
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
            return redirect()->route('shop.commnets');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('shop.commnets');
        }
    }

    public function statics ()
    {
        $id = Auth::id();     // 当前登录用户的id
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('is_reg',1)
            -> first();
        // 如果当前用户是商家，则查询当前商户的商品
        if($i){
            $data =  DB::table('statics')
                -> join('users','statics.user_id','=','users.id')
                -> join('merchants','statics.merchant_id','=','merchants.id')
                -> where('merchants.user_id',$id)
                -> select(['statics.id','statics.price','statics.describe','statics.state','statics.create_time','statics.type_id','users.name'])
                -> paginate(10);
        }else{
            $data =  DB::table('statics')
                -> join('users','statics.user_id','=','users.id')
                -> select(['statics.id','statics.price','statics.describe','statics.state','statics.create_time','statics.type_id','users.name'])
                -> paginate(10);
        }
        return $this->view('',['data'=> $data]);
    }

    public function staticsDel (Request $request)
    {
        $id = input::get('id');
        $res = Statics::where('id',$id)->update(['is_del' => 1]);
        if ($res){
            return redirect()->route('shop.statics');
        }
        return viewError('已删除或者删除失败');
    }
    /*
            * 订单数据展示
            * */
    public function orders(Request $request)
    {
        $input = $request->all();
        $id = Auth::id();     // 当前登录用户的id
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('is_reg',1)
            -> first();
        if(empty($input['status']))
        {
//            if($i){
//                $list = DB::table('orders')
//                    -> join('order_goods','orders.order_sn','=','order_goods.order_id')
////            -> join('merchants','order_goods.merchant_id','=','merchants.id')
//                    -> join('users','orders.user_id','=','users.id')
//                    -> where('order_goods.is_del',0)
//                    -> where('order_goods.merchant_id',$id)
//                    -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
//                        'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
//                    -> paginate(10);
//
//            }else{
//                $list = DB::table('orders')
//                    -> join('order_goods','orders.order_sn','=','order_goods.order_id')
//                    -> join('users','orders.user_id','=','users.id')
//                    -> where('order_goods.is_del',0)
//                    -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
//                        'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
//                    -> paginate(10);
//            }

            $status=80;
            if($status){
                $id = Auth::id();     // 当前登录用户的id
                // 判断当前用户是否是商家
                $i = DB::table('merchants')
                    -> where('user_id',$id)
                    -> where('is_reg',1)
                    -> first();
                // 如果当前用户是商家，则查询当前商户的商品
                if($i){
                    $list = DB::table('orders')
                        -> join('order_goods','orders.order_sn','=','order_goods.order_id')
//            -> join('merchants','order_goods.merchant_id','=','merchants.id')
                        -> join('users','orders.user_id','=','users.id')
                        -> where('order_goods.is_del',0)
                        -> where('order_goods.merchant_id',$id)
                        -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                            'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num','order_goods.order_show'])
                        -> paginate(10);

                }else{
                    $list = DB::table('orders')
                        -> join('order_goods','orders.order_sn','=','order_goods.order_id')
                        -> join('users','orders.user_id','=','users.id')
                        -> where('order_goods.is_del',0)
                        -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                            'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num','order_goods.order_show'])
                        -> paginate(10);
                }
            }

        }else{
            $status = $input['status'];
            switch($status){
                    case 10:
//                        var_dump($input);die;
                        $id = Auth::id();     // 当前登录用户的id
                        // 判断当前用户是否是商家
                        $i = DB::table('merchants')
                            -> where('user_id',$id)
                            -> where('is_reg',1)
                            -> first();
                        // 如果当前用户是商家，则查询当前商户的商品
                        if($i){
                            $list = DB::table('orders')
                                -> join('order_goods','orders.order_sn','=','order_goods.order_id')
//            -> join('merchants','order_goods.merchant_id','=','merchants.id')
                                -> join('users','orders.user_id','=','users.id')
                                -> where('order_goods.is_del',0)
                                -> where('order_goods.merchant_id',$id)
                                ->where('order_goods.status',$status)
                                -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                    'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                                -> paginate(10);

                        }else{
                            $list = DB::table('orders')
                                -> join('order_goods','orders.order_sn','=','order_goods.order_id')
                                -> join('users','orders.user_id','=','users.id')
                                -> where('order_goods.is_del',0)
                                ->where('order_goods.status',$status)
                                -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                    'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                                -> paginate(10);
                        }
                        break;
                    case 20:
                        $id = Auth::id();     // 当前登录用户的id
                        // 判断当前用户是否是商家
                        $i = DB::table('merchants')
                            -> where('user_id',$id)
                            -> where('is_reg',1)
                            -> first();
                        // 如果当前用户是商家，则查询当前商户的商品
                        if($i){
                            $list = DB::table('orders')
                                -> join('order_goods','orders.order_sn','=','order_goods.order_id')
//            -> join('merchants','order_goods.merchant_id','=','merchants.id')
                                -> join('users','orders.user_id','=','users.id')
                                -> where('order_goods.is_del',0)
                                -> where('order_goods.merchant_id',$id)
                                ->where('order_goods.status',$status)
                                -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                    'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                                -> paginate(10);

                        }else{
                            $list = DB::table('orders')
                                -> join('order_goods','orders.order_sn','=','order_goods.order_id')
                                -> join('users','orders.user_id','=','users.id')
                                -> where('order_goods.is_del',0)
                                ->where('order_goods.status',$status)
                                -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                    'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                                -> paginate(10);
                        }
                        break;
                    case 40:
                        $id = Auth::id();     // 当前登录用户的id
                        // 判断当前用户是否是商家
                        $i = DB::table('merchants')
                            -> where('user_id',$id)
                            -> where('is_reg',1)
                            -> first();
                        // 如果当前用户是商家，则查询当前商户的商品
                        if($i){
                            $list = DB::table('orders')
                                -> join('order_goods','orders.order_sn','=','order_goods.order_id')
//            -> join('merchants','order_goods.merchant_id','=','merchants.id')
                                -> join('users','orders.user_id','=','users.id')
                                -> where('order_goods.is_del',0)
                                -> where('order_goods.merchant_id',$id)
                                ->where('order_goods.status',$status)
                                -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                    'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                                -> paginate(10);

                        }else{
                            $list = DB::table('orders')
                                -> join('order_goods','orders.order_sn','=','order_goods.order_id')
                                -> join('users','orders.user_id','=','users.id')
                                -> where('order_goods.is_del',0)
                                ->where('order_goods.status',$status)
                                -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                    'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                                -> paginate(10);
                        }
                        break;
                    case 50:
                        $id = Auth::id();     // 当前登录用户的id
                        // 判断当前用户是否是商家
                        $i = DB::table('merchants')
                            -> where('user_id',$id)
                            -> where('is_reg',1)
                            -> first();
                        // 如果当前用户是商家，则查询当前商户的商品
                        if($i){
                            $list = DB::table('orders')
                                -> join('order_goods','orders.order_sn','=','order_goods.order_id')
//            -> join('merchants','order_goods.merchant_id','=','merchants.id')
                                -> join('users','orders.user_id','=','users.id')
                                -> where('order_goods.is_del',0)
                                -> where('order_goods.merchant_id',$id)
                                ->where('order_goods.status',$status)
                                -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                    'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                                -> paginate(10);

                        }else{
                            $list = DB::table('orders')
                                -> join('order_goods','orders.order_sn','=','order_goods.order_id')
                                -> join('users','orders.user_id','=','users.id')
                                -> where('order_goods.is_del',0)
                                ->where('order_goods.status',$status)
                                -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                    'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                                -> paginate(10);
                        }
                        break;
                    case 60:
                        $id = Auth::id();     // 当前登录用户的id
                        // 判断当前用户是否是商家
                        $i = DB::table('merchants')
                            -> where('user_id',$id)
                            -> where('is_reg',1)
                            -> first();
                        // 如果当前用户是商家，则查询当前商户的商品
                        if($i){
                            $list = DB::table('orders')
                                -> join('order_goods','orders.order_sn','=','order_goods.order_id')
//            -> join('merchants','order_goods.merchant_id','=','merchants.id')
                                -> join('users','orders.user_id','=','users.id')
                                -> where('order_goods.is_del',0)
                                -> where('order_goods.merchant_id',$id)
                                ->where('order_goods.status',$status)
                                -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                    'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                                -> paginate(10);

                        }else{
                            $list = DB::table('orders')
                                -> join('order_goods','orders.order_sn','=','order_goods.order_id')
                                -> join('users','orders.user_id','=','users.id')
                                -> where('order_goods.is_del',0)
                                ->where('order_goods.status',$status)
                                -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                    'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                                -> paginate(10);
                        }
                        break;
                case 70:
                    $id = Auth::id();     // 当前登录用户的id
                    // 判断当前用户是否是商家
                    $i = DB::table('merchants')
                        -> where('user_id',$id)
                        -> where('is_reg',1)
                        -> first();
                    // 如果当前用户是商家，则查询当前商户的商品
                    if($i){
                        $list = DB::table('orders')
                            -> join('order_goods','orders.order_sn','=','order_goods.order_id')
//            -> join('merchants','order_goods.merchant_id','=','merchants.id')
                            -> join('users','orders.user_id','=','users.id')
                            -> where('order_goods.is_del',0)
                            -> where('order_goods.merchant_id',$id)
                            -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num','order_goods.order_show'])
                            -> paginate(10);
                    }else{
                        $list = DB::table('orders')
                            -> join('order_goods','orders.order_sn','=','order_goods.order_id')
                            -> join('users','orders.user_id','=','users.id')
                            -> where('order_goods.is_del',0)
                            -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num','order_goods.order_show'])
                            -> paginate(10);
                    }
                    break;
                    default:
                        $id = Auth::id();     // 当前登录用户的id
                        // 判断当前用户是否是商家
                        $i = DB::table('merchants')
                            -> where('user_id',$id)
                            -> where('is_reg',1)
                            -> first();
                        // 如果当前用户是商家，则查询当前商户的商品
                        if($i){
                            $list = DB::table('orders')
                                -> join('order_goods','orders.order_sn','=','order_goods.order_id')
//            -> join('merchants','order_goods.merchant_id','=','merchants.id')
                                -> join('users','orders.user_id','=','users.id')
                                -> where('order_goods.is_del',0)
                                -> where('order_goods.merchant_id',$id)
                                -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                    'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                                -> paginate(10);
                        }else{
                            $list = DB::table('orders')
                                -> join('order_goods','orders.order_sn','=','order_goods.order_id')
                                -> join('users','orders.user_id','=','users.id')
                                -> where('order_goods.is_del',0)
                                -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                    'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                                -> paginate(10);
                        }
                }
        }

        if(empty($input['name'])){
        }else{
            //手机号 用户名搜索
            if ($input['sta']=="2"){
                // 如果当前用户是商家，则查询当前商户的商品
                $ids=  DB::table("users")->where("name","=",$input['name'])->first(['id']);
                if (empty($ids)){
                    if($i){
                        $list = DB::table('orders')
                            -> join('order_goods','orders.order_sn','=','order_goods.order_id')
//            -> join('merchants','order_goods.merchant_id','=','merchants.id')
                            -> join('users','order_goods.user_id','=','users.id')
                            -> where('order_goods.is_del',0)
                            -> where('order_goods.merchant_id',$id)
                            ->where('users.mobile','=',$input["user"])
//                        ->orWhere('order_goods.user_id','=',$ids->id)
                            -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                            -> paginate(10);
                    }else{
                        $list = DB::table('orders')
                            -> join('order_goods','orders.order_sn','=','order_goods.order_id')
                            -> join('users','order_goods.user_id','=','users.id')
                            -> where('order_goods.is_del',0)
                            ->where('users.mobile','=',$input["user"])
//                        ->orWhere('order_goods.user_id','=',$ids->id)
                            -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                            -> paginate(10);
                    }
                }else{
                    if($i){
                        $list = DB::table('orders')
                            -> join('order_goods','orders.order_sn','=','order_goods.order_id')
//            -> join('merchants','order_goods.merchant_id','=','merchants.id')
                            -> join('users','orders.user_id','=','users.id')
                            -> where('order_goods.is_del',0)
                            -> where('order_goods.merchant_id',$id)
                            ->where('order_goods.user_id','=',$ids->id)
                            -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                            -> paginate(10);
                    }else{
                        $list = DB::table('orders')
                            -> join('order_goods','orders.order_sn','=','order_goods.order_id')
                            -> join('users','orders.user_id','=','users.id')
                            -> where('order_goods.is_del',0)
                            ->where('order_goods.user_id','=',$ids->id)
                            -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                                'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                            -> paginate(10);
                    }
                }
            }
        }
         //订单编号搜索
        if(empty($input['keyword'])){

        }else{
            if ($input['sta']=="1"){
                // 如果当前用户是商家，则查询当前商户的商品
                if($i){
                    $list = DB::table('orders')
                        -> join('order_goods','orders.order_sn','=','order_goods.order_id')
                        -> join('users','orders.user_id','=','users.id')
                        -> where('order_goods.is_del',0)
                        -> where('order_goods.merchant_id',$id)
                        ->where('order_id','like','%'.$input["keyword"].'%')
                        -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                            'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                        -> paginate(10);
                }else{
                    $list = DB::table('orders')
                        -> join('order_goods','orders.order_sn','=','order_goods.order_id')
                        -> join('users','orders.user_id','=','users.id')
                        -> where('order_goods.is_del',0)
                        ->where('order_id','like','%'.$input["keyword"].'%')
                        -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                            'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                        -> paginate(10);
                }
            }
        }
        //时间搜索
        if(empty($input['time'])){
           }else{
            if($i){
                $list = DB::table('orders')
                    -> join('order_goods','orders.order_sn','=','order_goods.order_id')
//            -> join('merchants','order_goods.merchant_id','=','merchants.id')
                    -> join('users','orders.user_id','=','users.id')
                    -> where('order_goods.is_del',0)
                    -> where('order_goods.merchant_id',$id)
                    ->whereDate('order_goods.created_at','like',$input['time'])
                    -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                        'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                    -> paginate(10);
            }else{
                $list = DB::table('orders')
                    -> join('order_goods','orders.order_sn','=','order_goods.order_id')
                    -> join('users','orders.user_id','=','users.id')
                    -> where('order_goods.is_del',0)
                    ->whereDate('order_goods.created_at','like',$input['time'])
                    -> select(['order_goods.id','order_goods.pay_money','order_goods.created_at as pay_time','order_goods.total','orders.shipping_free','orders.order_sn',
                        'orders.pay_way','orders.remark','order_goods.status as statuss','users.name as user_name','users.mobile','orders.created_at','order_goods.order_source','order_goods.express_id','order_goods.courier_num'])
                    -> paginate(10);
            }
        }
        $id = Auth::id();     // 当前登录用户的id
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('is_reg',1)
            -> first();
        // 如果当前用户是商家，则查询当前商户的商品
        if($i){
            $data =Ogoods::with("users")->where(['is_del'=>0,'user_id'=>$id,'status'=>10])->get(['id'])->toArray();
            $data1 =Ogoods::with("users")->where(['is_del'=>0,'user_id'=>$id,'status'=>20])->get(['id'])->toArray();
            $data2 =Ogoods::with("users")->where(['is_del'=>0,'user_id'=>$id,'status'=>40])->get(['id'])->toArray();
            $data3 =Ogoods::with("users")->where(['is_del'=>0,'user_id'=>$id,'status'=>50])->get(['id'])->toArray();
            $data4 =Ogoods::with("users")->where(['is_del'=>0,'user_id'=>$id,'status'=>60])->get(['id'])->toArray();
            $data5 =Ogoods::with("users")->where(['is_del'=>0,'user_id'=>$id])->get(['id'])->toArray();

        }else{
            $data =Ogoods::with("users")->where(['is_del'=>0,'status'=>10])->get(['id'])->toArray();
            $data1 =Ogoods::with("users")->where(['is_del'=>0,'status'=>20])->get(['id'])->toArray();
            $data2 =Ogoods::with("users")->where(['is_del'=>0,'status'=>40])->get(['id'])->toArray();
            $data3 =Ogoods::with("users")->where(['is_del'=>0,'status'=>50])->get(['id'])->toArray();
            $data4 =Ogoods::with("users")->where(['is_del'=>0,'status'=>60])->get(['id'])->toArray();
            $data5 =Ogoods::with("users")->where(['is_del'=>0])->get(['id'])->toArray();
        }

        $count = ['data'=>$data,'data1'=>$data1,'data2'=>$data2,'data3'=>$data3,'data4'=>$data4,'data5'=>$data5];
        $model = Order::get(['order_goods_id'])->toArray();
        $cfCen =array_column($model,"order_goods_id");
        return $this->view('orders',['list'=>$list,'has'=>$cfCen,'count'=>$count]);
    }

    /*
     * 添加订单测试数据
     * */
    public function ordersAdd (Request $request)
    {
        return $this->view('ordersAdd',['list'=>[]]);
    }

    /*
     * 添加订单测试数据
     * */

    public function ordersAdds (Request $request)
    {
        $input = request()->all();
        $data = [
            'user_id'=> $input['user_id'],
            'order_sn'=> rand(100000,999999),
            'order_money'=>$input['order_money'],
            'pay_way'=>$input['pay_way'],
            'pay_money'=>$input['pay_money'],
            'shipping_free'=>$input['shipping_free'],
            'remark'=>$input['remark'],
            'pay_time'=> date('Y-m-d h:i:s',time()),
            'send_time'=> date('Y-m-d h:i:s',time()),
            'auto_receipt'=>$input['auto_receipt'],
            'status'=>$input['status'],
        ];
//        var_dump($data);die;
        $res = DB::table('orders')->insert($data);
        if($res){
            flash('编辑成功')->success();
            return redirect()->route('shop.orders');
        }else{
            flash('编辑失败')->error();
            return redirect()->route('shop.orders');
        }
    }

    /*
     * 删除订单
     * 只是将数据软删除并未被真正删除
     * */
    public function ordersDel (Request $request)
    {
        $id = $request->input('id');
        $red =Ogoods::where('id',$id)->update(['is_del' => 1]);
        if ($red){
            return redirect()->route('shop.orders');
        }
        return viewError('已删除或者删除失败');

    }

    // 异步上传文件
    public function storeAlbum(Request $request){
        // 获取上传的文件
        $choose_file = $_FILES['choose-file'];
        //判断第一个文件名是否为空
        if ($choose_file['name'][0] == "") {
            return "请选择商品图片";
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
        // 获取上传的id
        $goods_id = $request -> input('goods_id');
        $img_array = json_encode($img_array);
        $data = [
            'album' => $img_array
        ];
        DB::table('goods') -> where('id',$goods_id) -> update($data);
        // 上传成功
        return 1;

    }

    use ApiResponse;
    protected $merchant_type_id = 2;

    // 跳转商品界面
    public function goods(Request $request ,Auth $auth)
    {
        $id = Auth::id();     // 当前登录用户的id
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('is_reg',1)
            -> select('id')
            -> first();
        $all = $request->all();
        if(!empty($all['status']))
        {
            if($all['status'] == 2){
                $where[] = ['goods.is_sale',0];
            }else{
                $where[] = ['goods.is_sale',$all['status']];
            }
        }
        $where[] = ['goods.is_del',0];
        // 如果当前用户是商家，则查询当前商户的商品
        if($i){
            $goods = DB::table('goods')
                -> join('merchants','goods.merchant_id','=','merchants.id')
                -> where('goods.merchant_id',$i -> id)
                -> where($where)
                -> select(['merchants.name as merchant_name','goods.id','goods.pv','goods.created_at','goods.updated_at',
                    'goods.goods_cate_id','goods.name as goods_name','goods.img','goods.desc','goods.is_hot','goods.is_recommend','goods.is_sale',
                    'goods.is_bargain','goods.dilivery','goods.volume','goods.price'])
                -> orderBy('goods.id','desc')
                -> paginate(10);
            foreach ($goods as $k => $v){
                $goods_cate_id  = explode(',',$v->goods_cate_id);
                unset($goods_cate_id[0]);
                array_pop($goods_cate_id);
                $name=[];
                foreach ($goods_cate_id as $item) {
                    $name[]=Db::table('goods_cate')->select('name')->where('id',$item)->first()->name ?? '';
                }
                $goods[$k]->goods_cate_id=implode(',',$name);
            }
        }else{
            $goods = DB::table('goods')
                -> join('merchants','goods.merchant_id','=','merchants.id')
                -> where($where)
                -> select(['merchants.name as merchant_name','goods.id','goods.name as goods_name','goods.pv',
                    'goods.created_at','goods.name as goods_name','goods.updated_at','goods.goods_cate_id','goods.img','goods.desc','goods.is_hot',
                    'goods.is_recommend','goods.is_sale','goods.is_bargain','goods.dilivery','goods.volume','goods.price'])
                -> orderBy('goods.id','desc')
                -> paginate(10);
            foreach ($goods as $k => $v){
                $goods_cate_id  = explode(',',$v->goods_cate_id);
                unset($goods_cate_id[0]);
                array_pop($goods_cate_id);
                $name=[];
                foreach ($goods_cate_id as $item) {
                    $name[]=Db::table('goods_cate')->select('name')->where('id',$item)->first()->name ?? '';
                }
                $goods[$k]->goods_cate_id=implode(',',$name);
            }
        }
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            // 链接数据库，查询商户的商品分类
//            $datas = GoodsType::where('is_del',1)->get(['id','merchants_name','name','pid','num'])->toArray();
            $datas = DB::table('merchants_goods_type')
                -> join('merchants','merchants_goods_type.merchant_id','=','merchants.id')
                -> where('is_del',1)
                -> where('merchants_goods_type,merchant_id',$i -> id)
                -> select('merchants_goods_type.id','merchants.name as merchants_name','merchants_goods_type.name as name','pid','num')
                -> get();
        }else{
            // 链接数据库，查询商户的商品分类
//            $datas = GoodsType::where('is_del',1)->get(['id','merchants_name','name','pid','num'])->toArray();
            $datas = DB::table('merchants_goods_type')
                -> join('merchants','merchants_goods_type.merchant_id','=','merchants.id')
                -> where('is_del',1)
                -> select('merchants_goods_type.id','merchants.name as merchants_name','merchants_goods_type.name as name','pid','num')
                -> get();
        }
//        return dd();
        $data = Tree::tree(json_decode(json_encode($datas),true),'name','id','pid');
        $goods_sku = DB::select("select goods_id,SUM(store_num) as total from `goods_sku` group by `goods_id`");
        return $this->view('goods',['list'=>$goods,'data'=>$data,'goods_sku'=>json_decode(json_encode($goods_sku),true)]);
    }

    // 跳转商品新增界面
    public function create (Request $request)
    {
        $id = Auth::id();
        $goodsCate = GoodsCate::with(['children'=>function($res){
            $res->with('children');
        }])->where('pid','=',0)
            ->get();

        $level1 = GoodsCate::where('pid','=',0)->get();
        $goodBrands = GoodBrands::select('id','name')->orderBy('id','asc')->get();
        // 查询商品参数
        $attrData = DB::table('goods_attr') -> get();
        // 查询运费模板表
        $express_modeldata = DB::table('express_model') -> get();
        // 查询商品分类
        $merchants_goods_type = DB::table('merchants_goods_type')
            -> where('is_del',1)
            -> where('merchant_id',$id)
            -> select('id','name')
            -> get();
        // 查询规格表
        $goods_attr = DB::table('goods_attr') -> get();

        $a = DB::table('goods_attr_value') -> get();
        $arr = [
            'goodsCate'=>$goodsCate,
            'goodBrands'=>$goodBrands,
            'attrData'=>$attrData,
            'attrvalueData'=>$a,
            'goods_attr'=>$goods_attr,
            'express_modeldata'=>$express_modeldata,
            'merchants_goods_type'=>$merchants_goods_type,
            'goodssku'=>[],
            'goodsdata' =>(object)[
                'goods_brand_id'=>'',
                'is_hot'=>'',
                'is_bargain'=>'',
                'is_team_buy'=>'',
                'is_recommend'=>'',
                'dilivery'=>'',
                'merchants_goods_type_id'=>'',
                'is_sale'=>'',
            ]
        ];
        return $this->view('addGoods',$arr);
    }
    // 删除商品
    public function goodsDel(){
        // 获取传入的id
        $all = \request() -> all();
        // 根据当前id 删除表中数据
        $data = [
            'is_del' => 1
        ];
        // 链接数据库，执行删除操作
        $i = DB::table('goods') -> where('id',$all['id']) -> update($data);
        if ($i){
            flash('修改成功') -> success();
            return redirect()->route('shop.goods');
        }else{
            flash('修改失败') -> error();
            return redirect()->route('shop.goods');
        }
    }

    // 跳转修改商品界面
    public function update(){
        $all = \request() -> all();
        //根据id 查询商品详情
        $goodsdata = DB::table('goods') -> where('id',$all['id']) -> first();
        // 根据获取的id 查询商品参数表
        $goodssku = DB::table('goods_sku') -> where('goods_id',$all['id']) -> get();
        if(count($goodssku) != 0){
            foreach ($goodssku as $k =>$v){
                $a = json_decode($goodssku,true)[$k]['attr_value'];
                $goodssku_value[] = json_decode($a,true);
            }
            // 将三维数组转换成二维数组
            foreach ($goodssku_value as $v){
                $new_arr[] = $v[0]['value'];
            }
            // 将二维数组转换成一维数组
            $newarray = [];
            foreach($new_arr as $key=>$val){
                foreach ($val as $k=>$v){
                    $newarray[$key]=$v;
                }
            }
            $old_arr = call_user_func_array('array_merge',$new_arr);
        }else{
            $old_arr = [];
        }

        $goodsCate = GoodsCate::with(['children'=>function($res){
            $res->with('children');
        }])->where('pid','=',0)
            ->get();
        $goodBrands = GoodBrands::select('id','name')->orderBy('id','asc')->get();
        // 查询运费模板表
        $express_modeldata = DB::table('express_model') -> get();
        // 查询商品参数
        $attrData = DB::table('goods_attr') -> get();
        // 查询商品分类
        $merchants_goods_type = DB::table('merchants_goods_type')
            -> where('is_del',1)
            -> select('id','name')
            -> get();
        $goods_attr = DB::table('goods_attr') -> get();
        $a = DB::table('goods_attr_value') -> get();
        $arr = [
            'goodsCate'=>$goodsCate,
            'goodBrands'=>$goodBrands,
            'attrData'=>$attrData,
            'attrvalueData'=>$a,
            'goods_attr'=>$goods_attr,
            'goodsdata'=>$goodsdata,
            'merchants_goods_type'=>$merchants_goods_type,
            'goods_album'=>json_decode($goodsdata->album),
            'express_modeldata'=>$express_modeldata,
            'goods_id'=>$all['id'],
            'goodssku'=> $old_arr      // 将二维数组，转换成一维数组
        ];
        return $this->view('addGoods',$arr);

    }

    public function getCateChildren (Request $request)
    {
        $list = GoodsCate::where('pid','=',$request->input('id'))->select('id','pid','name')->get();
        if ($list) {
            return $this->success($list);
        }
        return  $this->failed('没有子分类了');
    }


    public function goodsAttr (Request $request)
    {
        $id = Auth::id();     // 当前登录用户的id
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('is_reg',1)
            -> first();
        // 如果当前用户是商家，则查询当前商户的商品
        if($i){
            $list = DB::table('goods_attr')
                -> join('merchants','goods_attr.merchant_id','=','merchants.id')
                -> where('merchants.user_id',$id)
                -> select(['goods_attr.id','goods_attr.name'])
                -> paginate(10);
        }else{
            $list = DB::table('goods_attr')
                -> join('merchants','goods_attr.merchant_id','=','merchants.id')
                -> select(['goods_attr.id','goods_attr.name'])
                -> paginate(10);
        }
        return $this->view('goodsAttr',['list'=>$list]);
    }

    public function addAttr (Request $request)
    {
        return $this->view('addAttr');
    }

    public function attrUpdate ($id)
    {
        $data = GoodsAttr::find($id);
        return $this->view('updateAttr',['data'=>$data]);
    }

    // 异步获取属性
    public function getAttr (Request $request)
    {
        // 获取提交的id
        $all = \request() -> all();
        // 根据id 查询数据库中商品模板表中的内容
        $goods_attr_data =DB::table('goods_attr') -> where('id',$all['id']) -> first();
        // 根据id 查询数据库中规格属性表
        $goods_attr_value_data = DB::table('goods_attr_value') -> where('goods_attr_id',$all['id']) -> get();
        $result = ['code'=>200,'data'=>$goods_attr_data,'goods_attr_value_data'=>$goods_attr_value_data];
        echo  json_encode($result,JSON_UNESCAPED_UNICODE);
    }

    // 存储属性值
    public function saveAttrValue (Request $request)
    {
        $validate = Validator::make($request->all(),[
            'id' => 'required',
            'attr_value' => 'required|array',
        ],[
            'name.required'=>'名称必须',
            'attr_value.required'=>'属性必须',
            'attr_value.array'=>'请填写属性值',
        ]);

        if ($validate->fails()) {
            flash($validate->errors()->first())->error()->important();
            return redirect()->route('shop.goodsAttr');
        }

        $data = $request->all();
        try {
            $ids = [];

            foreach ($data['attr_value'] as $k=>$v) {
                $model =  GoodsAttrValue::find($k);
                if (!$model) {
                    $model = new GoodsAttrValue();
                }
                $model->goods_attr_id = $request->input('id');
                $model->value = $v;
                $model->save();
                $ids [] = $model->id;
            }

            GoodsAttrValue::where('goods_attr_id', $request->input('id'))->whereNotIn('id',$ids)->delete();
            return redirect()->route('shop.goodsAttr');
        }catch (\Exception $e) {
            flash($e->getMessage())->error()->important();
            return redirect()->route('shop.goodsAttr');
        }
    }

    // 新增模板
    public function attrStore (Request $request)
    {
        flash("该功能还在开发中，敬请期待") -> error();
        return redirect()->route('shop.goodsAttr');
        $validate = Validator::make($request->all(),[
            'specNmae' => 'required',
        ],[
            'specNmae.required'=>'模板名称未输入',
        ]);
        $all = \request() -> all();
        // 判断新增的模板是否存在
        $data = DB::table('goods_attr') -> where('name',$all['specNmae']) -> first();
        if(!empty($data)){
            flash("该商品模板已存在，不能新增。") -> error();
            return redirect()->route('shop.goodsAttr');
        }
        DB::beginTransaction();
        try{
            // 新增模板表
            $goods_attr_data = [
                'merchant_id' => 1,
                'name' => $all['specNmae']
            ];
            $id = DB::table('goods_attr') -> insertGetId($goods_attr_data);
            // 获取上传的规格
            foreach ($all['spec'] as $v){
                // 新增规格属性表
                $item = $v['item'];
                $arr_push = [];
                foreach ($item as $m){
                    array_push($arr_push,$m['item']);
                }
                $spec_value = json_encode($arr_push,JSON_UNESCAPED_UNICODE);
                $goods_attr_value_data = [
                    'goods_attr_id' => $id,
                    'spec' => $v['name'],
                    'spec_value' => $spec_value
                ];
                // 向规格属性表中添加内容
                $i = DB::table('goods_attr_value') -> insert($goods_attr_value_data);
            }
            if ($i) {
                DB::commit();
                flash("商品参数模板添加成功") -> success();
                return redirect()->route('shop.goodsAttr');
            }else{
                DB::rollBack();
                flash("添加失败，请稍后重试") -> error();
                return redirect()->route('shop.goodsAttr');
            }
        }catch (\Exception $e){
            DB::rollBack();
            flash("添加失败，错误码：201") -> error();
            return redirect()->route('shop.goodsAttr');
        }


    }


    public function attrDelete(Request $request,$id)
    {
        $model = GoodsAttr::find($id);
        if  (!$model) flash('操作失败')->error()->important();
        try {
            DB::beginTransaction();
            GoodsAttrValue::where('goods_attr_id','=',$id)->delete();
            $model->delete();
            DB::commit();
            return   redirect()->route('shop.goodsAttr');
        } catch (\Exception $e) {
            DB::rollBack();
            flash('操作失败')->error()->important();
        }
    }

    // 商品信息新增 and 修改
    public function store (Request $request)
    {
        // 判断是否传值
        $validate = Validator::make($request->all(),[
            'goods_cate_id'=>'required',
            'name' => 'required',
            'merchants_goods_type' => 'required',
            'desc' => 'required',
            'img' => 'required',
            'price' => 'required',
            'weight' => 'required',
        ],[
            'name.required'=>'缺少名称',
            'merchants_goods_type.required'=>'缺少分类',
            'img.required'=>'缺少封面图片',
            'price.required'=>'缺少基础价格',
            'weight.required'=>'缺少重量',
        ]);

        if ($validate->fails()) {
            flash($validate->errors()->first())->error()->important();
            return redirect()->route('shop.create');
        }
        // 商品详情修改
        $all = \request() -> all();
        // 判断执行新增方法还是执行修改方法
        if($request -> input('goods_id')){
            // 执行修改方法
            // 判断是否上传新文件
            $choose_file = $_FILES['choose-file'];
            // 如果第一个文件为空，则未上传新文件
            if($choose_file['name'][0] == ""){
                // 判断是否传值
                $validate = Validator::make($request->all(),[
                    'choose_file'=>'required'
                ],[
                    'choose_file.required'=>'缺少详细图片'
                ]);

                if ($validate->fails()) {
                    flash($validate->errors()->first())->error()->important();
                    return redirect()->route('shop.goods');
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
            // 获取提交的数据
            $data = [
                'goods_cate_id' => ','.$request->input('goods_cate_id').','.$request->input('goods_cate_id1').','.$request->input('goods_cate_id2').',',
                'name' => $all['name'],
                'img' => $all['img'],
                'price' => $all['price'],
                'weight' => $all['weight'],
                'dilivery' => $all['dilivery'],
                'desc' => $all['desc'],
                'is_sale' => $all['is_sale'],
                'merchants_goods_type_id' => $all['merchants_goods_type'],
                'album' => $album,
            ];
            // 链接数据库，修改内容
            $i = DB::table('goods') -> where('id',$all['goods_id']) -> update($data);
            // 根据获取的id 查询商品参数表
            $goodssku = DB::table('goods_sku') -> where('goods_id',$all['goods_id']) -> get();
            if(count($goodssku) != 0){
                foreach ($goodssku as $k =>$v){
                    $a = json_decode($goodssku,true)[$k]['attr_value'];
                    $goodssku_value[] = json_decode($a,true);
                }
                // 将三维数组转换成二维数组
                foreach ($goodssku_value as $v){
                    $new_arr[] = $v[0]['value'];
                }
                // 将二维数组转换成一维数组
                $newarray = [];
                foreach($new_arr as $key=>$val){
                    foreach ($val as $k=>$v){
                        $newarray[$key]=$v;
                    }
                }
                $old_arr = call_user_func_array('array_merge',$new_arr);
            }else{
                $old_arr = [];
            }
            $goodsdata = DB::table('goods') -> where('id',$all['goods_id']) -> first();
            // 判断是否修改成功
            if($i){
                $goodsCate = GoodsCate::with(['children'=>function($res){
                    $res->with('children');
                }])->where('pid','=',0)
                    ->get();

                // 查询运费模板表
                $express_modeldata = DB::table('express_model') -> get();
                $goodBrands = GoodBrands::select('id','name')->orderBy('id','asc')->get();
                // 查询商品参数
                $attrData = DB::table('goods_attr') -> get();
                // 查询商品分类
                $merchants_goods_type = DB::table('merchants_goods_type')
                    -> where('is_del',1)
                    -> where('merchant_id',Auth::id())
                    -> select('id','name')
                    -> get();
                $goods_attr = DB::table('goods_attr') -> get();
                $a = DB::table('goods_attr_value') -> get();
                $arr = [
                    'goodsCate'=>$goodsCate,
                    'goodsdata'=>$goodsdata,
                    'goodBrands'=>$goodBrands,
                    'express_modeldata'=>$express_modeldata,
                    'attrData'=>$attrData,
                    'goods_attr'=>$goods_attr,
                    'merchants_goods_type'=>$merchants_goods_type,
                    'attrvalueData'=>$a,
                    'goods_id'=>$all['goods_id'],
                    'goodssku'=> $old_arr,
                    'goods_album'=> json_decode($goodsdata->album)
                ];
                flash('修改成功') -> success();
                return $this->view('addGoods',$arr);
            }else{
                $goodsCate = GoodsCate::with(['children'=>function($res){
                    $res->with('children');
                }])->where('pid','=',0)
                    ->get();

                // 查询运费模板表
                $express_modeldata = DB::table('express_model') -> get();
                $goodBrands = GoodBrands::select('id','name')->orderBy('id','asc')->get();
                // 查询商品参数
                $attrData = DB::table('goods_attr') -> get();
                // 查询商品分类
                $merchants_goods_type = DB::table('merchants_goods_type')
                    -> where('is_del',1)
                    -> where('merchant_id',Auth::id())
                    -> select('id','name')
                    -> get();
                $a = DB::table('goods_attr_value') -> get();
                $arr = [
                    'goodsCate'=>$goodsCate,
                    'goodsdata'=>$goodsdata,
                    'goodBrands'=>$goodBrands,
                    'express_modeldata'=>$express_modeldata,
                    'attrData'=>$attrData,
                    'merchants_goods_type'=>$merchants_goods_type,
                    'attrvalueData'=>$a,
                    'goods_id'=>$all['goods_id'],
                    'goodssku'=> $old_arr,
                    'goods_album'=> json_decode($goodsdata->album),
                ];
                flash('未修改任何内容') -> success();
                return $this->view('addGoods',$arr);
            }
        }else{
            // 执行新增方法
            $model = new Goods();

            if ($request->input('id')) {
                $model = Goods::find($request->input('id'));
            }

            // 获取上传的文件
            $choose_file = $_FILES['choose-file'];
            //判断第一个文件名是否为空
            if ($choose_file['name'][0] == "") {
                flash("请选择详情图片") -> error();
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
            // 获取上传的图片路径
            $img_array = json_encode($img_array);

            $model->goods_cate_id = ','.$request->input('goods_cate_id').','.$request->input('goods_cate_id1').','.$request->input('goods_cate_id2').',';

            $model->name = $request->input('name');
            $model->merchants_goods_type_id = $request->input('merchants_goods_type');
            $model->img = $request->input('img');
            $model->price = $request->input('price');
            $model->weight = $request->input('weight');
            $model->desc = $request->input('desc');
            $model->is_sale = $request->input('is_sale');
            $model->dilivery = $request->input('dilivery');
            $model->album = $img_array;
            $model->merchant_id = Auth::id();
            try {
                $model->save();

                $goodsCate = GoodsCate::with(['children'=>function($res){
                    $res->with('children');
                }])->where('pid','=',0)
                    ->get();
                // 根据获取的id 查询商品参数表
                $goodssku = DB::table('goods_sku') -> where('goods_id',$all['goods_id']) -> get();
                if(count($goodssku) != 0){
                    foreach ($goodssku as $k =>$v){
                        $a = json_decode($goodssku,true)[$k]['attr_value'];
                        $goodssku_value[] = json_decode($a,true);
                    }
                    // 将三维数组转换成二维数组
                    foreach ($goodssku_value as $v){
                        $new_arr[] = $v[0]['value'];
                    }
                    // 将二维数组转换成一维数组
                    $newarray = [];
                    foreach($new_arr as $key=>$val){
                        foreach ($val as $k=>$v){
                            $newarray[$key]=$v;
                        }
                    }
                    $old_arr = call_user_func_array('array_merge',$new_arr);
                }else{
                    $old_arr = [];
                }
                // 查询运费模板表
                $express_modeldata = DB::table('express_model') -> get();
                $level1 = GoodsCate::where('pid','=',0)->get();
                $goodBrands = GoodBrands::select('id','name')->orderBy('id','asc')->get();
                // 查询商品参数
                $attrData = DB::table('goods_attr') -> get();
                // 查询商品分类
                $merchants_goods_type = DB::table('merchants_goods_type')
                    -> where('is_del',1)
                    -> where('merchant_id',Auth::id())
                    -> select('id','name')
                    -> get();
                $goods_attr = DB::table('goods_attr') -> get();
                $a = DB::table('goods_attr_value') -> get();
                $arr = [
                    'goodsCate'=>$goodsCate,
                    'goodBrands'=>$goodBrands,
                    'attrData'=>$attrData,
                    'merchants_goods_type'=>$merchants_goods_type,
                    'attrvalueData'=>$a,
                    'express_modeldata'=>$express_modeldata,
                    'goods_id'=>$model->id,
                    'goodssku'=> $old_arr,
                    'goods_attr'=> $goods_attr,
                    'goodsdata' =>(object)[
                        'goods_cate_id'=>'',
                        'name'=>$request->input('name'),
                        'merchants_goods_type_id'=>$request->input('merchants_goods_type'),
                        'img'=>$request->input('img'),
                        'price'=>$request->input('price'),
                        'weight'=>$request->input('weight'),
                        'desc'=>$request->input('desc'),
                        'is_sale'=>$request->input('is_sale'),
                        'dilivery'=>$request->input('dilivery'),

                    ]
                ];
                flash('新增成功,请继续下一步,上传参数') -> success();
                return $this->view('addGoods',$arr);
            } catch (\Exception $e){
                return $this->failed($e->getMessage());
            }
        }

    }

    public function addAlbum (Request $request)
    {
        $validate = Validator::make($request->all(),[
            'id'=>'required|exists:goods,id',
            'images'=>'required',

        ],[
            'id.required'=>'缺少商品',
            'id.exists'=>'无效的商品数据',
        ]);
        $model =  Goods::find($request->input('id'));
        $model->album  = '';
        if (is_array($request->filled('images')) && !empty($request->input('images'))){
            $model->album = implode(',',$request->input('images'));
        }

        try {
            $model->save();
            return $this->status('保存成功',['id'=>$model->id],200);
        } catch (\Exception $e) {
            return $this->failed($e->getMessage());
        }
    }

    public function distroy (Request $request)
    {
        return false;
    }

    public function setStatus (Request $request,$field,$status,$id)
    {
        $validate = Validator::make(['status'=>$status,'id'=>$id],[
            'status' => 'required',
            'id'     => 'required',
        ],[
            'status.required'=>'缺少状态值',
            'id.required'=>'缺少id',
        ]);

        if ($validate->fails()) {
            return $this->message('获取失败');
        }

        $model = Goods::find($id);
        $model->$field = $status;
        $model->save();
        return  redirect()->route('shop.goods');
    }
    public function goodsCate (Request $request)
    {
        $list = GoodsCate::select('id','name','img','sort','pid')
            ->orderBy('sort','asc')
            ->orderBy('pid','asc')
            ->get();
        $list = Tree::tree($list->toArray(),'name','id','pid');
        return $this->view('goodsCate',['list'=>$list]);
    }

    public function cateAdd (Request $request)
    {
        $list = GoodsCate::select('id','name','sort','pid')
            ->where('level','<','3')
            ->orderBy('sort','asc')
            ->orderBy('pid','asc')
            ->get();
        $list = Tree::tree($list->toArray(),'name','id','pid');
        return $this->view('cateAdd',['list'=>$list]);
    }

    public function cateEdit (Request $request,$id)
    {
        $cate = GoodsCate::find($id);
        $list = GoodsCate::select('id','name','sort','pid')
            ->where('level','<','3')
            ->orderBy('sort','asc')
            ->orderBy('pid','asc')
            ->get();
        $list = Tree::tree($list->toArray(),'name','id','pid');
        return $this->view('cateEdit',['list'=>$list,'cate'=>$cate]);

    }

    public function cateDelete(Request $request ,$id)
    {
        $model = GoodsCate::find($id);
        if ($model->delete()){
            return redirect()->route('shop.goodsCate');
        }
        return viewError('已删除或者删除失败');
    }

    public function cateStore (Request $request)
    {
        $validate = Validator::make($request->all(),[
            'name' => 'required',
            'sort' => 'required|numeric',
            'img' => 'required',
            'pid'=>'required',
        ],[
            'name.required'=>'名称必须',
            'sort.numeric'=>'排序必须是数字',
            'img.required'=>'请上传图片',
            'pid.required'=>'缺少上级'
        ]);


        if ($validate->fails()) {
            flash($validate->errors()->first())->error()->important();
            return redirect()->route('shop.cateAdd');
        }

        $model = new GoodsCate();
        if ($request->input('id')) {

            $model = GoodsCate::find($request->input('id'));
            if ($model->pid != $request->input('pid')){
                flash('操作失败，不能更改分类的上下级关系')->error()->important();
                return redirect()->route('shop.goodsCate');
            }
        }

        $model->name = $request->input('name');
        $model->sort = $request->input('sort');

        // 等级判断
        $model->level = 1;
        $model->roots = 0;
        if  ($request->input('pid') > 0) {
            $pmodel = GoodsCate::find($request->input('pid'));
            $model->level = ++$pmodel->level;
            $model->roots = $pmodel->roots . ',' .$pmodel->id;
        }

        $model->pid = $request->input('pid');
        $model->img = $request->input('img');

        if ($model->save()) {
            return   redirect()->route('shop.goodsCate');
        }
        return  viewError('操作失败','shop.cateAdd');
    }

    public function goodsBrand (Request $request)
    {
        $id = Auth::id();     // 当前登录用户的id
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('is_reg',1)
            -> first();
        // 如果当前用户是商家，则查询当前商户的商品
        if($i){
            $list = DB::table('goods_brands')
                -> join('merchants','goods_brands.merchant_id','=','merchants.id')
                -> where('merchants.user_id',$id)
                -> select(['goods_brands.id','goods_brands.name','goods_brands.img'])
                -> paginate(5);
        }else{
            $list = DB::table('goods_brands')
                -> join('merchants','goods_brands.merchant_id','=','merchants.id')
                -> select(['goods_brands.id','goods_brands.name','goods_brands.img'])
                -> paginate(5);
        }
        return $this->view('goodsBrand',['list'=>$list]);
    }

    public function brandAdd (Request $request)
    {
        return $this->view('brandAdd');
    }


    public function brandUpdate (Request $request,$id)
    {
        $brand = GoodBrands::find($id);
        return $this->view('brandUpdate',['brand'=>$brand]);
    }



    public function brandStore (Request $request)
    {
        $validate = Validator::make($request->all(),[
            'name' => 'required',
            'img' => 'required',
        ],[
            'name.required'=>'排序必须是数字',
            'img.required'=>'请上传图片',
        ]);


        if ($validate->fails()) {
            flash($validate->errors()->first())->error()->important();
            return redirect()->route('shop.brandAdd');
        }

        $model = new GoodBrands();
        if ($request->input('id')) {
            $model = GoodBrands::find($request->input('id'));
        }
        $model->name = $request->input('name');
        $model->img = $request->input('img');
        $model->merchant_id = Auth::id();

        if ($model->save()) {
            return   redirect()->route('shop.goodsBrand');
        }
        return  viewError('操作失败','shop.brandAdd');
    }


    public function brandDelete (Request $request ,$id)
    {
        $model = GoodBrands::find($id);
        if ($model->delete()){
            return redirect()->route('shop.goodsBrand');
        }
        return viewError('已删除或者删除失败');
    }

    public function express (Request $request)
    {
        $id = Auth::id();     // 当前登录用户的id
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('is_reg',1)
            -> first();
        // 如果当前用户是商家，则查询当前商户的商品
        if($i){
            $list = DB::table('express_model')
                -> join('merchants','express_model.merchant_id','=','merchants.id')
                -> where('merchants.user_id',$id)
                -> select(['express_model.id','express_model.name as exname','express_model.caculate_method','merchants.name as mename'])
                -> paginate(10);
        }else{
            $list = DB::table('express_model')
                -> join('merchants','express_model.merchant_id','=','merchants.id')
                -> select(['express_model.id','express_model.name as exname','express_model.caculate_method','merchants.name as mename'])
                -> paginate(10);
        }
        return $this->view('express',['list'=>$list]);
    }

    public function createExpress (Request $request)
    {

        return $this->view('createExpress');
    }

    public function updateExpress (Request $request,$id)
    {
        $data = ExpressModel::with('merchant')
            ->find($id);
        return $this->view('updateExpress',['data'=>$data]);
    }
    // 删除快递模板
    public function deleteExpress (Request $request,$id)
    {
        $model = ExpressModel::find($id);
        if  (!$model) flash('操作失败')->error()->important();
        try {
            DB::beginTransaction();
            ExpressAttr::where('express_model_id','=',$id)->delete();
            $model->delete();
            DB::commit();
            return   redirect()->route('shop.express');
        } catch (\Exception $e) {
            DB::rollBack();
            flash('操作失败')->error()->important();
        }
    }

    // 渲染列表
    public function addExpressAttrs (Request $request,$id)
    {
        $list   = ExpressAttr::with('city')->where('express_model_id',$id)->get();
        $ids = [];
        foreach ($list as $item) {
            $ids[]=$item->city_id;
        }

        $data   = ExpressModel::find($id);
        $city   = District::select('id','name','deep')->where('deep',0)->get();
        $express_modeldData = DB::table('express_model') -> where('id',$id) -> first();

        return $this->view('expressAttr',['list'=>$list,'data'=>$data,'city'=>$city,'ids'=>$ids,'express_modeldData'=>$express_modeldData]);
    }

    // 存储信息
    public function storeExpressAttrs (Request $request)
    {
        $validate = Validator::make($request->all(),[
            'express_id' => 'required',
            'caculate_method' => 'required',
            'ids'=>''
        ],[
            'express_id.required'=>'快递模板id必须',
            'caculate_method.required'=>'计量方式必须',
            'ids.required'=>'区域必须',
        ]);

        if ($validate->fails()) {
            flash($validate->errors()->first())->error()->important();
            return redirect()->route('shop.createExpress');
        }
//        return dd(\request() -> all());
        $all = \request() -> all();
        $ids = $request->input('ids');
        DB::beginTransaction();
        // 操作副表 先把副表中有此ID的数据删除，再向副表中添加内容
        try{
            // 删除附表中存在该id的数据
            DB::table('express_detail') -> where('express_model_id',$all['express_id']) -> delete();
                // 删除成功之后 向副表新增数据
            foreach ($all['ids'] as $v){
                $addDetailData = [
                    'express_model_id' => $all['express_id'],
                    'city_id' => $v
                   ];
                $i = DB::table('express_detail') -> insert($addDetailData);
            }
            $data = [
                'merchant_id' => Auth::id(),
                'caculate_method' => $all['caculate_method'],
                'num' => $all['num'],
                'basic_price' => $all['basic_price'],
                'unit_price' => $all['unit_price'],
            ];
            $m = DB::table('express_model') -> where('id',$all['express_id']) -> update($data);
            if($m){
                flash("修改成功")-> success();
                DB::commit();
            }

        }catch (\Exception $e){

        }
        return redirect()->route('shop.addExpressAttrs',['id'=>$request->input('express_id')]);
    }

    public function deleteExpressAttr (Request $request , $id)
    {
        $model = ExpressAttr::find($id);
        if  (!$model) flash('操作失败')->error()->important();
        try {
            $model->delete();
            return   redirect()->route('shop.addExpressAttrs',['id'=>$model->express_model_id]);
        } catch (\Exception $e) {
            flash('操作失败')->error()->important();
        }
    }


    public function storeExpress (Request $request)
    {
        $validate = Validator::make($request->all(),[
            'name' => 'required',
        ],[
            'name.required'=>'名称必须',
        ]);

        if ($validate->fails()) {
            flash($validate->errors()->first())->error()->important();
            return redirect()->route('shop.createExpress');
        }

        $model = new ExpressModel();
        if ($request->input('id')) {
            $model = ExpressModel::find($request->input('id'));
        }

        $admin = Auth::guard('admin')->user();
        $model->merchant_id = $admin->id;
        $model->name = $request->input('name');

        try {
            $model->save();
            flash("修改成功") -> success();
            return redirect()->route('shop.express');
        } catch ( \Exception $e) {
            flash($validate->errors()->first())->error()->important();
            return redirect()->route('shop.createExpress');
        }

    }
    public function hotkeywords(){
        $data=Db::table('hotsearch')->paginate(10);
        return $this->view('',['data'=>$data]);
    }
    public function hotkeywordsedit(){
        $all = request()->all();
        if (request()->isMethod('post')) {
            $save['name']=$all['name'];
            if (empty($all['id'])) {
                $save['status']=0;
                $save['created_at']=date('Y-m-d H:i:s',time());
                $re=Db::table('hotsearch')->insert($save);
            }else{
                $re=Db::table('hotsearch')->where('id',$all['id'])->update($save);
            }
            if ($re) {
                flash('修改成功')->success();
                return redirect()->route('shop.hotkeywords');
            }else{
                flash('修改失败')->error();
                return redirect()->route('shop.hotkeywords');
            }
        }else{
            if (empty($all['id'])) {
                $data = (object)[];
                
            }else{
                $data=Db::table('hotsearch')->where('id',$all['id'])->first();
            }
            return $this->view('',['data'=>$data]);
        }
    }
    public function hotkeywordsdel(){
         // 获取传入的id
        $all = request() -> all();
        // 根据id删除表中数据
        $data['status'] = $all['status'];
        $i = DB::table("hotsearch") ->where('id',$all['id']) ->update($data);
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('shop.hotkeywords');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('shop.hotkeywords');
        }
    }
}
