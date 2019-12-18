@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>活动管理</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>

                <a href="{{route('shop.activityChange')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                        <i class="fa fa-plus-circle"></i>新增活动</button>
                </a>

                <form method="post" action="{{route('shop.express')}}" name="form">
                    <style>
                        th ,td{
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th>活动ID</th>
                            <th>商家名称</th>
                            <th>活动名称</th>
                            <th>活动商品</th>
                            <th>开始时间</th>
                            <th>结束时间</th>
                            <th>活动状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->merchants_name}}</td>
                                <td>{{$item->activity_name}}</td>
                                <td>
                                    @foreach(json_decode($item->goods) as $v)
                                        {{$v->names}}
                                    @endforeach
                                </td>
                                <td>{{$item->create_time}}</td>
                                <td>{{$item->end_time}}</td>
                                <td style="color: blue">{{$item->status == 1 ? "开启" : "未开启"}}</td>
                                <td>
                                    <a href="{{route('shop.activityChange')}}?id={{$item->id}}">
                                        <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button>
                                    </a>
                                    <a onclick="del({{$item->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{$data}}
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <script type="text/javascript">
        function del(e) {
            var id = e;
            layer.alert("是否删除该数据？",{icon:3},function (index) {
                location.href="{{route('shop.activityDel')}}?id="+id;
                layer.close(index);
            });
        }
    </script>
@endsection