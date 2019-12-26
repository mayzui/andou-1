@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>售后服务</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
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
                            <th>用户名</th>
                            <th>退款原因</th>
                            <th>退款说明</th>
                            <th>售后类型</th>
                            <th>审核状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->order_id }}</td>
                                <td>{{$item->user_name}}</td>
                                <td>{{$item->retun_name}}</td>
                                <td>{{$item->content}}</td>
                                <td>{{$item->status == 1 ? "退货退款" : "仅退款"}}</td>
                                <td>
                                    @if($item->is_reg == 0)
                                        <p style="color: blue">申请退款中</p>
                                        @elseif($item->is_reg == 1)
                                        <p style="color: green">已同意退款</p>
                                        @elseif($item->is_reg == 2)
                                        <p style="color: blue">用户发货中</p>
                                        @elseif($item->is_reg == 3)
                                        <p style="color: green">已完成</p>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="btn-group">
                                        @if($item->status == 1)
                                            @if($item->is_reg==0)
                                                <a onclick="nexts({{$item->id}})"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-check"></i> 同意退款</button></a>
                                                <a href="{{route('refund.aftermarketChange')}}?ids={{$item->id}}"><button class="btn btn-success btn-xs" type="button"><i class="fa fa-paste"></i> 详情</button></a>
                                            @elseif($item->is_reg==1)
                                                <a onclick="nexts({{$item->id}})"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-check"></i> 下一步</button></a>
                                            @elseif($item->is_reg==2)
                                                <a onclick="nexts({{$item->id}})"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-check"></i> 完成</button></a>
                                            @elseif($item->is_reg==3)
                                                <a><button class="btn btn-primary btn-xs" type="button" disabled><i class="fa fa-check"></i> 操作成功</button></a>
                                            @endif
                                            @else
                                            @if($item->is_reg==0)
                                                <a onclick="nexts({{$item->id}})"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-check"></i> 同意退款</button></a>
                                            @elseif($item->is_reg==1)
                                                <a onclick="nexts({{$item->id}})"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-check"></i> 完成</button></a>
                                            @elseif($item->is_reg==2)
                                                <a onclick="nexts({{$item->id}})"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-check"></i> 完成</button></a>
                                            @elseif($item->is_reg==3)
                                                <a><button class="btn btn-primary btn-xs" type="button" disabled><i class="fa fa-check"></i> 操作成功</button></a>
                                            @endif
                                        @endif
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
    <script type="text/javascript">
        function nexts(e) {
            var id = e;
            layer.alert("更改当前状态？",{icon:3},function (index) {
                location.href="{{route('refund.aftermarketChange')}}?id="+id;
                layer.close(index);
            });
        }
    </script>
@endsection