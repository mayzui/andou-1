@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>菜品购物车</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                @if($order->status == 60)
                    <div class="btn-group">
                        <a onclick="return_money({{$order->id}})"><button class="btn btn-success btn-sm" type="button"><i class="fa fa-check"></i> 同意</button></a>
                        <a onclick="return_refuse({{$order->id}})"><button class="btn btn-danger btn-sm" type="button"><i class="fa fa-close"></i> 拒绝</button></a>
                    </div>
                @endif

                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{route('foods.orderschange')}}" method="post" accept-charset="UTF-8">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{ $order -> id or '' }}" />
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <h3>客户信息</h3>
                        <thead>
                        <tr>
                            <th width="200">客户名称</th>
                            <th width="200">客户电话</th>
                            <th width="200">下单总金额</th>
                            <th width="200">支付方式</th>
                        </tr>
                        @if(!empty($order))
                        </thead>
                        <tr>
                            <td>{{$order -> user_name}}</td>
                            <td>{{$order -> phone}}</td>
                            <td>{{$order->prices}}</td>
                            <td>
                                @if($order->method == 0)
                                    未支付
                                    @elseif($order->method == 1)
                                    微信
                                    @elseif($order->method == 2)
                                    支付宝
                                    @elseif($order->method == 3)
                                    银联
                                    @elseif($order->method == 4)
                                    余额
                                @endif
                            </td>
                        </tr>
                        @else
                            <tr>
                                <td colspan="11">没有查询到相关数据</td>
                            </tr>
                        @endif
                        <tbody>
                    </table>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <h3>订餐信息</h3>
                        <thead>
                        <tr>
                            <th>订单编号</th>
                            <th>用餐人数</th>
                            <th>用餐时间</th>
                            <th>备注</th>
                        </tr>
                        @if(!empty($order))
                        </thead>
                        <tr>
                            <td>{{$order->order_sn}}</td>
                            <td>{{$order->people}}</td>
                            <td>{{$order -> dinnertime}}</td>
                            <td>{{$order->remark}}</td>
                        </tr>
                        @else
                            <tr>
                                <td colspan="11">没有查询到相关数据</td>
                            </tr>
                        @endif
                        <tbody>
                    </table>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <h3>菜品详情</h3>
                        <thead>
                        <tr>
                            <th>菜品名称</th>
                            <th>菜品图片</th>
                            <th>菜品价格</th>
                            <th>预定数量</th>
                        </tr>
                        @if(!empty($order))
                        </thead>
                        @foreach($goods_information as $v)
                        <tr>
                            <td>{{ $v->name }}</td>
                            <td><img src="{{ env('IMAGE_PATH_PREFIX')}}{{$v->image}}" alt="" style="width: 55px;height: 55px;"></td>
                            <td>￥{{$v->price}}</td>
                            <td>{{$v->shuliang}}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="2">合计：</td>
                            <td colspan="2">￥{{$order->prices}}</td>
                        </tr>
                        @else
                            <tr>
                                <td colspan="11">没有查询到相关数据</td>
                            </tr>
                        @endif
                        <tbody>
                    </table>
                    <div>
                        <hr/>
                    </div>

                    @if(empty($order -> id))
                        <div class="form-group">
                            <div class="col-sm-12 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>　
                                <button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                            </div>
                        </div>
                    @endif
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        // 用js计算菜品的总金额
        var p = document.getElementsByName('price[]');
        var num = document.getElementsByName('num[]');
        var c = 0;
        for (var i = 0;i<p.length;i++){
            c = c + (p[i].value * num[i].value);
        }
        document.getElementById('prices').value=c;
        // 当更改菜品数量是，重新计算菜品总金额
        function calculation() {
            var p = document.getElementsByName('price[]');
            var num = document.getElementsByName('num[]');
            var c = 0;
            for (var i = 0;i<p.length;i++){
                c = c + (p[i].value * num[i].value);
            }
            document.getElementById('prices').value=c;
        }

        layui.use('laydate', function(){
            var laydate = layui.laydate;
            //执行一个laydate实例
            laydate.render({
                elem: '#test1' //指定元素
                ,type: 'datetime'
            });
        });
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
