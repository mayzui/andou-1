<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class GoodsController extends Controller
{   
    /**
     * @api {post} /api/goods/index 在线商城
     * @apiName index
     * @apiGroup goods
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *          "banner": [
                    {
                        "id": '轮播id',
                        "img": "图片地址",
                        "url": "跳转地址"
                    }
                ],
                "category": [
                    {
                        "id": '分类id',
                        "img": "图片地址",
                        "name": "分类名字"
                    }
                    
                ],
                "recommend_goods": [
                    {
                        "id": '推荐商品id',
                        "img": "图片地址",
                        "name": "商品名字",
                        "price": "价格"
                    }
                ],
                "bargain_goods": [
                    {
                        "id": '特价商品id',
                        "img": "图片地址",
                        "name": "名字",
                        "price": "价格"
                    }
                ]
     *       },
     *       "msg":"查询成功"
     *     }
     */
    public function index(){
        $data['banner']=Db::table('banner')
        ->select('id','img','url')
        ->where(['banner_position_id'=>6,'status'=>1])
        ->orderBy('sort','ASC')
        ->get();
        $data['category']=Db::table('goods_cate')
        ->select('id','img','name')
        ->where(['pid'=>0])
        ->orderBy('sort','ASC')
        ->limit(8)
        ->get();
        $data['recommend_goods']=Db::table('goods')
        ->select('id','img','name','price')
        ->where(['is_recommend'=>1,'is_sale'=>1,'is_del'=>0])
        ->orderBy('created_at','DESC')
        ->limit(4)
        ->get();
        $data['bargain_goods']=Db::table('goods')
        ->select('id','img','name','price')
        ->where(['is_bargain'=>1,'is_sale'=>1,'is_del'=>0])
        ->orderBy('created_at','DESC')
        ->limit(4)
        ->get();
        return $this->rejson(200,'查询成功',$data); 
    }
    /**
     * @api {post} /api/goods/goods 商品详情数据
     * @apiName goods
     * @apiGroup goods
     * @apiParam {string} id 商品id
     * @apiParam {string} uid 用户id(非必传)
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *          "name": "商品名字",
                "img": "商品封面图",
                "album": "商品轮播图",
                "price": "价格",
                "dilivery": "运费",
                "volume": "销量",
                "store_num": "库存"
                "merchant": {
                    "id":"商家id"
                    "name": "商家名字",
                    "logo_img": "商家头像"
                },
                "is_collection": "1为以收藏 0未收藏"
     *       },
     *       "msg":"查询成功"
     *     }
     */
    public function goods() {
        $all=request()->all();
        if (!isset($all['id'])) {
            return $this->rejson(201,'缺少参数'); 
        }
        $data=DB::table('goods')
        ->select('name','merchant_id','weight','img','album','price','dilivery','volume')
        ->where('id',$all['id'])
        ->first();
        if ($data->album) {
            $data->album=json_decode($data->album,1);
        }else{
            $data->album='';
        }
        // echo $data->weight;exit();
        if ($data->dilivery > 0) {
            $data->dilivery=$this->freight($data->weight,1,$data->dilivery);
        }
        if(isset($all['uid'])){//添加浏览记录
            $pv=$this->seemerchant($all['uid'],$all['id'],1);
            if ($pv) {
                $re=DB::table('goods')->where('id',$all['id'])->increment('pv');
            }
            $collection=DB::table('collection')
            ->select('id')
            ->where(['user_id'=>$all['uid'],'pid'=>$all['id'],'type'=>1])
            ->first();
            // var_dump($data);exit();
            if(empty($collection)){
                $data->is_collection=0;
            }else{
                $data->is_collection=1;
            }
        }else{
            $data->is_collection=0;
        }
        
        if (isset($data->merchant_id)) {
            $data->merchant=Db::table('merchants')->select('id','name','logo_img')->where('id',$data->merchant_id)->first();
        }
        $store_num=DB::table('goods_sku')
        ->where('goods_id',$all['id'])
        ->sum('store_num');
        if($store_num){
            $data->store_num=$store_num;
        }else{
            $data->store_num=0;
        }

        return $this->rejson(200,'查询成功',$data); 
    }
    /**
     * @api {post} /api/goods/good_list 产品列表
     * @apiName good_list
     * @apiGroup goods
     * @apiParam {string} keyword 关键字查询(非必传)
     * @apiParam {string} cate_id 分类id查询(非必传)
     * @apiParam {string} is_recommend 查询推荐产品传1(非必传)
     * @apiParam {string} is_bargain 查询特价产品传1(非必传)
     * @apiParam {string} price_sort 价格排序(非必传1为倒序,0为正序)
     * @apiParam {string} volume_sort 销量排序(非必传1为倒序,0为正序)
     * @apiParam {string} start_sort 信誉排序(非必传1为倒序,0为正序)
     * @apiParam {string} page 分页参数
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":  [
                {
                    "name": "商品名字",
                    "img": "商品图片",
                    "price": "价格",
                    "id": "商品id"
                }
             ],   
     *       "msg":"查询成功"
     *     }
     */
    public function goodList(){
        $all=request()->all();
        $num=10;
        $where[]=['g.is_sale',1];
        $where[]=['g.is_del',0];
        if (isset($all['page'])) {
            $pages=($all['page']-1)*$num;
        }else{
            $pages=0;
        }
        if (!empty($all['is_recommend'])) {//推荐
           $where[]=['g.is_recommend',1];
        }
        if (!empty($all['is_bargain'])) {//特价
           $where[]=['g.is_bargain',1];
        }
        if (isset($all['cate_id'])) {
            $where[]=['g.goods_cate_id', 'like', '%,'.$all['cate_id'].',%'];
        }
        if (isset($all['keyword'])) {
            $where[]=['g.name', 'like', '%'.$all['keyword'].'%'];
        }
        $orderBy='g.pv';
        $sort='DESC';
        if (isset($all['price_sort'])) {
            if ($all['price_sort']==1) {
               $orderBy='g.price'; 
            }else{
               $orderBy='g.price';
               $sort='ASC'; 
            }
        }
        if (isset($all['volume_sort'])) {
            if ($all['volume_sort']==1) {
               $orderBy='g.volume'; 
            }else{
               $orderBy='g.volume';
               $sort='ASC';  
            }
        }
        if (isset($all['start_sort'])) {
            if ($all['start_sort']==1) {
               $orderBy='cnum'; 
            }else{
               $orderBy='cnum';
               $sort='ASC';  
            }
        }
        $data=Db::table('goods as g')
        ->select('g.name','g.img','g.price','g.id',DB::raw("count(c.content) as cnum"))
        ->leftJoin('order_commnets as c','c.goods_id','=','g.id')
        ->where($where)
        ->orderBy($orderBy,$sort)
        ->offset($pages)
        ->limit($num)
        ->groupBy('g.id')
        ->get();
        return $this->rejson('200','查询成功',$data);

    }
    /**
     * @api {post} /api/goods/details 商品详情展示
     * @apiName details
     * @apiGroup goods
     * @apiParam {string} id 商品id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *          "details": "商品详情"
     *       },
     *       "msg":"查询成功"
     *     }
     */
    public function details(){
        $all=request()->all();
        if (!isset($all['id'])) {
           return $this->rejson(201,'缺少参数');  
        }
        $desc=Db::table('goods')
        ->select('desc')
        ->where('id',$all['id'])
        ->first();
        if (isset($desc->desc)) {
            $data['details']=$desc->desc;
        }else{
            $data['details']='';
        }
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/goods/comment 商品评论
     * @apiName comment
     * @apiGroup goods
     * @apiParam {string} id 商品id
     * @apiParam {string} page 页码数(非必传)
     * @apiSuccessExample 参数返回:
     *     {
     *      "code": "200",
     *      "data": [
                {
                    "avator": "用户头像",
                    "name": "用户名字",
                    "id": "评论id",
                    "stars": "评论星级",
                    "content": "评论内容",
                    "created_at": "评论时间"
                }
            ],
     *      "msg":"查询成功"
     *     }
     */
    public function comment(){
        $all=request()->all();
        $num=10;
        if (isset($all['page'])) {
            $pages=($all['page']-1)*$num;
        }else{
            $pages=0;
        }
        if (!isset($all['id'])) {
           return $this->rejson(201,'缺少参数');  
        }
        $data=Db::table('order_commnets as c')
        ->join("users as u","c.user_id","=","u.id")
        ->select('u.avator','u.name','c.id','c.stars','c.content','c.created_at')
        ->where(['c.goods_id'=>$all['id'],'c.status'=>1,'c.is_del'=>0,])
        ->orderBy('created_at','DESC')
        ->offset($pages)
        ->limit($num)
        ->get();
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/goods/specslist 商品规格
     * @apiName specslist
     * @apiGroup goods
     * @apiParam {string} id 商品id
     * @apiSuccessExample 参数返回:
     *     {
     *      "code": "200",
     *      "data": "",
     *      "msg":"收藏成功"
     *     }
     */
    public function specslist(){
        $all=request()->all();
        if (!isset($all['id'])) {
           return $this->rejson(201,'缺少参数');  
        }
        $data=Db::table('goods_sku')->where(['goods_id'=>$all['id'],'is_valid'=>1])->get();
        
        $datas=json_decode($data[0]->attr_value,1)[0]['name'];
        foreach ($datas as $k => $v) {
            $res[$k]['name']=$v;
            $res[$k]['value']=[];
            foreach ($data as $key => $value) {
                $re=json_decode($value->attr_value,1)[0]['value'];
                if (!in_array($re[$k],$res[$k]['value'])) {
                    $res[$k]['value'][]=$re[$k];   
                }  
            }   
        }
        foreach ($data as $key => $value) {
                $re=json_decode($value->attr_value,1)[0]['value'];
                $re=implode('-',$re);
                $arr=array('id'=>$value->id,'price'=>$value->price,'num'=>$value->store_num);
                $specs[$re]=$arr; 
        }
        $goodspecs['price']=$specs;
        $goodspecs['res']=$res;
        return $this->rejson(200,'查询成功',$goodspecs);
    }
    /**
     * @api {post} /api/goods/collection 商品收藏或取消收藏
     * @apiName collection
     * @apiGroup goods
     * @apiParam {string} id 商品id
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 用户验证
     * @apiParam {string} type 1收藏 0取消收藏
     * @apiSuccessExample 参数返回:
     *     {
     *      "code": "200",
     *      "data": "",
     *      "msg":"收藏成功"
     *     }
     */
    public function collection(){
        $all=request()->all();
        $token=request()->header('token')??'';
        if ($token!='') {
            $all['token']=$token;
        }
        if (empty($all['uid'])||empty($all['token'])) {
           return $this->rejson(202,'登陆失效');
        }
        $check=$this->checktoten($all['uid'],$all['token']);
        if ($check['code']==202) {
           return $this->rejson($check['code'],$check['msg']);
        }
        if (!isset($all['id']) || !isset($all['type'])) {
           return $this->rejson(201,'缺少参数');  
        }
        if ($all['type']==1) {
            $data['type']=1;
            $data['user_id']=$all['uid'];
            $data['pid']=$all['id'];
            $data['created_at']=date('Y-m-d H:i:s',time());
            $res=Db::table('collection')
            ->where(['user_id'=>$all['uid'],'pid'=>$all['id'],'type'=>1])
            ->first();
            if (!empty($res)) {
                return $this->rejson(201,'商品已收藏');
            }
            Db::table('collection')->insert($data);
            return $this->rejson(200,'收藏成功');
        }else{
            Db::table('collection')
            ->where(['user_id'=>$all['uid'],'pid'=>$all['id'],'type'=>1])
            ->delete();
            return $this->rejson(200,'取消收藏成功');
        }
    }
    /**
     * @api {post} /api/goods/follow 商家关注或取消关注
     * @apiName follow
     * @apiGroup goods
     * @apiParam {string} id 商家id
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 用户验证
     * @apiParam {string} type 1关注 0取消关注
     * @apiSuccessExample 参数返回:
     *     {
     *      "code": "200",
     *      "data": "",
     *      "msg":"关注成功"
     *     }
     */
    public function follow(){
        $all=request()->all();
        $token=request()->header('token')??'';
        if ($token!='') {
            $all['token']=$token;
        }
        if (empty($all['uid'])||empty($all['token'])) {
           return $this->rejson(202,'登陆失效');
        }
        $check=$this->checktoten($all['uid'],$all['token']);
        if ($check['code']==202) {
           return $this->rejson($check['code'],$check['msg']);
        }
        if (!isset($all['id']) || !isset($all['type'])) {
           return $this->rejson(201,'缺少参数');  
        }
        if ($all['type']==1) {
            $data['type']=3;
            $data['user_id']=$all['uid'];
            $data['pid']=$all['id'];
            $data['created_at']=date('Y-m-d H:i:s',time());
            $res=Db::table('collection')
            ->where(['user_id'=>$all['uid'],'pid'=>$all['id'],'type'=>3])
            ->first();
            if (!empty($res)) {
                return $this->rejson(201,'商品已关注');
            }
            Db::table('collection')->insert($data);
            return $this->rejson(200,'关注成功');
        }else{
            Db::table('collection')
            ->where(['user_id'=>$all['uid'],'pid'=>$all['id'],'type'=>3])
            ->delete();
            return $this->rejson(200,'取消关注成功');
        }
    }
    /**
     * @api {post} /api/goods/goods_cate 商品分类
     * @apiName goods_cate
     * @apiGroup goods
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
     *           {
                    "id": "一级分类id",
                    "name": "一级分类名字",
                    "towcate": [
                        {
                            "id": "二级分类id",
                            "name": "二级分类名字",
                            "img": "分类图片"
                        }
                    ]
                }
     *       ],
     *       "msg":"查询成功"
     *     }
     */
    public function goodsCate()
    {
        $data=DB::table('goods_cate')
        ->select('id','name')
        ->where('pid',0)
        ->get();
        foreach ($data as $key => $value) {
            $data[$key]->towcate=DB::table('goods_cate')
            ->select('id','name','img')
            ->where('pid',$value->id)
            ->get();
        }
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/goods/cate 商品分类联动
     * @apiName cate
     * @apiGroup goods
     * @apiParam {string} id 上级分类id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
     *           {
                    "id": "一级分类id",
                    "name": "一级分类名字",
                    "img": "分类图片"
                }
     *       ],
     *       "msg":"查询成功"
     *     }
     */
    public function cate(){
        $all=request()->all();
        if (empty($all['id'])) {
           $pid=0;
        }else{
           $pid=$all['id'];
        }
        $data=DB::table('goods_cate')
        ->select('id','name','img')
        ->where('pid',$pid)
        ->get();
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/goods/hotsearch 热门搜索
     * @apiName hotsearch
     * @apiGroup goods
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
     *          {
                    "id": "关键词id",
                    "name": "搜索关键词"
                }
     *       ],
     *       "msg":"查询成功"
     *     }
     */
    public function hotsearch(){
        $data=DB::table('hotsearch')
        ->select('id','name')
        ->where('status',1)
        ->get();
        return $this->rejson(200,'查询成功',$data);
    }
}