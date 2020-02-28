<?php
/**
 * Created by henry.
 * User: byhenry
 * Date: 2020/2/17
 * Time: 22:30
 */

namespace App\Services;


use App\Models\GoodsModel;
use App\Models\PuzzleGoodsModel;
use App\Models\PuzzleGroupModel;
use App\Models\PuzzleUserModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GroupService
{
    protected $puzzleGoodsModel;

    public function __construct()
    {
        $this->puzzleGoodsModel = new PuzzleGoodsModel();
    }

    public function topGoodsByType($type)
    {
        // type 1今日必抢 2热门推荐
        $now = Carbon::now()->toDateTimeString();
        $where['pg.goods_type'] = $type;
        $where[] = ['pg.begin_time', '<=', $now];
        $where[] = ['pg.finish_time', '>=', $now];
        $where['pg.code'] = 0;
        $where['go.is_sale'] = 1;
        $where['go.is_del'] = 0;

        $list = $this->puzzleGoodsModel->from('puzzle_goods as pg')
            ->join('goods as go', 'pg.goods_id', '=', 'go.id')
            ->where($where)
            ->select(['pg.id', 'pg.goods_id', 'pg.sku_id', 'pg.top_member', 'pg.price', 'go.name', 'go.img'])
            ->limit(3)
            ->get()->toArray();
        if (! empty($list)) $list = $this->transformTopList($list);

        return $list;
    }

    public function normalGoodsByCate($cate_id, $page)
    {
        $now = Carbon::now()->toDateTimeString();
        $where['pg.goods_type'] = 0;
        $where['pg.cate_id'] = $cate_id;
        $where[] = ['pg.begin_time', '<=', $now];
        $where[] = ['pg.finish_time', '>=', $now];
        $where['pg.code'] = 0;
        $where['go.is_sale'] = 1;
        $where['go.is_del'] = 0;

        $offset = ($page - 1) * 10;

        $list = $this->puzzleGoodsModel->from('puzzle_goods as pg')
            ->join('goods as go', 'pg.goods_id', '=', 'go.id')
            ->join('goods_sku as gs', 'pg.sku_id', '=', 'gs.id')
            ->where($where)
            ->select(['pg.id', 'pg.goods_id', 'pg.sku_id', 'pg.price','pg.storage', 'go.name', 'go.img', 'gs.price as old_price'])
            ->offset($offset)
            ->limit(10)
            ->get()->toArray();
        if (! empty($list)) $list = $this->transformGoodsList($list);
        
        return $list;
    }

    public function groupTeamList($puzzle_id, $limit = 2)
    {
        $pro_host = request()->getScheme() . '://' . request()->getHost();

        $list = PuzzleGroupModel::from('puzzle_groups as pg')
            ->join('users as u', 'pg.captain_id', '=', 'u.id')
            ->select(['pg.id as group_id', DB::raw('pg.total_member-pg.member_num as left_member'), DB::raw('concat("'.$pro_host.'",u.avator) as captain_avatar')])
            ->where(['pg.puzzle_id' => $puzzle_id, 'pg.status' => 1])
            ->limit($limit)
            ->orderBy('pg.id', 'desc')
            ->get()->toArray();

        return $list;
    }

    protected function transformGoodsList(array $list)
    {
        $pro_host = request()->getScheme() . '://' . request()->getHost();

        foreach ($list as &$item) {
            $item['img'] = $pro_host . $item['img'];

            $data = PuzzleGroupModel::where(['puzzle_id' => $item['id']])
                ->whereIn('status', [1, 2])
                ->select('id', 'member_num')
                ->get();
            $item['total_member'] = $data->sum('member_num'); // 已参团人数
            $group_ids = $data->pluck('id')->toArray();

            $buy_total = PuzzleUserModel::whereIn('group_id', $group_ids)
                ->sum('buy_num'); // 总的已购数量
            $item['buy_total'] = $buy_total;
            $item['sale_percent'] = round(($buy_total / $item['storage']) * 100);
        }

        return $list;
    }

    private function transformTopList(array $list)
    {
        $pro_host = request()->getScheme() . '://' . request()->getHost();
        foreach ($list as &$item) {
            $item['img'] = $pro_host . $item['img'];
            $item['total_member'] =PuzzleGroupModel::where(['puzzle_id' => $item['id']])
                ->whereIn('status', [1, 2])
                ->sum('member_num');
        }

        return $list;
    }

    //------------------------------------------团购操作和订单------------------------------------------

    public function groupOrderCheck($puzzle_id, $uid, $num, $open_join, $group_id)
    {
        // 基本参数判断
        if (empty($puzzle_id) || ! is_numeric($puzzle_id) || $puzzle_id < 1) {
            throw new \Exception("参数错误", 201);
        }
        $puzzle_goods = PuzzleGoodsModel::find($puzzle_id);
        if (! $puzzle_goods || $puzzle_goods->code != 0) {
            throw new \Exception("团购未开启", 201);
        }
        $now = date('Y-m-d H:i:s');
        if ($now < $puzzle_goods->begin_time || $now > $puzzle_goods->finish_time) {
            throw new \Exception("当前不在团购时间内", 201);
        }
        if ($puzzle_goods->storage < $num) {
            throw new \Exception("库存不足", 201);
        }
        if ($num > $puzzle_goods->single_limit) {
            throw new \Exception("购买数量超过限购数量", 201);
        }

        // 开团参团判断
        if (empty($open_join)) {
            throw new \Exception("参数错误", 201);
        }

        // 开团/拼团，针对同一个商品不能再次开团或拼团
        if ($open_join == 1) {
//            $has = PuzzleGroupModel::where(['captain_id' => $uid, 'status' => 1])->first();
//            if ($has) throw new \Exception("您已经开过团了", 201);
        }
        elseif ($open_join == 2) {
            // 参团
            if (empty($group_id) || ! is_numeric($group_id)) {
                throw new \Exception("参数错误", 201);
            }
            $group = PuzzleGroupModel::find($group_id);
            if (! $group || $group->status != 1) {
                throw new \Exception("该组团已结束或删除", 201);
            }
            if ($group->puzzle_id != $puzzle_id) {
                throw new \Exception('团购商品和团队不匹配', 201);
            }
            if ($group->member_num >= $group->total_member) {
                throw new \Exception("该团人数已满", 201);
            }
            $group_users = PuzzleUserModel::where('group_id', $group_id)->pluck('user_id')->toArray();
            if (in_array($uid, $group_users)) {
                throw new \Exception("你已经参与了该团组", 201);
            }
        }
        else {
            throw new \Exception("参数错误", 201);
        }

        return $puzzle_goods;
    }

    /**
     * 参团或组团检测入库，支付时调用
     *
     * @param [type] $order_id
     * @param [type] $puzzle_id 团购id,客户端传递
     * @param [type] $open_join 组团参团。客户端传递
     * @param [type] $num 购买数量，客户端传递
     * @param [type] $uid 客户端传递
     * @param integer $group_id 参团id,客户端传递
     * @return void
     */
    public function openOrJoinGroup($order_id, $puzzle_id, $open_join, $num, $uid, $group_id = 0)
    {
        $puzzle_goods = $this->groupOrderCheck($puzzle_id, $uid, $num, $open_join, $group_id);

        $goods = GoodsModel::find($puzzle_goods->goods_id);
        if (! $goods || $goods->is_sale != 1 || $goods->is_del == 1 || empty($goods->merchant_id)) {
            throw new \Exception('商品不存在或已下架', 201);
        }

        // 开团/拼团，针对同一个商品不能再次开团或拼团
        $this->checkHasPartInGoods($puzzle_id, $uid);

        if ($open_join == 1) {
            // 开团
            try {
                DB::beginTransaction();

                $group_data['puzzle_id'] = $puzzle_id;
                $group_data['group_code'] = $this->createGroupCode();
                $group_data['captain_id'] = $uid;
                $group_data['total_member'] = $puzzle_goods->top_member;
                $group_data['member_num'] = 1;
                $group_data['status'] = 1;
                $group = PuzzleGroupModel::create($group_data);

                $user_data['group_id'] = $group->id;
                $user_data['user_id'] = $uid;
                $user_data['order_id'] = $order_id;
                $user_data['goods_id'] = $puzzle_goods->goods_id;
                $user_data['sku_id'] = $puzzle_goods->sku_id;
                $user_data['group_price'] = $puzzle_goods->price;
                $user_data['buy_num'] = $num;
                $user_data['part_time'] = date('Y-m-d H:i:s');
                PuzzleUserModel::create($user_data);

                $res = PuzzleGoodsModel::where(['id' => $puzzle_id, 'storage' => $puzzle_goods->storage])
                    ->decrement('storage', $num);
                if (!$res) {
                    throw new \Exception('库存不足', 201);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
        else {
            // 参团
            try {
                DB::beginTransaction();

                $order_exist = PuzzleUserModel::where('order_id', $order_id)->first();
                if ($order_exist) {
                    throw new \Exception('订单已参与拼团', 201);
                }

                $puzzle_group = PuzzleGroupModel::find($group_id);
                $puzzle_group->member_num += 1;
                if ($puzzle_group->member_num > $puzzle_group->total_member) {
                    throw new \Exception('该团人数已满', 201);
                }
                if ($puzzle_group->member_num == $puzzle_group->total_member) {
                    $puzzle_group->status = 2;
                }
                $puzzle_group->save();

                $user_data['group_id'] = $group_id;
                $user_data['user_id'] = $uid;
                $user_data['order_id'] = $order_id;
                $user_data['goods_id'] = $puzzle_goods->goods_id;
                $user_data['sku_id'] = $puzzle_goods->sku_id;
                $user_data['group_price'] = $puzzle_goods->price;
                $user_data['buy_num'] = $num;
                $user_data['part_time'] = date('Y-m-d H:i:s');
                PuzzleUserModel::create($user_data);

                $res = PuzzleGoodsModel::where(['id' => $puzzle_id, 'storage' => $puzzle_goods->storage])
                    ->decrement('storage', $num);
                if (!$res) {
                    throw new \Exception('库存不足', 201);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }

        return true;
    }

    public function createGroupCode()
    {
        return date('YmdHi') . mt_rand(10000, 99999);
    }

    public function deleteOrder($order_sn)
    {
        DB::table('orders')->where('order_sn', $order_sn)->update(['is_del' => 1]);
        DB::table('order_goods')->where('order_id', $order_sn)->update(['is_del' => 1]);
    }

    /**
     * 扫描过期未拼团成功的商品下架，并修改拼团状态为失败（这里到时间清理，会自动下架团购，应该不存在返库存的问题了）
     * @return bool
     * @throws \Exception
     */
    public function scanPuzzleGroups()
    {
        $now = Carbon::now()->toDateTimeString();

        $finished_puzzles = $this->puzzleGoodsModel
            ->where('code', 0)
            ->where('finish_time', '<', $now)
            ->pluck('id')
            ->toArray();
        if (! empty($finished_puzzles)) {
            try {
                DB::beginTransaction();
                PuzzleGroupModel::whereIn('puzzle_id', $finished_puzzles)
                    ->where('status', 1)
                    ->whereColumn('member_num', '<', 'total_member')
                    ->update(['status' => 3]);

                $this->puzzleGoodsModel
                    ->where('code', 0)
                    ->where('finish_time', '<', $now)
                    ->update(['code' => 1]);

                DB::commit();
            }
            catch (\Exception $exception) {
                DB::rollBack();
                throw $exception;
            }

            return true;
        }

        return false;
    }

    /**
     * 检测当前用户是否参加过当前商品的团购，参加过就不能再次拼团
     * @param $puzzle_id
     * @param $uid
     * @return bool
     * @throws \Exception
     */
    private function checkHasPartInGoods($puzzle_id, $uid)
    {
        $group_ids = PuzzleGroupModel::where('puzzle_id', $puzzle_id)
            ->where('status', 1)
            ->pluck('id')->toArray();

        $has_me = PuzzleUserModel::whereIn('group_id', $group_ids)
            ->where('user_id', $uid)
            ->first();
        if ($has_me) {
            throw new \Exception('你已经参加过该商品的团购', 201);
        }

        return true;
    }
}
