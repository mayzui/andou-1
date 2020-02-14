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
                <h5>秒杀管理</h5>
            </div>
            <div class="ibox-title">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>1
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
                            <th>活动时间</th>
                            <th>已秒</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        @if(count($list) > 0)
                            @foreach($list as $k => $item)
                            <tr>
                                <td>{{$item['id']}}</td>
                                <td>{{$item['goods_name']->name}}</td>
                                <td>{{$item['start_time']}}-{{$item['end_time']}}</td>
                                <td>{{$item['kill_num']}}</td>
                                <td>
                                    @if(now()>$item['start_time'] && now()<$item['end_time'])
                                        @if($item['num']==0)
                                            进行中（已售罄）
                                            @else
                                            进行中
                                            @endif
                                        @else
                                           @if(now()<$item['start_time'])
                                               尚未开始
                                               @else
                                               已结束
                                               @endif
                                    @endif
                                </td>
                                <td style="width: 300px;">
                                    @if(now()>$item['start_time'] && now()<$item['end_time'])
                                        @if($item['num']==0)
                                            <a href="" style="margin-left: 20px;"><font style="color: lightgreen">修改</font></a>
                                            <a onclick="dels({{$item['id']}})" style="margin-left: 20px;"><font style="color: lightgreen">删除</font></a>
                                            <a onclick="del({{$item['id']}})"  style="margin-left: 20px;"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-warning"></i> 下架</button></a>
                                        @else
                                            <a href="" style="margin-left: 20px;"><font style="color: lightgreen">修改</font></a>
                                            <a onclick="del({{$item['id']}})"  style="margin-left: 20px;"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-warning"></i> 下架</button></a>
                                        @endif
                                    @else
                                        @if(now()<$item['start_time'])
                                            <a href="" style="margin-left: 20px;"><font style="color: lightgreen">修改</font></a>
                                            <a onclick="del({{$item['id']}})"  style="margin-left: 20px;"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-warning"></i> 下架</button></a>
                                        @else
                                            <a href="" style="margin-left: 20px;"><font style="color: lightgreen">修改</font></a>
                                            <a onclick="dels({{$item['id']}})" style="margin-left: 20px;"><font style="color: lightgreen">删除</font></a>
                                        @endif
                                    @endif
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
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <script type="text/javascript">
        //下架
        function del(e) {
            var id = e;
            layer.alert("是否下架该数据？",{icon:3},function (index) {
                location.href="{{route('seckill.killdel')}}?id="+id;
                layer.close(index);
            });
        }
        //删除
        function dels(e) {
            var id = e
            layer.alert("是否删除该数据？",{icon:3},function (index) {
                location.href="{{route('seckill.killdels')}}?id="+id;
                layer.close(index);
            });
        }
    </script>
@endsection
