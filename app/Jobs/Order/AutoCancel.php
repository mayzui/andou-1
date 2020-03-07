<?php

namespace App\Jobs\Order;

use App\Models\Goods;
use App\Models\OrderCancel;
use App\Models\OrderGoods;
use App\Models\Orders;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoCancel implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    private $order_sn;

    /**
     * Create a new job instance.
     *
     * @param array|string $order_sn
     *
     * @return void
     */
    public function __construct($order_sn = null) {
        $this->order_sn = $order_sn;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle() {
        if ($this->attempts() > 3) {
            $this->fail(new Exception('自动取消订单异常，订单号：' . $this->order_sn));
        } else {
            $order = Orders::getInstance()->where('order_sn', $this->order_sn)->first();
            if ($order) {
                if ($order->status == 10 && Carbon::now()->diffInSeconds($order->created_at) >= 1800) {
                    DB::beginTransaction();
                    $updatedAt = Carbon::now()->toDateTimeString();
                    // 更新状态
                    if ($order->update(['status' => 0, 'updated_at' => $updatedAt])) {
                        $orderGood = OrderGoods::getInstance()->where('order_id', $this->order_sn);

                        // 还原销量
                        $goodIds = $orderGood->pluck('goods_id');
                        foreach ($goodIds as $goodId) {
                            Goods::getInstance()->find($goodId)->decrement('volume');
                        }

                        $updateRet = $orderGood->update(['status' => 0, 'updated_at' => $updatedAt]);

                        // 写取消记录
                        if ($updateRet !== false && OrderCancel::getInstance()->insert([
                                'order_id' => $order->id,
                                'reason_id' => 4,
                                'reason' => '超时未支付自动取消'
                            ])) {
                            DB::commit();
                        } else {
                            DB::rollBack();
                            Log::error('自动取消订单失败，更新商品状态或写取消记录失败，订单号：' . $this->order_sn);
                        }
                    } else {
                        DB::rollBack();
                        Log::error('自动取消订单失败，更新状态失败，订单号：' . $this->order_sn);
                    }
                }
            } else {
                Log::error('自动取消订单失败，订单号不存在：' . $this->order_sn);
            }
        }

        $this->delete();
    }
}
