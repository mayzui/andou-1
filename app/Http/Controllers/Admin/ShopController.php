<?php

namespace App\Http\Controllers\Admin;

use App\Models\District;
use App\Models\ExpressAttr;
use App\Models\ExpressModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Handlers\Tree;
use App\Models\GoodBrands;
use App\Models\Goods;
use App\Models\Statics;
use Illuminate\Support\Facades\Input;
use App\Models\Orders;
use App\Models\GoodsAttr;
use App\Models\GoodsAttrValue;
use App\Models\GoodsCate;
use App\Models\Merchant;
use App\Libraires\ApiResponse;
use Auth;


class ShopController extends BaseController
{
    use ApiResponse;
    protected $merchant_type_id = 2;

    public function goods(Request $request ,Auth $auth)
    {
        $admin = Auth::guard('admin')->user();
        $list = Goods::with(['goodsCate','goodBrands'])
            ->orderBy('created_at','desc')
            ->where(function($res) use ($admin){
//                $res->where('user_id',$admin->id);
            })
            ->paginate($request->input('limit'));
        return $this->view('goods',['list'=>$list]);
    }

    public function create (Request $request)
    {
        $goodsCate = GoodsCate::with(['children'=>function($res){
            $res->with('children');
        }])->where('pid','=',0)
            ->get();

        $level1 = GoodsCate::where('pid','=',0)->get();
        $goodBrands = GoodBrands::select('id','name')->orderBy('id','asc')->get();
        return $this->view('addGoods',['goodsCate'=>$goodsCate,'goodBrands'=>$goodBrands]);
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
        $admin = Auth::guard('admin')->user();
        $list = GoodsAttr::orderBy('id','desc')
            ->where('user_id','=',$admin->id)
            ->paginate($request->input('limit'));
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
        $data = GoodsAttr::with('attrValue')->find($request->input('id'));
        if ($data) {
            $result = ['code'=>200,'data'=>$data];
            echo  json_encode($result);
        }
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

    public function attrStore (Request $request)
    {
        $validate = Validator::make($request->all(),[
            'name' => 'required',
            'is_sale_attr' => 'required',
        ],[
            'name.required'=>'名称必须',
            'is_sale_attr.numeric'=>'排序必须是数字',
        ]);


        if ($validate->fails()) {
            flash($validate->errors()->first())->error()->important();
            return redirect()->route('shop.addAttr');
        }

        $model = new GoodsAttr();
        if ($request->input('id')) {
            $model = GoodsAttr::find($request->input('id'));
        }


        $admin = Auth::guard('admin')->user();
        $model->user_id = $admin->id;

        $merchant =  Merchant::where('user_id','=',$admin->id)
            ->where('merchant_type_id',$this->merchant_type_id)
            ->first();

        // 判断是哪个商户或者修改  上线后可以删除判断

        $model->merchant_id = 0;
        if ($merchant) {
            $model->merchant_id = $merchant->id;
        }

        $model->name = $request->input('name');
        $model->is_sale_attr = $request->input('is_sale_attr');

        if ($model->save()) {
            return   redirect()->route('shop.goodsAttr');
        }
        return  viewError('操作失败','shop.addAttr');
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


    public function update(Request $request,$id)
    {
        return $this->view('update',['data'=>$data]);
    }

    public function store (Request $request)
    {
        $validate = Validator::make($request->all(),[
            'goods_cate_id'=>'required',
            'goods_brand_id'=>'required',
            'name' => 'required',
            'desc' => 'required',
            'img' => 'required',
            'price' => 'required',
            'is_hot' => 'required',
            'is_recommend' => 'required',
            'is_bargain' => 'required',
            'is_team_buy' => 'required',
        ],[
            'goods_cate_id.required'=>'缺少分类',
            'goods_brand_id.required'=>'缺少品牌',
            'name.required'=>'缺少名称',
            'desc.required'=>'缺少描述',
            'img.required'=>'缺少图片',
            'price.required'=>'缺少基础价',
        ]);

        if ($validate->fails()) {
            flash($validate->errors()->first())->error()->important();
            return redirect()->route('shop.create');
        }

        $model = new Goods();

        if ($request->input('id')) {
            $model = Goods::find($request->input('id'));
        }

        $model->goods_cate_id = $request->input('goods_cate_id');
        $model->goods_brand_id = $request->input('goods_brand_id');
        $model->name = $request->input('name');
        $model->img = $request->input('img');
        $model->desc = $request->input('desc');
        $model->price = $request->input('price');

        $model->is_hot = $request->input('is_hot',0);
        $model->is_recommend = $request->input('is_recommend',0);
        $model->is_bargain = $request->input('is_bargain',0);
        $model->is_team_buy = $request->input('is_team_buy',0);
        try {
            $model->save();
            return $this->status('保存成功',['id'=>$model->id],200);
        } catch (\Exception $e) {
            return $this->failed($e->getMessage());
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



    /*
        * 订单数据展示
        * */
    public function orders (Request $request)
    {
        $list = Orders::orderBy('id','desc')->where('is_del',0)->paginate($request->input('limit'));
//        var_dump($list);die;
        return $this->view('orders',['list'=>$list]);
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
            'pay_discount'=>$input['pay_discount'],
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
        $id = input::get('id');
        $res = Orders::where('id',$id)->update(['is_del' => 1]);
        if ($res){
            return redirect()->route('shop.orders');
        }
        return viewError('已删除或者删除失败');

    }

    public function goodsBrand (Request $request)
    {
        $list = GoodBrands::orderBy('id','desc')->paginate($request->input('limit'));
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

    public function statics (Request $request)
    {
        $data =  Statics::orderBy('id','desc')->where('is_del',0)->paginate($request->input('limit'));
        return $this->view('statics',['data'=>$data]);
    }

    public function express (Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $list = ExpressModel::with('merchant')->where('merchant_user_id',$admin->id)->paginate();
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

        return $this->view('expressAttr',['list'=>$list,'data'=>$data,'city'=>$city,'ids'=>$ids]);
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

        $ids = $request->input('ids');

        try {

            // 先删除 不在里面的
            foreach ($ids as $v=>$id) {
                $model = ExpressAttr::where('city_id',$id)
                    ->where('express_model_id',$request->input('express_id'))
                    ->first();
                if (!$model) {
                    $model = new ExpressAttr();
                }
                $model->express_model_id = $request->input('express_id');
                if ($request->filled('caculate_method')) $model->caculate_method = $request->input('caculate_method');
                if ($request->filled('basic_price')) $model->basic_price = $request->input('basic_price');
                if ($request->filled('unit_price')) $model->unit_price = $request->input('unit_price');
                $model->city_id = $id;
                $model->save();
            }
            // 删除不在ids 中的行
            $res =  ExpressAttr::where('express_model_id',$request->input('express_id'))->whereNotIn('city_id',$ids)->delete();

        } catch (\Exception $e) {
            flash($e->getMessage())->error()->important();
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
            'ship_address' => 'required',
            'is_free' => 'required',
        ],[
            'name.required'=>'名称必须',
            'ship_address.required'=>'宝贝地址必须',
            'is_free.required'=>'名称是否包邮',
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
        $model->merchant_user_id = $admin->id;
        $model->name = $request->input('name');
        $model->ship_address = $request->input('ship_address');
        $model->is_free = $request->input('is_free');

        try {
            $model->save();
            return redirect()->route('shop.express');
        } catch ( \Exception $e) {
            flash($validate->errors()->first())->error()->important();
            return redirect()->route('shop.createExpress');
        }

    }
}
