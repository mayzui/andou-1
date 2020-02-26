<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GoodsModel;
use App\Models\SecDetailModel;
use App\Models\SecRuleModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Log;

class GoodsController extends Controller {
    /**
     * @api {post} /api/goods/index 在线商城
     * @apiName index
     * @apiGroup goods
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *          "banner": [
     * {
     * "id": '轮播id',
     * "img": "图片地址",
     * "url": "跳转地址"
     * }
     * ],
     * "category": [
     * {
     * "id": '分类id',
     * "img": "图片地址",
     * "name": "分类名字"
     * }
     *
     * ],
     * "recommend_goods": [
     * {
     * "id": '推荐商品id',
     * "img": "图片地址",
     * "name": "商品名字",
     * "price": "价格"
     * }
     * ],
     * "bargain_goods": [
     * {
     * "id": '特价商品id',
     * "img": "图片地址",
     * "name": "名字",
     * "price": "价格"
     * }
     * ]
     *       },
     *       "msg":"查询成功"
     *     }
     */
    public function index() {
        $data['banner'] = DB::table('banner')
            ->select('id', 'img', 'url')
            ->where(['banner_position_id' => 6, 'status' => 1])
            ->orderBy('sort', 'ASC')
            ->get();
        $data['category'] = DB::table('goods_cate')
            ->select('id', 'img', 'name')
            ->where(['pid' => 0])
            ->orderBy('sort', 'ASC')
            ->limit(8)
            ->get();
        $data['recommend_goods'] = DB::table('goods')
            ->select('id', 'img', 'name', 'price')
            ->where(['is_recommend' => 1, 'is_sale' => 1, 'is_del' => 0])
            ->orderBy('created_at', 'DESC')
            ->limit(4)
            ->get();
        $data['bargain_goods'] = DB::table('goods')
            ->select('id', 'img', 'name', 'price')
            ->where(['is_bargain' => 1, 'is_sale' => 1, 'is_del' => 0])
            ->orderBy('created_at', 'DESC')
            ->limit(4)
            ->get();
        return $this->rejson(200, '查询成功', $data);
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
                "is_sec": "是否秒杀商品 1是 0否",
                "store_num": "库存"
                "merchant": {
                    "id":"商家id"
                    "name": "商家名字",
                    "logo_img": "商家头像"
                },
                "is_collection": "1为以收藏 0未收藏",
                "start_time": "秒杀开始时间",
                "end_time": "秒杀结束时间"
     *       },
     *       "msg":"查询成功"
     *     }
     */
    public function goods() {
        $all = request()->all();
        if (!isset($all['id'])) {
            return $this->rejson(201, '缺少参数');
        }
        $data = DB::table('goods')
            ->select('name', 'merchant_id', 'weight', 'img', 'album', 'price', 'dilivery', 'volume', 'is_sec')
            ->where('id', $all['id'])
            ->first();
        if ($data->album) {
            $data->album = json_decode($data->album, 1);
        } else {
            $data->album = '';
        }
        // echo $data->weight;exit();
        if ($data->dilivery > 0) {
            $data->dilivery = $this->freight($data->weight, 1, $data->dilivery);
        }
        if (isset($all['uid'])) {//添加浏览记录
            $pv = $this->seemerchant($all['uid'], $all['id'], 1);
            if ($pv) {
                $re = DB::table('goods')->where('id', $all['id'])->increment('pv');
            }
            $collection = DB::table('collection')
                ->select('id')
                ->where(['user_id' => $all['uid'], 'pid' => $all['id'], 'type' => 1])
                ->first();
            // var_dump($data);exit();
            if (empty($collection)) {
                $data->is_collection = 0;
            } else {
                $data->is_collection = 1;
            }
        } else {
            $data->is_collection = 0;
        }

        if (isset($data->merchant_id)) {
            $data->merchant = DB::table('merchants')->select('id', 'name', 'logo_img')->where('id', $data->merchant_id)->first();
        }
        $store_num = DB::table('goods_sku')
            ->where('goods_id', $all['id'])
            ->sum('store_num');
        if ($store_num) {
            $data->store_num = $store_num;
        } else {
            $data->store_num = 0;
        }

        // hcq新增：判断是否秒杀商品,是就返回秒杀开始结束时间
         if ($data->is_sec == 1) {
             $sec_rule = SecRuleModel::where('goods_id', $all['id'])
                 ->where('status', 1)
                 ->select('start_time', 'end_time')->first();
             if ($sec_rule) {
                 $data->start_time = $sec_rule->start_time;
                 $data->end_time = $sec_rule->end_time;
             }
             else {
                 $data->is_sec = 0;
             }
         }

        return $this->rejson(200, '查询成功', $data);
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
     * {
     * "name": "商品名字",
     * "img": "商品图片",
     * "price": "价格",
     * "id": "商品id"
     * }
     * ],
     *       "msg":"查询成功"
     *     }
     */
    public function goodList() {
        $all = request()->all();
        $num = 10;
        $where[] = ['g.is_sale', 1];
        $where[] = ['g.is_del', 0];
        if (isset($all['page'])) {
            $pages = ($all['page'] - 1) * $num;
        } else {
            $pages = 0;
        }
        if (!empty($all['is_recommend'])) {//推荐
            $where[] = ['g.is_recommend', 1];
        }
        if (!empty($all['is_bargain'])) {//特价
            $where[] = ['g.is_bargain', 1];
        }
        if (isset($all['cate_id'])) {
            $where[] = ['g.goods_cate_id', 'like', '%,' . $all['cate_id'] . ',%'];
        }
        if (isset($all['keyword'])) {
            $where[] = ['g.name', 'like', '%' . $all['keyword'] . '%'];
        }
        $orderBy = 'g.pv';
        $sort = 'DESC';
        if (isset($all['price_sort'])) {
            if ($all['price_sort'] == 1) {
                $orderBy = 'g.price';
            } else {
                $orderBy = 'g.price';
                $sort = 'ASC';
            }
        }
        if (isset($all['volume_sort'])) {
            if ($all['volume_sort'] == 1) {
                $orderBy = 'g.volume';
            } else {
                $orderBy = 'g.volume';
                $sort = 'ASC';
            }
        }
        if (isset($all['start_sort'])) {
            if ($all['start_sort'] == 1) {
                $orderBy = 'cnum';
            } else {
                $orderBy = 'cnum';
                $sort = 'ASC';
            }
        }
        $data = DB::table('goods as g')
            ->select('g.name', 'g.img', 'g.price', 'g.id', DB::raw("count(c.content) as cnum"))
            ->leftJoin('order_commnets as c', 'c.goods_id', '=', 'g.id')
            ->where($where)
            ->orderBy($orderBy, $sort)
            ->offset($pages)
            ->limit($num)
            ->groupBy('g.id')
            ->get();
        return $this->rejson('200', '查询成功', $data);

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
    public function details() {
        $all = request()->all();
        if (!isset($all['id'])) {
            return $this->rejson(201, '缺少参数');
        }
        $desc = DB::table('goods')
            ->select('desc')
            ->where('id', $all['id'])
            ->first();
        if (isset($desc->desc)) {
            $data['details'] = $desc->desc;
        } else {
            $data['details'] = '';
        }
        return $this->rejson(200, '查询成功', $data);
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
     * {
     * "avator": "用户头像",
     * "name": "用户名字",
     * "id": "评论id",
     * "stars": "评论星级",
     * "content": "评论内容",
     * "created_at": "评论时间"
     * }
     * ],
     *      "msg":"查询成功"
     *     }
     */
    public function comment() {
        $all = request()->all();
        $num = 10;
        if (isset($all['page'])) {
            $pages = ($all['page'] - 1) * $num;
        } else {
            $pages = 0;
        }
        if (!isset($all['id'])) {
            return $this->rejson(201, '缺少参数');
        }
        $data = DB::table('order_commnets as c')
            ->join("users as u", "c.user_id", "=", "u.id")
            ->select('u.avator', 'u.name', 'c.id', 'c.stars', 'c.content', 'c.created_at')
            ->where(['c.goods_id' => $all['id'], 'c.status' => 1, 'c.is_del' => 0,])
            ->orderBy('created_at', 'DESC')
            ->offset($pages)
            ->limit($num)
            ->get();
        return $this->rejson(200, '查询成功', $data);
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
    public function specslist() {
        $all = request()->all();
        if (!isset($all['id'])) {
            return $this->rejson(201, '缺少参数');
        }
        $data = DB::table('goods_sku')->where(['goods_id' => $all['id'], 'is_valid' => 1])->get();

        $datas = json_decode($data[0]->attr_value, 1)[0]['name'];
        foreach ($datas as $k => $v) {
            $res[$k]['name'] = $v;
            $res[$k]['value'] = [];
            foreach ($data as $key => $value) {
                $re = json_decode($value->attr_value, 1)[0]['value'];
                if (!in_array($re[$k], $res[$k]['value'])) {
                    $res[$k]['value'][] = $re[$k];
                }
            }
        }
        foreach ($data as $key => $value) {
            $re = json_decode($value->attr_value, 1)[0]['value'];
            $re = implode('-', $re);
            $arr = ['id' => $value->id, 'price' => $value->price, 'num' => $value->store_num];
            $specs[$re] = $arr;
        }
        $goodspecs['price'] = $specs;
        $goodspecs['res'] = $res;
        return $this->rejson(200, '查询成功', $goodspecs);
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
    public function collection() {
        $all = request()->all();
        $token = request()->header('token') ?? '';
        if ($token != '') {
            $all['token'] = $token;
        }
        if (empty($all['uid']) || empty($all['token'])) {
            return $this->rejson(202, '登陆失效');
        }
        $check = $this->checktoten($all['uid'], $all['token']);
        if ($check['code'] == 202) {
            return $this->rejson($check['code'], $check['msg']);
        }
        if (!isset($all['id']) || !isset($all['type'])) {
            return $this->rejson(201, '缺少参数');
        }
        if ($all['type'] == 1) {
            $data['type'] = 1;
            $data['user_id'] = $all['uid'];
            $data['pid'] = $all['id'];
            $data['created_at'] = date('Y-m-d H:i:s', time());
            $res = DB::table('collection')
                ->where(['user_id' => $all['uid'], 'pid' => $all['id'], 'type' => 1])
                ->first();
            if (!empty($res)) {
                return $this->rejson(201, '商品已收藏');
            }
            DB::table('collection')->insert($data);
            return $this->rejson(200, '收藏成功');
        } else {
            DB::table('collection')
                ->where(['user_id' => $all['uid'], 'pid' => $all['id'], 'type' => 1])
                ->delete();
            return $this->rejson(200, '取消收藏成功');
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
    public function follow() {
        $all = request()->all();
        $token = request()->header('token') ?? '';
        if ($token != '') {
            $all['token'] = $token;
        }
        if (empty($all['uid']) || empty($all['token'])) {
            return $this->rejson(202, '登陆失效');
        }
        $check = $this->checktoten($all['uid'], $all['token']);
        if ($check['code'] == 202) {
            return $this->rejson($check['code'], $check['msg']);
        }
        if (!isset($all['id']) || !isset($all['type'])) {
            return $this->rejson(201, '缺少参数');
        }
        if ($all['type'] == 1) {
            $data['type'] = 3;
            $data['user_id'] = $all['uid'];
            $data['pid'] = $all['id'];
            $data['created_at'] = date('Y-m-d H:i:s', time());
            $res = DB::table('collection')
                ->where(['user_id' => $all['uid'], 'pid' => $all['id'], 'type' => 3])
                ->first();
            if (!empty($res)) {
                return $this->rejson(201, '商品已关注');
            }
            DB::table('collection')->insert($data);
            return $this->rejson(200, '关注成功');
        } else {
            DB::table('collection')
                ->where(['user_id' => $all['uid'], 'pid' => $all['id'], 'type' => 3])
                ->delete();
            return $this->rejson(200, '取消关注成功');
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
     * "id": "一级分类id",
     * "name": "一级分类名字",
     * "towcate": [
     * {
     * "id": "二级分类id",
     * "name": "二级分类名字",
     * "img": "分类图片"
     * }
     * ]
     * }
     *       ],
     *       "msg":"查询成功"
     *     }
     */
    public function goodsCate() {
        $data = DB::table('goods_cate')
            ->select('id', 'name', 'img')
            ->where('pid', 0)
            ->get();
        foreach ($data as $key => $value) {
            $data[$key]->towcate = DB::table('goods_cate')
                ->select('id', 'name', 'img')
                ->where('pid', $value->id)
                ->get();
        }
        return $this->rejson(200, '查询成功', $data);
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
     * "id": "一级分类id",
     * "name": "一级分类名字",
     * "img": "分类图片"
     * }
     *       ],
     *       "msg":"查询成功"
     *     }
     */
    public function cate() {
        $all = request()->all();
        if (empty($all['id'])) {
            $pid = 0;
        } else {
            $pid = $all['id'];
        }
        $data = DB::table('goods_cate')
            ->select('id', 'name', 'img')
            ->where('pid', $pid)
            ->get();
        return $this->rejson(200, '查询成功', $data);
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
     * "id": "关键词id",
     * "name": "搜索关键词"
     * }
     *       ],
     *       "msg":"查询成功"
     *     }
     */
    public function hotsearch() {
        $data = DB::table('hotsearch')
            ->select('id', 'name')
            ->where('status', 1)
            ->get();
        return $this->rejson(200, '查询成功', $data);
    }

    /**
     * @api {post} /api/goods/sec_list 秒杀商品列表
     * @apiName sec_list
     * @apiGroup goods
     * @apiParam {Number} sec_hour 秒杀小时 0-23,不传默认当前小时
     * @apiParam {Number} page 页数 不传默认第一页
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *          "sec_status": "秒杀状态 1进行中 0未开始 2已结束",
                "top_goods": [
                    {
                        "sec_id": "秒杀id",
                        "goods_id": "商品id",
                        "sku_id": "规格id",
                        "name": "商品名称",
                        "img": "图片",
                        "kill_price": "秒杀价"
                    },
                    {
                        "sec_id": 2,
                        "goods_id": 43,
                        "sku_id": 108,
                        "name": "【新年礼物】纪梵希节日限量 禁忌之吻霓虹唇膏口红N28 N27 散粉",
                        "img": "andou.test/uploads/05cafd118675c550b5e0202a64dab5b6.jpg",
                        "kill_price": "166.00"
                    }
                ],
                "goods_list": [
                    {
                        "sec_id": "秒杀id",
                        "goods_id": "商品id",
                        "sku_id": "规格id",
                        "name": "博洋家纺（BEYOND）床品套件 纯棉四件套北欧风全棉斜纹床单被套双人床1.8m床上用品 西莉雅220*240cm",
                        "img": "andou.test/uploads/a1430211d841e00f986d96bf1e50c113.jpg",
                        "price": "原价",
                        "kill_price": "秒杀价",
                        "num": "总数",
                        "kill_num": "已秒数",
                        "start_time": "秒杀开始时间",
                        "end_time": "秒杀结束时间",
                        "kill_percent": "已秒百分比",
                        "is_finish": "是否秒完 0否 1是",
                        "in_sec": "秒杀状态 1进行中 0未开始 2已结束",
                        "has_sec": "是否秒已经秒杀过 0否 1是"
                    },
                    {
                        "sec_id": 4,
                        "name": "【情人节限量】纪梵希红丝绒n37口红套装 散粉 心无禁忌香水正品",
                        "img": "andou.test/uploads/99f2f434793a671a62a274a6ecfeb8ab.jpg",
                        "price": "888.00",
                        "kill_price": "800.00",
                        "num": 134,
                        "kill_num": 50,
                        "start_time": "2020-02-14 18:00:00",
                        "end_time": "2020-02-14 19:00:00",
                        "kill_percent": 39,
                        "is_finish": 0,
                        "in_sec": 0,
                        "has_sec": 0
                    }
                ]
            },
     *       "msg":"ok"
     *     }
     */
    public function secKillList(Request $request) {
        // 登录判断
        $token = $request->header('token', '');
        $uid = $request->input('uid', '');
        if (!$token || !$uid) {
            $is_login = 0;
        } else {
            $check = $this->checktoten($uid, $token);
            if (isset($check['code']) && $check['code'] == 202) {
                $is_login = 1;
            } else {
                $is_login = 0;
            }
        }

        $page = $request->input('page', 1);
        $offset = ($page - 1) * 10;

        [$start_time, $end_time] = $this->secTimeGet();
        $list_where['g.is_sale'] = 1;
        $list_where['g.is_del'] = 0;
        $list_where['g.is_sec'] = 1;
        $list_where['sr.status'] = 1;
        $list_where['sr.is_top'] = 1;
        $list_where[] = ['sr.start_time', '>=', $start_time];
        $list_where[] = ['sr.end_time', '<=', $end_time];

        // 必抢好货
        $top_goods = GoodsModel::from('goods as g')
                ->join('seckill_rules as sr', 'g.id', '=', 'sr.goods_id')
                ->where($list_where)
                ->select(['sr.id as sec_id', 'sr.goods_id', 'sr.sku_id', 'g.name', 'g.img', 'sr.kill_price'])
                ->limit(3)
                ->get()
                ->toArray();

        // 秒杀列表
        $list_where['sr.is_top'] = 0;
        $goods_list = GoodsModel::from('goods as g')
                    ->join('seckill_rules as sr', 'g.id', '=', 'sr.goods_id')
                    ->join('goods_sku as gs', 'sr.sku_id', '=', 'gs.id')
                    ->where($list_where)
                    ->select(['sr.id as sec_id', 'sr.goods_id', 'sr.sku_id', 'g.name', 'g.img', 'gs.price', 'sr.kill_price', 'sr.num', 'sr.kill_num', 'sr.start_time', 'sr.end_time'])
                    ->offset($offset)
                    ->limit(10)
                    ->get()
                    ->toArray();

        $now_hour = Carbon::now()->hour;
        $sec_hour = $request->input('sec_hour', $now_hour);

        // 秒杀状态 1进行中 0未开始 2已结束
        if ($now_hour == $sec_hour) {
            $sec_status = 1;
        } else if ($now_hour < $sec_hour) {
            $sec_status = 0;
        } else {
            $sec_status = 2;
        }

        foreach ($goods_list as &$value) {
            $per = ($value['kill_num'] / $value['num']) * 100;
            $value['kill_percent'] = round($per);
            $value['is_finish'] = $value['kill_num'] >= $value['num'] ? 1 : 0;
            // 秒杀状态 1进行中 0未开始 2已结束
            $value['in_sec'] = $sec_status;

            // 是否秒杀过
            if ($is_login) {
                $has = SecDetailModel::where([
                    ['goods_id', $value['id']], ['sku_id', $value['sku_id']], ['user_id', $uid]
                ])->whereBetween('sec_time', [$value['start_time'], $value['end_time']])->first();
                $value['has_sec'] = $has ? 1 : 0;
            } else {
                $value['has_sec'] = 0;
            }
        }

        $data = compact('sec_status', 'top_goods', 'goods_list');

        return response()->json([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    /**
     * @api {post} /api/goods/sec_goods 秒杀操作
     * @apiName sec_goods
     * @apiGroup goods
     * @apiParam {Number} uid 用户id
     * @apiParam {string} token 用户验证
     * @apiParam {Number} sec_id 秒杀id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *          "order_sn": "订单号"
     *       }
     *       "msg":"秒杀成功"
     *     }
     */
    public function secGoods(Request $request) {
        // 登录判断
        $token = $request->header('token', '');
        $uid = $request->input('uid', '');
        if (!$token || !$uid) {
            return $this->rejson(202, '请登录');
        }
        $check = $this->checktoten($uid, $token);
        if (isset($check['code']) && $check['code'] == 202) {
            return $this->rejson(202, '请登录');
        }

        // 请求参数
        $sec_id = $request->input('sec_id', '');
        if (! $sec_id || ! is_numeric($sec_id)) return $this->rejson(201, '参数错误');

        // 秒杀验证
        $sec_rule = SecRuleModel::find($sec_id);
        if (! $sec_rule || $sec_rule->status != 1) {
            return $this->rejson(201, '秒杀商品不存在或已下架');
        }
        list($start_time, $end_time) = $this->secTimeGet(1);
        if ($start_time < $sec_rule->start_time || $end_time > $sec_rule->end_time) {
            return $this->rejson(201, '当前不在秒杀时间段');
        }
        if ($sec_rule->kill_num >= $sec_rule->num) {
            return $this->rejson(201, '商品已秒杀完');
        }

        // 商品状态验证
        $goods = GoodsModel::find($sec_rule->goods_id);
        if (! $goods || $goods->is_sale != 1 || $goods->is_del == 1) return $this->rejson(201, '商品不存在或已下架');
        if ($goods->is_sec != 1) return $this->rejson(201, '该商品未参与秒杀');

        // 是否秒杀验证
        $has = SecDetailModel::where([
            ['goods_id', $sec_rule->goods_id], ['sku_id', $sec_rule->sku_id], ['user_id', $uid]
        ])->whereBetween('sec_time', [$sec_rule->start_time, $sec_rule->end_time])->first();
        if ($has) {
            return $this->rejson(201, '你已经秒杀过该商品了');
        }

        // 秒杀
        try {
            DB::beginTransaction();

            $address = Db::table('user_address')->where(['user_id'=>$uid,'is_defualt'=>1])->first();
            if (! $address) {
                throw new \Exception('请填写收货地址', 205);
            }

            // 增加秒中数
            $where['id'] = $sec_id;
            $where['kill_num'] = $sec_rule->kill_num;
            SecRuleModel::where($where)->increment('kill_num');

            // 生成秒杀订单
            $ret = $this->createSecOrder($sec_id, $sec_rule->goods_id, $sec_rule->sku_id, $sec_rule->merchantsid, $address->id, $uid, $sec_rule->kill_price);
            $order_sn = $ret['order_sn'];
            $order_id = $ret['order_id'];

            // 秒中记录
            $now = Carbon::now();
            SecDetailModel::create([
                'goods_id' => $sec_rule->goods_id,
                'sku_id' => $sec_rule->sku_id,
                'user_id' => $uid,
                'sec_price' => $sec_rule->kill_price,
                'sec_time' => $now,
                'order_id' => $order_id
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e->getCode() == 205) {
                return $this->rejson(205, $e->getMessage());
            }
            Log::error($e->getMessage());

            return $this->rejson(206, '秒杀失败，请稍后再试');
        }
        
        return $this->rejson(200, '秒杀成功', ['order_sn' => $order_sn]);
    }

    private function secTimeGet($is_sec = 0) {
        // 当前时间参数
        $carbon = Carbon::now();
        $now_year = $carbon->year;
        $now_month = $carbon->month;
        $now_day = $carbon->day;
        $now_hour = $carbon->hour;
        $sec_hour = $is_sec == 0 ? request()->input('sec_hour', $now_hour) : $now_hour; // 秒杀小时时间点: 0 1 2 3...23
        // 获取当前小时开始和下一个小时开始
        $sec_carbon = Carbon::create($now_year, $now_month, $now_day, $sec_hour);
        $start_time = $sec_carbon->toDateTimeString();
        $end_time = $sec_carbon->addHour()->toDateTimeString();

        return [$start_time, $end_time];
    }
    /**
     * @api {post} /api/goods/sec_details 秒杀详情
     * @apiName sec_details
     * @apiGroup goods
     * @apiParam {Number} sec_id 秒杀id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"秒杀成功",
     *           "data": {
                    "goods_id": "商品id",
                    "sku_id": "规格id",
                    "kill_price": "秒杀价",
                    "kill_num": "秒杀数",
                    "storage": "秒杀库存",
                    "start_time": "2020-02-20 14:00:00",
                    "end_time": "2020-02-20 15:00:00",
                    "sec_status": "秒杀状态 1进行中 0未开始 2已结束",
                    "now": "服务器当前时间"
                }
     *     }
     */
    public function getSecRule(Request $request)
    {
        // hcq新增：获取一个秒杀明细
        $sec_id = $request->input('sec_id', '');
        if (empty($sec_id || ! is_numeric($sec_id))) {
            return $this->responseJson(201, '参数错误');
        }
        $sec_rule = SecRuleModel::find($sec_id);
        if (! $sec_rule) {
            return $this->responseJson(404, '没有该秒杀相关信息');
        }
        if ($sec_rule->status != 1) {
            return $this->responseJson(404, '该秒杀商品已下架');
        }

        $now = Carbon::now()->toDateTimeString();
        // 秒杀状态 1进行中 0未开始 2已结束
        if ($now >= $sec_rule->start_time && $now <= $sec_rule->end_time) {
            $sec_status = 1;
        }
        elseif ($now < $sec_rule->start_time) {
            $sec_status = 0;
        }
        else {
            $sec_status = 2;
        }

        $data['goods_id'] = $sec_rule->goods_id;
        $data['sku_id'] = $sec_rule->sku_id;
        $data['kill_price'] = $sec_rule->kill_price;
        $data['kill_num'] = $sec_rule->kill_num;
        $data['storage'] = $sec_rule->num - $sec_rule->kill_num;
        $data['start_time'] = $sec_rule->start_time;
        $data['end_time'] = $sec_rule->end_time;
        $data['sec_status'] = $sec_status;
        $data['now'] = $now;

        return $this->responseJson(200, 'ok', $data);
    }

    private function createSecOrder($sec_id, $goods_id, $sku_id, $merchantsid, $address_id, $uid, $kill_price)
    {
        $order_data = $goods_data = [];

        $order_data['sec_id'] = $sec_id;
        $order_data['address_id'] = $address_id;
        $order_data['status'] = $goods_data['status'] = 10;
        $order_data['user_id'] = $goods_data['user_id'] = $uid;
        $order_data['order_sn'] = $goods_data['order_id'] = $this->suiji();
        $order_data['created_at'] = $order_data['updated_at']=$goods_data['created_at'] = $goods_data['updated_at'] =date('Y-m-d H:i:s',time());
        $goods_data['goods_id']=$goods_id;
        $goods_data['merchant_id']=$merchantsid;
        $goods_data['goods_sku_id']=$sku_id;
        $goods_data['num']=1;
        $goods_data['pay_discount']=1;

        $dilivery=Db::table('goods')->select('dilivery','weight')->where('id',$goods_id)->first();
        if ($dilivery->dilivery > 0) {
            $order_data['shipping_free']=$goods_data['shipping_free']=$this->freight($dilivery->weight*1,1,$dilivery->dilivery);
        }else{
            $order_data['shipping_free']=$goods_data['shipping_free']=0;
        }

        $order_data['order_money']=$goods_data['pay_money']=$goods_data['total']=$kill_price+$order_data['shipping_free'];
        $order_data['type']=1;
        $order_data['remark']='';
        $order_data['auto_receipt']=0;

        $re=DB::table('order_goods')->insert($goods_data);
        $res=DB::table('orders')->insertGetId($order_data);

        if (! $re || ! $res) {
            throw new \Exception('下单失败', 205);
        }

        return ['order_id' => $res, 'order_sn' => $order_data['order_sn']];
    }
}
