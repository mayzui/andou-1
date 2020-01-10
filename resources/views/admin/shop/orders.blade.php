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
            <div class="ibox-title">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
            </div>
            <div class="ibox-content">
                <div class="col-sm-3" style="padding-left: 0px; width: 100%;">
                    <div class="input-group">
                        <a href="{{url("/admin/shop/orders?status=70")}}">
                            <button class="btn btn-primary " type="button"><i class="fa fa-paste">全部订单@php if(empty($count)){echo 0;}else {echo (count($count['data5']));} @endphp</i></button>
                        </a>
                        <a href="{{url("/admin/shop/orders?status=10")}}">
                            <button class="btn btn-primary " type="button"><i class="fa fa-paste">待付款@php if(empty($count)){echo 0;}else {echo (count($count['data']));} @endphp</i></button>
                        </a>

                        <a href="{{url("/admin/shop/orders?status=20")}}">
                            <button class="btn btn-primary " type="button"><i class="fa fa-paste">待发货@php if(empty($count)){echo 0;}else {echo (count($count['data1']));} @endphp</i></button>
                        </a>

                        <a href="{{url("/admin/shop/orders?status=40")}}">
                            <button class="btn btn-primary " type="button"><i class="fa fa-paste">已发货@php if(empty($count)){echo 0;}else {echo (count($count['data2']));} @endphp</i></button>
                        </a>

                        <a href="{{url("/admin/shop/orders?status=50")}}">
                            <button class="btn btn-primary " type="button"><i class="fa fa-paste">已完成@php if(empty($count)){echo 0;}else {echo (count($count['data3']));} @endphp</i></button>
                        </a>

                        <a href="{{url("/admin/shop/orders?status=60")}}">
                            <button class="btn btn-primary " type="button"><i class="fa fa-paste">已关闭@php if(empty($count)){echo 0;}else {echo (count($count['data4']));} @endphp</i></button>
                        </a>

                        {{--                        收货人:<input type="text" style="height: 25px;margin-left: 10px;" class="userval" onkeydown="user()" placeholder="收货人姓名/手号码">--}}

                        输入搜索:<input type="text" style="height: 25px;margin-left: 10px; width: 200px;" name="search" id="sval" onkeydown="search()" placeholder="订单编号/收货人姓名/手号码">

                        提交时间:<input type="date"  class="time" onkeydown="time()" placeholder="请选择时间">

                         <button class="btn btn-primary " type="button"><i class="fa fa-search" id="pse">搜索</i></button>
                    </div>
                </div>
                <style>
                    th ,td{
                        text-align: center;
                    }
                </style>
                <div class="hr-line-dashed"></div>
                <form method="post" action="{{route('shop.index')}}" name="form">
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th width="50px"><input type="checkbox" id="checkall" /></th>
                            <th width="100">ID</th>
                            <th>订单编号</th>
                            <th>提交时间</th>
                            <th>用户账号</th>
                            <th>订单金额</th>
                            <th>支付方式</th>
                            <th>订单来源</th>
                            <th>订单状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        @if(count($list) > 0)
                        @foreach($list as $k => $item)
                            <tr>
                                <td><input type="checkbox" name="ids" value="{{$item->id}}" /></td>
                                <td>{{$item->id}}</td>
                                <td>{{$item->order_sn}}</td>
                                <td>{{$item->created_at}}</td>
                                <td>{{$item->mobile}}</td>
                                <td>{{$item->pay_money}}</td>
                                <td>
                                    @if($item->pay_way == 1)
                                        微信支付
                                    @elseif($item->pay_way == 2)
                                        支付宝支付
                                    @elseif($item->pay_way == 4)
                                        平台余额支付
                                    @elseif($item->pay_way == 3)
                                        银联支付
                                    @elseif($item->pay_way == 0)
                                        未选择支付方式
                                        @else
                                        其他方式
                                    @endif
                                </td>
                                <td>
                                    @if($item->order_source==1)
                                        APP订单
                                    @else
                                    @endif
                                </td>
                                <td>
                                    @if($item->statuss == 0)
                                        <font color="880000">已取消</font>
                                    @elseif($item->statuss == 10)
                                        <font color="red">待付款</font>
                                    @elseif($item->statuss == 20)
                                        <font color="#ff6600">待发货</font>
                                    @elseif($item->statuss == 40)
                                        <font color="#cc9900">已发货</font>
                                    @elseif($item->statuss == 50)
                                        <font color="#228b22">已完成</font>
                                    @elseif($item->statuss== 60)
                                        <font color="#004400">已关闭</font>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                                <a href="{{url("/admin/shop/ordersUpd?id=$item->id&status=$item->statuss&express_id=$item->express_id&courier_num=$item->courier_num")}}">
                                                    <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i>查看订单</button>
                                                </a>
                                        @if($item->statuss == 0)
                                            <font color="880000">已取消</font>
                                        @elseif($item->statuss == 20)
                                            <a href="{{url("/admin/logistics/goGoods?id=$item->id")}}" ><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-check" ></i> 订单发货</button></a>
                                        @elseif($item->statuss == 40)
                                            <a href="{{url("/admin/logistics/readLogistics?id=$item->id&express_id=$item->express_id&courier_num=$item->courier_num")}}" ><button class= "btn btn-danger btn-xs"type="button"><i class="fa fa-check" ></i> 订单跟踪</button></a>
                                        @elseif($item->statuss == 50)
                                            <a href="{{url("/admin/logistics/readLogistics?id=$item->id&express_id=$item->express_id&courier_num=$item->courier_num")}}" ><button class= "btn btn-danger btn-xs"type="button"><i class="fa fa-check" ></i> 订单跟踪</button></a>
                                        @elseif($item->statuss == 60)
                                            <a onclick="del({{$item->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除订单</button></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                            @else
                            <tr>
                                <td colspan="11">没有查询到相关数据</td>
                            </tr>
                        @endif
                        <tbody>
                    </table>
                       @if(count($list)>0)
                         @if(empty($item->order_show))
                             @if(empty($timess) && empty($namess) && empty($mobiless) && empty($numss))
                                { $list->appends(['status'=>$item->statuss]) }}
                            @else
                                 {{$list}}}
                            @endif
                             @else
                         {{$list}}
                             @endif
                           @else
                           @endif

                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <script type="text/javascript">
        function del(e) {
            var id = e;
            layer.alert("是否删除该数据？",{icon:3},function (index) {
                location.href="{{route('shop.ordersDel')}}?id="+id;
                layer.close(index);
            });
        }
         //搜索订单编号
        function search() {
            var keyword = $("#sval").val()
            if (event.keyCode==13){
                location.href="{{route('shop.orders')}}?keyword="+keyword +"&sta="+"1"+ "&good_num="+keyword;
            }
        }
       //搜索收货人
        function user() {
            var user = $(".userval").val()
            if (event.keyCode==13){
                location.href="{{route('shop.orders')}}?user="+user +"&sta="+"2" +"&name="+user;
            }
        }

        function time() {

            if (event.keyCode==13){
                var time = $(".time").val()
                location.href="{{route('shop.orders')}}?time="+time;
            }
        }
        // 实现全选
        $("#checkall").click(function () {
            if(this.checked){
                $("[name=ids]:checkbox").prop("checked",true);
            }else{
                $("[name=ids]:checkbox").prop("checked",false);
            }
        })

        //搜索

        $("#pse").click(function () {
            var vals = $("#sval").val()
            var times = $(".time").val()
            location.href="{{route('shop.orders')}}?num="+vals +"&mobiles="+ vals+"&names="+vals+"&times="+times
        })


    </script>
@endsection