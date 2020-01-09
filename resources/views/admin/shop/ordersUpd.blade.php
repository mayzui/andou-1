@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('css')
    <style>
        .animated{-webkit-animation-fill-mode: none;}
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
            </div>
            <div class="ibox-content">
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <h3>基本信息</h3>
                    <thead>
                    <tr>
                        <th>订单编号</th>
                        <th>发货单流水号</th>
                        <th>用户账号</th>
                        <th>支付方式</th>
                        <th>订单来源</th>
                        <th>订单类型</th>
                    </tr>
                    </thead>
                            <tr>
                                <td>{{$data->order_id}}</td>
                                <td>{{$data->courier_num}}</td>
                                <td>{{$data->mobile}}</td>
                                <td>
                                    @if($data->pay_way == 1)
                                        微信支付
                                    @elseif($data->pay_way == 2)
                                        支付宝支付
                                    @elseif($data->pay_way == 4)
                                        平台余额支付
                                    @elseif($data->pay_way == 3)
                                        银联支付
                                    @elseif($data->pay_way == 0)
                                        未选择支付方式
                                    @else
                                        其他方式
                                    @endif
                                </td>
                                <td>
                                    @if($data->order_source==1)
                                        APP订单
                                    @else
                                    @endif
                                </td>
                                <td>
                                    @if($data->order_types==1)
                                        普通订单
                                    @else
                                    @endif
                                </td>
                            </tr>
                    <tbody>
                </table>

                <table class="table table-striped table-bordered table-hover m-t-md" >
                    <h3 style="margin-top: 20px;">收货人信息</h3>
                    <thead>
                    <tr>
                        <th>收货人</th>
                        <th>手机号码</th>
                        <th>快递公司</th>
                        <th>收货地址</th>
                    </tr>
                    </thead>
                    <tr>
                        <td>{{$address->name}}</td>
                        <td>
                            @if($address->express_id == 1)
                                韵达快递
                            @elseif($address->express_id == 2)
                                申通快递
                            @elseif($address->express_id == 3)
                                圆通速递
                            @elseif($address->express_id == 4)
                                邮政快递包裹
                            @elseif($address->express_id == 5)
                                中通快递
                            @elseif($address->express_id == 6)
                                顺丰速运
                            @elseif($address->express_id == 7)
                                百世快递
                            @elseif($address->express_id == 8)
                                京东物流
                            @elseif($address->express_id == 9)
                                天天快递
                            @elseif($address->express_id == 10)
                                EMS
                            @elseif($address->express_id == 11)
                                德邦
                            @elseif($address->express_id == 12)
                                DHL-中国件
                            @elseif($address->express_id == 13)
                                优速快递
                            @endif
                        </td>
                        <td>{{$address->courier_num}}</td>
                        <td>{{$address->address}}</td>
                    </tr>
                    <tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="functions" style="display: none;">
        @include('admin.rules.fonticon')
    </div>
@section('footer-js')
    <script>

        function showicon(){
            layer.open({
                type: 1,
                title:'点击选择图标',
                area: ['800px', '80%'], //宽高
                anim: 2,
                shadeClose: true, //开启遮罩关闭
                content: $('#functions')
            });
        }

        $('.fontawesome-icon-list .fa-hover').find('a').click(function(){
            var str=$(this).text();
            $('#fonts').val( $.trim(str));
            layer.closeAll();
        })
    </script>
@endsection
@endsection
