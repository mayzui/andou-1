@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>物流信息</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <form method="post" action="" name="form">

                    <style>
                        th ,td{
                            font-size: 14px;
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th width="100">ID</th>
                            <th width="230">订单编号</th>
                            <th>商户名称</th>
                            <th>用户名称</th>
                            <th style="width: 200px">商品名称</th>
                            <th>商品数量</th>
                            <th>运费</th>
                            <th>总金额</th>
                            <th>快递公司</th>
                            <th>快递单号</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($list) > 0)
                        @foreach($list as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->order_id}}</td>
                                <td>{{$item->merchants_name}}</td>
                                <td>{{$item->users_name}}</td>
                                <td><p style="width: 200px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">{{$item->goods_name}}</p></td>
                                <td>{{$item->num}}</td>
                                <td>{{$item->shipping_free}}</td>
                                <td>{{$item->total}}</td>
                                <td>
                                    @if($item->express_id == 1)
                                        韵达快递
                                        @elseif($item->express_id == 2)
                                        申通快递
                                        @elseif($item->express_id == 3)
                                        圆通速递
                                        @elseif($item->express_id == 4)
                                        邮政快递包裹
                                        @elseif($item->express_id == 5)
                                        中通快递
                                        @elseif($item->express_id == 6)
                                        顺丰速运
                                        @elseif($item->express_id == 7)
                                        百世快递
                                        @elseif($item->express_id == 8)
                                        京东物流
                                        @elseif($item->express_id == 9)
                                        天天快递
                                        @elseif($item->express_id == 10)
                                        EMS
                                        @elseif($item->express_id == 11)
                                        德邦
                                        @elseif($item->express_id == 12)
                                        DHL-中国件
                                        @elseif($item->express_id == 13)
                                        优速快递
                                    @endif
                                </td>
                                <td>{{$item->courier_num}}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        @if(empty($item->express_id))
                                            <a href="{{route('logistics.goGoods')}}?id={{$item->id}}">
                                                <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-check"></i> 去发货</button>
                                            </a>
                                            @else
                                            <a href="{{route('logistics.readLogistics')}}?id={{$item->id}}&express_id={{ $item -> express_id }}&courier_num={{ $item ->courier_num }}">
                                                <button class="btn btn-info btn-xs" type="button"><i class="fa fa-check"></i> 查看物流信息</button>
                                            </a>
                                            <a href="{{route('logistics.updateLogistics')}}?id={{$item->id}}">
                                                <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改物流单号</button>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                            @else
                            <tr>
                                <td colspan="11">对不起未查询到相关内容</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    {{$list}}
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
@endsection