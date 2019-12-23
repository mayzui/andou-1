@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>广告位管理</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <a href="{{route('banner.positionAdd')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 新增广告位</button></a>
                <form method="post" action="{{route('banner.position')}}" name="form">
                    <style>
                        th ,td{
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th width="100">ID</th>
                            <th>位置名称</th>
                            <th>启用状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->name}}</td>
                                <td style="color: blue">{{$item->status == 0 ? "未启用" : "启用中"}}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a onclick="del({{$item->id}})">
                                            <button class="btn btn-group btn-xs" type="button"><i class="fa fa-warning"></i> {{$item->status == 0 ? "启用" : "禁用"}}</button>
                                        </a>
                                        <a href="{{route('banner.positionEdit')}}?id={{$item->id}}">
                                            <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{$list}}
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <script type="text/javascript">
        function del(e) {
            var id = e;
            layer.alert("是否修改该数据的状态？",{icon:3},function (index) {
                location.href="{{route('banner.positionDel')}}?id="+id;
                layer.close(index);
            });
        }
    </script>
@endsection