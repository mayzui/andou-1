@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>用户点餐订单表</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>&nbsp;
                {{--判断用户是否是超级管理员，超级管理员不能新增规格--}}
                {{--@if($id)--}}
                <a href="{{route('foods.orderadd')}}" link-url="javascript:void(0)">
                    <button class="btn btn-primary btn-sm" type="button">
                        <i class="fa fa-plus-circle"></i> 新增点餐订单</button>
                </a>
                {{--@endif--}}
                <style>
                    th ,td{
                        text-align: center;
                    }
                </style>
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th width="100">ID</th>
                        <th>用户ID</th>
                        <th>联系电话</th>
                        <th>下单时间</th>
                        <th>用餐时间</th>
                        <th>用餐人数</th>
                        <th>备注</th>
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
                                <th>{{ $v->user_id }}</th>
                                <th>{{ $v->phone }}</th>
                                <th>{{ $v->orderingtime }}</th>
                                <th>{{ $v->dinnertime }}</th>
                                <th>{{ $v->people }}</th>
                                <th>{{ $v->remark }}</th>
                                <th>{{ $v->price }}</th>
                                <th>{{ $v->status }}</th>
                                <th>{{ $v->method }}</th>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{route('foods.cartadd')}}?id={{$v->id}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>
                                        <a onclick="del({{$v->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-ban"></i> 删除</button></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th colspan="11">暂时还没有数据</th>
                        </tr>
                    @endif
                    </tbody>
                </table>

            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <script type="text/javascript">
        function del(e) {
            var id = e;
            layer.alert("是否删除该数据？",{icon:3},function (index) {
                location.href="{{route('foods.cartdel')}}?id="+id;
                layer.close(index);
            });
        }
    </script>

@endsection