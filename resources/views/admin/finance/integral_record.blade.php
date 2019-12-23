@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>感恩币明细</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>&nbsp;
                {{--判断用户是否是超级管理员，超级管理员不能新增菜品--}}
                {{--@if($id)--}}
                    {{--<a href="{{route('finance.integral_recordChange')}}" link-url="javascript:void(0)">--}}
                        {{--<button class="btn btn-primary btn-sm" type="button">--}}
                            {{--<i class="fa fa-plus-circle"></i> 新增感恩币明细</button>--}}
                    {{--</a>--}}
                    <style>
                        th ,td{
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th width="100">感恩币明细id</th>
                            <th>用户名称</th>
                            <th style="width: 200px">感恩币描述</th>
                            <th>感恩币值</th>
                            <th>感恩币状态</th>
                            <th>操作时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            @if(count($data) > 0)
                                @foreach($data as $v)
                                    <tr>
                                        <th>{{ $v -> id }}</th>
                                        <th>{{ $v -> name }}</th>
                                        <th><p style="width: 200px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">{{ $v -> describe }}</p></th>
                                        <th>{{ $v -> price }}</th>
                                        <th>{{ $v -> state == 1 ? "获得" : "消耗" }}</th>
                                        <th>{{ $v -> create_time }}</th>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a onclick="del({{$v->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-ban"></i> 删除</button></a>
                                            </div>
                                        </td>
                                    </tr>

                                @endforeach
                                @else
                                <tr>
                                    <th colspan="6">暂时还没有数据</th>
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
                location.href="{{route('finance.integral_recordDel')}}?id="+id;
                layer.close(index);
            });
        }
    </script>

@endsection