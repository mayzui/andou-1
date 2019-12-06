@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>酒店预定</h5>
            </div>
            <div class="ibox-content">
                
                
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>&nbsp;
                    <style>
                        th ,td{ 
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th width="100">ID</th>
                            <th>下单用户</th>
                            <th>预定商户</th>
                            <th>预定编号</th>
                            <th>入住人姓名</th>
                            <th>入住人电话</th>
                            <th>身份证</th>
                            <th>房间型号</th>
                            <th>入住时间</th>
                            <th>离开时间</th>
                            <th>预定天数</th>
                            <th>入住人数</th>
                            <th>订单金额</th>
                            <th>支付金额</th>
                            <th>支付方式</th>
                            <th>订单状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
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
                                <td>{{$item->pay_way}}</td>
                                <td>
                                @if($item->status == 0)
                                    已取消
                                @elseif($item->status == 10)
                                    未支付
                                @elseif($item->status == 20)
                                    已支付
                                @elseif($item->status == 30)
                                    入住中
                                @elseif($item->status == 40)
                                    已离店
                                @endif
                                </td>
                                <td class="text-center">    
                                    <div class="btn-group">
                                    <a href="javascript:;"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 待定操作</button></a>   
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{$data}}
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
@endsection