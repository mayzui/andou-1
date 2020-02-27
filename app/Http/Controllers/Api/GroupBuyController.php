<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PuzzleGoodsModel;
use App\Models\PuzzleGroupModel;
use App\Models\PuzzleUserModel;
use App\Services\GroupService;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class GroupBuyController extends Controller
{
    protected $service;

    public function __construct(GroupService $groupService)
    {
        $this->service = $groupService;
    }

    /**
     * @api {get} /api/group/today_top 团购今日必拼热门推荐
     * @apiName today_hot_group
     * @apiGroup goods
     * @apiSuccessExample 参数返回:
     *     {
            "code": 200,
            "msg": "ok",
            "data": {
                "today_goods": [
                    {
                    "id": "团购id",
                    "goods_id": "商品id",
                    "sku_id": "规格id",
                    "top_member": "团队人数上限",
                    "price": "团购价",
                    "name": "名称",
                    "img": "图片",
                    "total_member": 0
                    },
                    {
                    "id": 19,
                    "goods_id": 20,
                    "sku_id": 48,
                    "top_member": 10,
                    "price": "32.00",
                    "name": "资生堂可悠然（KUYURA）美肌 沐浴露套装",
                    "img": "http://andou.test/uploads/d4ddeb2cb5dc5846aacd7bc610d714da.jpg",
                    "total_member": 0
                    },
                    {
                    "id": 9,
                    "goods_id": 46,
                    "sku_id": 110,
                    "top_member": 10,
                    "price": "31.00",
                    "name": "【情人节限量】纪梵希红丝绒n37口红套装 散粉 心无禁忌香水正品",
                    "img": "http://andou.test/uploads/99f2f434793a671a62a274a6ecfeb8ab.jpg",
                    "total_member": 0
                    }
                ],
                "hot_goods": [
                    {
                    "id": 3,
                    "goods_id": 3,
                    "sku_id": 65,
                    "top_member": 10,
                    "price": "35.00",
                    "name": "牛仔裤",
                    "img": "http://andou.test/uploads/ac65c83e8eb160adf097f98d4ad441ae.jpg",
                    "total_member": 0
                    },
                    {
                    "id": 8,
                    "goods_id": 47,
                    "sku_id": 111,
                    "top_member": 10,
                    "price": "30.00",
                    "name": "【新年礼物】纪梵希节日限量 禁忌之吻霓虹唇膏口红N28 N27 散粉",
                    "img": "http://andou.test/uploads/05cafd118675c550b5e0202a64dab5b6.jpg",
                    "total_member": 0
                    },
                    {
                    "id": 29,
                    "goods_id": 40,
                    "sku_id": 92,
                    "top_member": 10,
                    "price": "32.00",
                    "name": "美宝莲 (MAYBELLINE) 巨遮瑕新颜霜 30ml（象牙色 按压喷头 BB霜 油皮亲妈 粉底液 遮瑕提亮肤色不易脱妆）",
                    "img": "http://andou.test/uploads/8e6e58e87c57f091c73dd940dca8eb8f.jpg",
                    "total_member": "参与总人数"
                    }
                ]
            }
        }
     */
    public function todayHot()
    {
        $today_goods = $this->service->topGoodsByType(1);
        $hot_goods = $this->service->topGoodsByType(2);

        return $this->responseJson(200, 'ok', compact('today_goods', 'hot_goods'));
    }

    /**
     * @api {get} /api/group/group_cate 团购商品分类
     * @apiName group_cate
     * @apiGroup goods
     * @apiSuccessExample 参数返回:
     *    {
            "code": 200,
            "msg": "ok",
            "data": [
            {
                "id": 2,
                "name": "美妆"
            },
            {
                "id": 21,
                "name": "进口尖货"
            },
            {
                "id": 22,
                "name": "服饰内衣"
            },
            {
                "id": 23,
                "name": "鞋包配饰"
            },
            {
                "id": 24,
                "name": "家纺家电"
            },
            {
                "id": 25,
                "name": "居家百货"
            },
            {
                "id": 26,
                "name": "休闲美食"
            },
            {
                "id": 55,
                "name": "手机配件"
            }
            ]
        }
     */
    public function topCate()
    {
        $cates = DB::table('goods_cate')
                ->where('level', 1)
                ->select(['id', 'name'])
                ->get()->toArray();
        
        return $this->responseJson(200, 'ok', $cates);
    }

    /**
     * @api {get} /api/group/group_list/{cate_id}/{page} 团购商品列表
     * @apiName group_list
     * @apiGroup goods
     * @apiSuccessExample 参数返回:
     *    {
            "code": 200,
            "msg": "ok",
            "data": [
                {
                    "id": "团购id",
                    "goods_id": "商品id",
                    "sdu_id": "规格id",
                    "price": "团购价",
                    "storage": "库存",
                    "name": "商品名称",
                    "img": "商品图片",
                    "old_price": "商品原价",
                    "total_member": "已拼团人数",
                    "buy_total": "已购总数",
                    "sale_percent": "已购商品百分比"
                },
                {
                    "id": 11,
                    "goods_id": 12,
                    "price": "32.00",
                    "storage": 100,
                    "name": "小天才儿童手表",
                    "img": "http://andou.test/uploads/c5f3b83fc0aaf0a18840d29f5be63bc8.jpg",
                    "old_price": "99.00",
                    "total_member": 0,
                    "buy_total": 0,
                    "sale_percent": 0
                },
                {
                    "id": 12,
                    "goods_id": 13,
                    "price": "32.00",
                    "storage": 100,
                    "name": "老坑冰种玉石正阳绿飘花缅甸翡翠手镯圆条玉镯子天然A货正品证书",
                    "img": "http://andou.test/uploads/f2d4ee7afc8219369a81168969f3ae41.jpg",
                    "old_price": "299.00",
                    "total_member": 0,
                    "buy_total": 0,
                    "sale_percent": 0
                },
                {
                    "id": 13,
                    "goods_id": 14,
                    "price": "32.00",
                    "storage": 100,
                    "name": "MASUNAGA 增永眼镜 日本手工眼镜 方框小脸眼镜框 GMS 820",
                    "img": "http://andou.test/uploads/c9178ba7ec072eec22a356ff4346b477.jpg",
                    "old_price": "59.00",
                    "total_member": 0,
                    "buy_total": 0,
                    "sale_percent": 0
                },
                {
                    "id": 14,
                    "goods_id": 15,
                    "price": "32.00",
                    "storage": 100,
                    "name": "海氏海诺创可贴透明防水透气医用隐形创口贴伤口止血贴ok绷100片",
                    "img": "http://andou.test/uploads/cf04a3a2b42dbb93b48e95a70a5b2299.jpg",
                    "old_price": "49.00",
                    "total_member": 0,
                    "buy_total": 0,
                    "sale_percent": 0
                }
            ]
    }
     */
    public function groupGoodsListByCate($cate_id = 0, $page = 1)
    {
        if (empty($cate_id) || ! is_numeric($cate_id) || $cate_id < 1) {
            return $this->responseJson(201, '参数错误');
        }
        if (! is_numeric($page) || $page < 1) {
            return $this->responseJson(201, '参数错误');
        }

        $list = $this->service->normalGoodsByCate($cate_id, $page);

        return $this->responseJson(200, 'ok', $list);
    }

    /**
     * @api {get} /api/group/puzzle_detail/{id} 团购明细,id为团购id
     * @apiName puzzle_detail
     * @apiGroup goods
     * @apiSuccessExample 参数返回:
     *   {
            "code": 200,
            "msg": "ok",
            "data": {
                "group_goods": {
                    "goods_id": "商品id",
                    "sku_id": "规格id",
                    "price": "团购价格",
                    "storage": "团购库存",
                    "top_member": "单团人数上限",
                    "begin_time": "团购开始时间",
                    "finish_time": "团购结束时间",
                    "code": "状态0上架 1下架"
                    "sale_total": "团购销量"
                },
                "total_member": "参与团购总人数",
                "team_list": [
                    {
                        "group_id": "团队id",
                        "left_member": "剩余空位",
                        "captain_avatar": "队长头像"
                    },
                    {
                        "group_id": 1,
                        "left_member": 8,
                        "captain_avatar": "http://andou.test/images/7520e6faa309a1eed8a4fd95fb49770.jpg"
                    }
                ],
                "status": "团购状态 1团购中 0未开始 2已结束",
                "now": "当前服务器时间"
            }
        }
     */
    public function groupGoodsDetail($puzzle_id)
    {
        if (empty($puzzle_id) || ! is_numeric($puzzle_id)) return $this->responseJson(201, '参数错误');

        // 团购商品详情
        $group_goods = PuzzleGoodsModel::where('id', $puzzle_id)
            ->select(['goods_id', 'sku_id', 'price', 'storage', 'top_member', 'begin_time', 'finish_time', 'code'])
            ->first();
        if (! $group_goods || $group_goods->code != 0) {
            return $this->responseJson(404, '该团购不存在或已下架');
        }

        $group_goods = $group_goods->toArray();
        // 销量
        $data = PuzzleGroupModel::where(['puzzle_id' => $puzzle_id])
            ->whereIn('status', [1, 2])
            ->select('id', 'member_num')
            ->get();
        $group_ids = $data->pluck('id')->toArray();
        $sale_total = PuzzleUserModel::whereIn('group_id', $group_ids)
            ->sum('buy_num');
        $group_goods['sale_total'] = $sale_total;
        // 状态和时间 1团购中 0未开始 2已结束
        $now = Carbon::now()->toDateTimeString();
        if ($now >= $group_goods['begin_time'] &&  $now <= $group_goods['finish_time']) {
            $status = 1;
        }
        elseif ($now < $group_goods['begin_time']) {
            $status = 0;
        }
        else {
            $status = 2;
        }

        // 已参团总人数
        $total_member = $data->sum('member_num');

        // 团队列表 2条
        $team_list = $this->service->groupTeamList($puzzle_id);

        return $this->responseJson(200, 'ok', compact('group_goods', 'total_member', 'team_list', 'status', 'now'));
    }

    /**
     * @api {post} /api/group/group_order 开团/拼团订单
     * @apiName group_order
     * @apiGroup goods
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} num 购买数量
     * @apiParam {number} puzzle_id 团购id，购团商品需要传递
     * @apiParam {number} open_join 开团还是参团：1开团 2参团
     * @apiParam {number} group_id 可选,参团需要，组团id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
                "order_sn": "订单号",
                "puzzle_id": "团购id",
                "open_join": "开团或参团",
                "group_id": "参团id"
            }
     *       "msg":"添加成功"
     *     }
     */
    public function groupOrderAdd()
    {
        // 登录判断
        $all=request()->all();
        $token=request()->header('token')??'';
        if (!empty($token)) {
            $all['token']=$token;
        }
        if (empty($all['uid'])||empty($all['token'])) {
            return $this->responseJson(202, '请登录');
        }
        $check=$this->checktoten($all['uid'],$all['token']);
        if ($check['code']==202) {
            return $this->responseJson(202, '请登录');
        }
        if (empty($all['num']) || ! is_numeric($all['num']) || $all['num'] < 1) {
            return $this->responseJson(201, '参数错误');
        }

        $alldata = [];
        $data = [];
        // 收货地址验证
        $address=Db::table('user_address')->where(['user_id'=>$all['uid'],'is_defualt'=>1])->first();
        if(empty($address)){
            return $this->responseJson(201,'请填写收货地址');
        }

        $uid = $all['uid'];
        $num = $all['num'];
        $puzzle_id = empty($all['puzzle_id']) ? 0 : $all['puzzle_id'];
        $open_join = empty($all['open_join']) ? 0 : $all['open_join'];
        $group_id = empty($all['group_id']) ? 0 : $all['group_id'];

        // 团购验证
        try {
            $puzzle_goods = $this->service->groupOrderCheck($puzzle_id, $uid, $num, $open_join, $group_id);
        }
        catch (\Exception $exception) {
            if ($exception->getCode() == 201) {
                return $this->responseJson(201, $exception->getMessage());
            }
            return $this->responseJson(500, '未知错误，请稍后再试');
        }

        $all['goods_id'] = $puzzle_goods->goods_id;
        $all['goods_sku_id'] = $puzzle_goods->sku_id;
        $all['merchant_id'] = $puzzle_goods->merchant_id;
        // 订单参数
        $alldata['address_id']=$address->id;
        $alldata['puzzle_id'] = $puzzle_id;
        $data['goods_id']=$all['goods_id'];
        $alldata['status']=10;
        $data['status']=10;
        $data['merchant_id']=$all['merchant_id'];
        $data['goods_sku_id']=$all['goods_sku_id'];
        $data['num']=$all['num'];
        $data['pay_discount']=1;
        $alldata['user_id']=$data['user_id']=$all['uid'];
        $alldata['order_sn']=$data['order_id']=$this->suiji();
        $alldata['created_at'] = $alldata['updated_at']=$data['created_at'] = $data['updated_at'] =date('Y-m-d H:i:s',time());
        $dilivery=Db::table('goods')->select('dilivery','weight')->where('id',$all['goods_id'])->first();
        if ($dilivery->dilivery > 0) {
            $alldata['shipping_free']=$data['shipping_free']=$this->freight($dilivery->weight*$all['num'],$all['num'],$dilivery->dilivery);
        }else{
            $alldata['shipping_free']=$data['shipping_free']=0;
        }
        $alldata['order_money']=$data['pay_money'] = $data['total'] = $all['num'] * $puzzle_goods->price + $data['shipping_free'];
        $alldata['type']=1;
        $alldata['remark']=$all['remark']??'';
        $alldata['auto_receipt']=$all['auto_receipt']??0;

        try {
            DB::beginTransaction();
            DB::table('order_goods')->insert($data);
            DB::table('orders')->insert($alldata);

            DB::commit();
        }
        catch (\Exception $exception) {
            DB::rollback();
            return $this->responseJson(201,'操作失败');
        }
        // TODO 针对一定时间内不支付的订单，看是否作废该订单

        return $this->responseJson(200,'下单成功',array('order_sn'=>$data['order_id'], 'puzzle_id' => $puzzle_id, 'open_join' => $open_join, 'group_id' => $group_id));
    }

    /**
     * @api {post} /api/group/group_buy_test 拼团操作测试接口(正式接口为支付接口)
     * @apiName group_buy_test
     * @apiGroup goods
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} sNo 订单号
     * @apiParam {number} puzzle_id 团购id
     * @apiParam {number} open_join 方式，开团还是参团：1开团 2参团
     * @apiParam {number} group_id 参团必填，参团id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"拼团成功"
     *     }
     */
    public function groupBuyTest()
    {
        $all = \request()->all();
        $token=request()->header('token')??'';
        if (!empty($token)) {
            $all['token']=$token;
        }
        if (empty($all['uid'])||empty($all['token'])) {
            return $this->responseJson(202, '请登录');
        }
        $check=$this->checktoten($all['uid'],$all['token']);
        if ($check['code']==202) {
            return $this->responseJson(202, '请登录');
        }

        $sNo=$all['sNo'];
        $orders = Db::table('orders')
            ->where(['order_sn'=>$sNo,'status'=>10,'user_id'=>$all['uid']])
            ->first();
        if (empty($orders)) {
            return $this->responseJson(201,'订单不存在');
        }

        // hcq新增：团购支付处理
        $puzzle_id = empty($all['puzzle_id']) ? 0 : $all['puzzle_id'];
        $open_join = empty($all['open_join']) ? 0 : $all['open_join'];
        $group_id = empty($all['group_id']) ? 0 : $all['group_id'];
        if ($puzzle_id) {
            try {
                $goods = DB::table('order_goods')->where('order_id',$sNo)->first();
                if (! $goods) {
                    return $this->responseJson(201, '没有该订单数据');
                }
                if ($orders->puzzle_id == 0 ||$orders->puzzle_id != $puzzle_id || $orders->is_del == 1) {
                    return $this->responseJson(201, '非法订单');
                }
                $num = $goods->num;
                $this->service->openOrJoinGroup($orders->id, $puzzle_id, $open_join, $num, $all['uid'], $group_id);
            }
            catch (\Exception $e) {
                // 删除订单
                //                 $service->deleteOrder($sNo);
                if ($e->getCode() == 201) {
                    return $this->responseJson(201, '拼团失败,' . $e->getMessage());
                }
                return $this->responseJson(500, '未知错误,拼团失败');
            }

            return $this->responseJson(200, '拼团成功');
        }
        else {
            return $this->responseJson(201, '缺少参数');
        }
    }

    /**
     * 定时任务接口，清理拼团失败的数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function scanFailedGroups()
    {
        try {
            $res = $this->service->scanPuzzleGroups();
        }
        catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return $this->responseJson(201, $exception->getMessage());
        }

        if (! $res) {
            \Log::info('暂时没有数据可以更新');
            return $this->responseJson(404, '暂时没有数据可以更新');
        }

        \Log::info('数据已更新完毕');
        return $this->responseJson(200, '数据已更新完毕');
    }
}
