@extends('admin.layouts.layout')
<style>
    th ,td{
        text-align: center;
        font-size: 13px;
    }
</style>

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>订单管理</h5>
            </div>
            <div class="ibox-content">
                <div class="col-sm-3" style="padding-left: 0px;">
                    <div class="input-group">
                        <input type="text" class="form-control" v-model="key" placeholder="输入需查询的关键字" />
                        <span class="input-group-btn">
                           <a type="button" class="btn btn-primary" @click="search"><i class="fa fa-search"></i> 搜索</a>
                    </span>&nbsp;&nbsp;&nbsp;
                        <span class="input-group-btn">
                           <a  href="{{url('/admin/shop/ordersAdd')}}" type="button" class="btn btn-primary"><i class="fa fa-plus-circle"></i>添加</a>
                    </span>

                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <form method="post" action="{{route('shop.index')}}" name="form">
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th width="100">ID</th>
                            <th>订单号</th>
                            <th>下单人</th>
                            <th>支付方式</th>
                            <th>支付金额</th>
                            <th>总计金额</th>
                            <th>邮费</th>
                            <th>订单备注</th>
                            <th>发票信息</th>
                            <th>支付时间</th>
                            <th>订单状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        @foreach($list as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->order_sn}}</td>
                                <td>{{$item->name}}</td>
                                <td>
                                    @if($item->pay_way == 0)
                                        微信支付
                                    @elseif($item->pay_way == 1)
                                        支付宝支付
                                    @elseif($item->pay_way == 2)
                                        平台余额支付
                                    @elseif($item->pay_way == 3)
                                        银联支付
                                    @endif
                                </td>
                                <td>{{$item->pay_money}}</td>
                                <td>{{$item->order_money}}</td>
                                <td>{{$item->shipping_free}}</td>
                                <td>{{$item->remark}}</td>
                                <td>
                                    @if($item->auto_receipt == 0)
                                        不开发票
                                    @elseif($item->auto_receipt == 1)
                                        自动发票
                                    @elseif($item->auto_receipt == 3)
                                        客户发票
                                    @endif
                                </td>
                                <td>{{$item->pay_time}}</td>
                                <td>
                                    @if($item->status == 0)
                                        <font color="880000">取消支付</font>
                                    @elseif($item->status == 10)
                                        <font color="red">未支付</font>
                                    @elseif($item->status == 20)
                                        <font color="#ff6600">已支付</font>
                                    @elseif($item->status == 40)
                                        <font color="#cc9900">已发货</font>
                                    @elseif($item->status == 50)
                                        <font color="#228b22">交易成功</font>
                                    @elseif($item->status == 60)
                                        <font color="#004400">交易关闭</font>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{url("/admin/shop/ordersUpd?id=$item->id")}}">
                                            <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button>
                                        </a>
                                        <a href="{{url("/admin/shop/ordersDel?id=$item->id")}}" onClick="delcfm()"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o" ></i> 删除</button></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tbody>
                    </table>

                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <script language="javascript">
        function delcfm() {
            if (!confirm("订单数据很重要确认要删除吗？")) {
                window.event.returnValue = false;
            }
        }

    </script>
@endsection