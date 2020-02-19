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
                <h5>秒杀统计</h5>
            </div>
            <div class="ibox-title">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
            </div>
            <div class="ibox-content">
                <style>
                    th ,td{
                        text-align: center;
                    }
                </style>
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th width="100">ID</th>
                        <th>商品名称</th>
                        <th>用户名称</th>
                        <th>价格</th>
                        <th>时间</th>
                        <th>订单编号</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    @if(count($data) > 0)
                        @foreach($data as $k => $item)
                            <tr>
                               <td>{{$item->id}}</td>
                                <td>{{$item->goods_name->name}}</td>
                                <td>{{$item->user_name->name}}</td>
                                <td>{{$item->sec_price}}</td>
                                <td>{{$item->sec_time}}</td>
                                <td>{{$item->order_id}}</td>
                                <td>
                                    <a onclick="dels({{$item->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
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
                {{--{{$data}}--}}
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <script type="text/javascript">
        //删除
        function dels(e) {
            var id = e
            layer.alert("是否删除该数据？",{icon:3},function (index) {
                location.href="{{route('seckill.countdel')}}?id="+id;
                layer.close(index);
            });
        }
    </script>
@endsection