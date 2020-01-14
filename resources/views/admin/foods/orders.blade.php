@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>用户点餐订单表</h5>
            </div>
            <div class="ibox-content">
                <form method="post" action="{{route('foods.orders')}}" name="form">
                    {{ csrf_field() }}
                    <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                    <input type="text" style="height: 25px;margin-left: 10px;" value="{{ $name or '' }}" name="name" placeholder="电话/姓名/订单编号">
                    <button class="btn btn-primary btn-sm" type="submit"><i class="fa fa-search"></i> 查询</button>
                    <a href="{{url('/admin/foods/orders')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            全部</button>
                    </a>
                    <a href="{{url('/admin/foods/orders?status=10')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            未支付</button>
                    </a>
                    <a href="{{url('/admin/foods/orders?status=30')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            已到店</button>
                    </a>
                    <a href="{{url('/admin/foods/orders?status=20')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            未到店</button>
                    </a>
                    <a href="{{url('/admin/foods/orders?status=60')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            申请退款</button>
                    </a>
                    <a href="{{url('/admin/foods/orders?status=70')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            退款成功</button>
                    </a>
                </form>

                <style>
                    th ,td{
                        text-align: center;
                    }
                </style>
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th width="100">ID</th>
                        <th>订单编号</th>
                        <th>客户名称</th>
                        <th>客户电话</th>
                        <th>下单时间</th>
                        <th>用餐时间</th>
                        <th>用餐人数</th>
                        <th style="width: 200px;">备注</th>
                        <th>下单金额</th>
                        <th>订单状态</th>
                        <th>支付方式</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($data) > 0)
                        @foreach($data as $v)
                            <tr>
                                <th>{{ $v->id }}</th>
                                <th>{{ $v->order_sn }}</th>
                                <th>{{ $v->user_name }}</th>
                                <th>{{ $v->phone }}</th>
                                <th>{{ $v->orderingtime }}</th>
                                <th>{{ $v->dinnertime }}</th>
                                <th>{{ $v->people }}</th>
                                <th><p style="width: 200px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">{{ $v->remark }}</p></th>
                                <th>{{ $v->prices }}</th>
                                <th>
                                    @if($v->status == 0)
                                        <span style="color: red">已取消</span>
                                        @elseif($v->status == 10)
                                        <span style="color: red">未支付</span>
                                        @elseif($v->status == 20)
                                        <span style="color: green">已支付(未到店)</span>
                                        @elseif($v->status == 30)
                                        <span style="color: green">已到店</span>
                                        @elseif($v->status == 40)
                                        <span style="color: green">已完成</span>
                                        @elseif($v->status == 50)
                                        <span style="color: blue">已评价</span>
                                        @elseif($v->status == 60)
                                        <span style="color: blue">申请退款</span>
                                        @elseif($v->status == 70)
                                        <span style="color: green">退款成功</span>
                                        @elseif($v->status == 80)
                                        <span style="color: red">拒绝退款</span>
                                    @endif
                                </th>
                                <th style="color: blue;">
                                    @if($v->method == 1)
                                        微信
                                        @elseif($v->method == 2)
                                        支付宝
                                        @elseif($v->method == 3)
                                        银联
                                        @else
                                        余额
                                    @endif
                                </th>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{route('foods.orderschange')}}?id={{$v->id}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 详情</button></a>
                                    </div>
                                    @if($v->status == 60)
                                        <div class="btn-group">
                                            <a onclick="return_money({{$v->id}})"><button class="btn btn-success btn-xs" type="button"><i class="fa fa-check"></i> 同意</button></a>
                                            <a onclick="return_refuse({{$v->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-close"></i> 拒绝</button></a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th colspan="12">暂时还没有数据</th>
                        </tr>
                    @endif
                    </tbody>
                </table>
                {{ $data->appends(['status'=>$status]) }}
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <script type="text/javascript">
        function del(e) {
            var id = e;
            layer.alert("是否删除该数据？",{icon:3},function (index) {
                location.href="{{route('foods.ordersdel')}}?id="+id;
                layer.close(index);
            });
        }
        function return_money(e) {
            var id = e;
            layer.alert("是否同意退款？",{icon:3},function (index) {
                location.href="{{route('foods.return_money')}}?id="+id;
                layer.close(index);
            });
        }
        function return_refuse(e) {
            var id = e;
            layer.alert("是否拒绝退款？",{icon:3},function (index) {
                location.href="{{route('foods.return_refuse')}}?id="+id;
                layer.close(index);
            });
        }
    </script>

@endsection