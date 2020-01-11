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
                @if($status==20)
                    <a href="{{url("/admin/logistics/goGoods?id=$id")}}" ><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-check" ></i> 订单发货</button></a>
                @else
                @endif
            </div>
            <div class="ibox-content">
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
{{--                基本信息--}}
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


{{--                 收货人信息--}}
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
                    @if(empty($uid->express_id))
                    <tr>
                        <td>{{$address->name}}</td>
                        <td>{{$address->mobile}}</td>
                        <td>
                            @if($uid->express_id == 1)
                                韵达快递
                            @elseif($uid->express_id == 2)
                                申通快递
                            @elseif($uid->express_id == 3)
                                圆通速递
                            @elseif($uid->express_id == 4)
                                邮政快递包裹
                            @elseif($uid->express_id == 5)
                                中通快递
                            @elseif($uid->express_id == 6)
                                顺丰速运
                            @elseif($uid->express_id == 7)
                                百世快递
                            @elseif($uid->express_id == 8)
                                京东物流
                            @elseif($uid->express_id == 9)
                                天天快递
                            @elseif($uid->express_id == 10)
                                EMS
                            @elseif($uid->express_id == 11)
                                德邦
                            @elseif($uid->express_id == 12)
                                DHL-中国件
                            @elseif($uid->express_id == 13)
                                优速快递
                            @endif
                        </td>
                        <td>{{$address->address}}</td>
                    </tr>
                    @else
                        <tr>
                            <td colspan="11">没有查询到相关数据</td>
                        </tr>
                    @endif
                    <tbody>
                </table>

{{--                商品信息--}}
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <h3 style="margin-top: 20px;">商品信息</h3>
                    <thead>
                    <tr>
                        <th>商品图片</th>
                        <th>商品名称</th>
                        <th>价格/货号</th>
                        <th>属性</th>
                        <th>数量</th>
                        <th>库存</th>
                        <th>小计</th>
                    </tr>
                    </thead>
                    @if(count($good) > 0)
                        @foreach($good as $k => $item)
                    <tr>
                        <td><img src="{{ env('IMAGE_PATH_PREFIX')}}{{$item->img}}" alt="" style="width: 55px;height: 55px;"></td>
                        <td>{{$item->name}}</td>
                        <td>
                            <p>价格:{{$item->price}}</p>
                            <p>货号:{{$item->good_num}}</p>
                        </td>
                        <td>
                            @php
                                $arr = json_decode($item->attr_value);
                                $dou = $arr[0]->name;
                                $val = $arr[0]->value;
                                     for($i=0;$i<count($dou);$i++){
                                         echo "<span>" ."$dou[$i]</span>".":". "<span>" ."$val[$i]</span>"."<br>";
                                     }
                            @endphp
                        </td>

                        <td>{{$item->num}}</td>
                        <td>
                            {{$item->store_num}}
                        </td>
                        <td>{{$item->price}}</td>
                    </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="11">没有查询到相关数据</td>
                        </tr>
                    @endif
                    <tbody>
                </table>

                <span style="margin-left: 95%">合计:<font style="color:red">
                        @php
                            $sum=0;
                          foreach ($num as &$value)
                          {
                            $sum+=$value;
                            }
                          echo $sum
                        @endphp</font></span>

{{--                        发票信息--}}
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <h3>发票信息</h3>
                    <thead>
                    <tr>
                        <th>发票类型</th>
                        <th>发票抬头</th>
                        <th>发票内容</th>
                        <th>收票人信息</th>
                    </tr>
                    </thead>
                    @if(!empty($tick) && !empty($user))
                    <tr>
                        <td>
                            @if($tick[0]->is_vat==0)
                                普通发票
                            @else
                                增值发票
                            @endif
                        </td>
                        <td>{{$tick[0]->invoice_title}}</td>
                        <td>{{$tick[0]->invoice_content}}</td>
                        <td>{{$user[0]->mobile}}</td>
                    </tr>
                    @else
                        <tr>
                            <td colspan="11">没有查询到相关数据</td>
                        </tr>
                        @endif
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
