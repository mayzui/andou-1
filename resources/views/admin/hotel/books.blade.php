@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>酒店预定</h5>
            </div>
            <div class="ibox-content">
                
                <form method="post" action="{{route('hotel.books')}}" name="form">
                {{ csrf_field() }}
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <input type="text" style="height: 25px;margin-left: 10px;" value="{{$wheres['book_sn'] or ''}}" name="book_sn" placeholder="预订编号/姓名/电话">
                    <button class="btn btn-primary btn-sm" type="submit"><i class="fa fa-search"></i> 查询</button>
                    <a href="{{url('/admin/hotel/books')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            全部</button>
                    </a>
                    <a href="{{url('/admin/hotel/books?status=30')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            已入住</button>
                    </a>
                    <a href="{{url('/admin/hotel/books?status=20')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            待入住</button>
                    </a><a href="{{url('/admin/hotel/books?status=40')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            已完成</button>
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
                            <th width="50">ID</th>
                            <th width="70">下单用户</th>
                            <th>预定商户</th>
                            <th>预定编号</th>
                            <th>入住人姓名</th>
                            <th>入住人电话</th>
                            <th>身份证</th>
                            <th width="70">房间型号</th>
                            <th width="100">入住时间</th>
                            <th width="100">离开时间</th>
                            <th width="50">预定天数</th>
                            <th width="50">入住人数</th>
                            <th width="70">订单金额</th>
                            <th width="70">支付金额</th>
                            <th width="70">支付方式</th>
                            <th width="70">订单状态</th>
                            <th width="150">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($data) > 0)
                        @foreach($data as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->user_id}}</td>
                                <td>{{$item->merchant_id}}</td>
                                <td>{{$item->book_sn}}</td>
                                <td>{{$item->real_name}}</td>
                                <td>{{$item->mobile}}</td>
                                <td>{{$item->id_card_no}}</td>
                                <td>{{$item->hotel_room_id}}</td>
                                <td>{{$item->start_time}}</td>
                                <td>{{$item->end_time}}</td>
                                <td>{{$item->day_num}}</td>
                                <td>{{$item->num}}</td>
                                <td>{{$item->money}}</td>
                                <td>{{$item->pay_money}}</td>
                                <td>
                                    @if($item->pay_way == 0)
                                        未支付
                                        @elseif($item->pay_way == 1)
                                        <span style="color:blue">微信</span>
                                        @elseif($item->pay_way == 2)
                                        支付宝
                                        @elseif($item->pay_way == 3)
                                        银联
                                        @elseif($item->pay_way == 4)
                                        余额
                                        @elseif($item->pay_way == 5)
                                        其他
                                    @endif
                                </td>
                                <td>
                                @if($item->status == 0)
                                    <span style="color: red">已取消</span>
                                @elseif($item->status == 10)
                                        <span style="color: red">未支付</span>
                                @elseif($item->status == 20)
                                        <span style="color: blue">待入住</span>
                                @elseif($item->status == 30)
                                        <span style="color: blue">入住中</span>
                                @elseif($item->status == 40)
                                        <span style="color: green">已完成</span>
                                @elseif($item->status == 60)
                                        <span style="color: red">申请退款(未入住)</span>
                                @elseif($item->status == 70)
                                        <span style="color: green">退款成功</span>
                                @endif
                                </td>
                                <td class="text-center">    
                                    <div class="btn-group">
                                        @if($item->status == 20)
                                            <a onclick="write_off({{$item->id}});"><button class="btn btn-success btn-xs" type="button"><i class="fa fa-check"></i> 核销</button></a>
                                            <a onclick="return_money({{$item->id}});"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-close"></i> 退款</button></a>
                                            @elseif($item->status == 30)
                                            <a  onclick="write_off({{$item->id}});"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-paste"></i> 退房</button></a>
                                            @elseif($item->status == 60)
                                            <a onclick="return_money({{$item->id}});"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-check"></i> 确认退款</button></a>
                                            @elseif($item->status == 40)
                                            <a href="javascript:;"><button class="btn btn-primary btn-xs" type="button" disabled><i class="fa fa-paste"></i> 已完成</button></a>
                                            <a href="javascript:;"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                                            @elseif($item->status == 70)
                                            <a href="javascript:;"><button class="btn btn-primary btn-xs" type="button" disabled><i class="fa fa-paste"></i> 退款成功</button></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                            @else
                            <tr>
                                <td colspan="17">对不起，没有查询到相关内容</td>
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
        function write_off(e) {
            var id = e;
            layer.alert("核销该用户状态？",{icon:3},function (index) {
                location.href="{{route('hotel.write_off')}}?id="+id;
                layer.close(index);
            });
        }
        function return_money(e) {
            var id = e;
            layer.alert("是否确认退款？",{icon:3},function (index) {
                location.href="{{route('hotel.return_money')}}?id="+id;
                layer.close(index);
            });
        }
    </script>
@endsection