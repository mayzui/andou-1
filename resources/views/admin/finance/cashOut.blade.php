@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>提现管理</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>&nbsp;
                {{--判断用户是否是超级管理员，超级管理员不能新增菜品--}}
                {{--@if($id)--}}
                    {{--<a href="{{route('finance.cashOutChange')}}" link-url="javascript:void(0)">--}}
                        {{--<button class="btn btn-primary btn-sm" type="button">--}}
                                {{--<i class="fa fa-plus-circle"></i> 新增管理明细</button>--}}
                    {{--</a>--}}
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
                            <th>用户名称</th>
                            <th>提现金额</th>
                            <th>提现时间</th>
                            <th>审核状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            @if(count($data) > 0)
                                @foreach($data as $v)
                                    <tr>
                                        <th>{{ $v -> id }}</th>
                                        <th>{{ $v -> name }}</th>
                                        <th>{{ $v -> price }}</th>
                                        <th>{{ $v -> create_time }}</th>
                                        <th style="color: blue;">{{ $v -> status == 1 ? "已审核" : "未审核" }}</th>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                @if($v -> status == 0)
                                                    <a onclick="examine({{$v->id}})"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-check"></i> 审核通过</button></a>
                                                    @else
                                                    <a onclick="examine({{$v->id}})"><button class="btn btn-primary btn-xs" type="button" disabled><i class="fa fa-check"></i>  审核通过</button></a>
                                                @endif
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
                location.href="{{route('finance.cashOutDel')}}?id="+id;
                layer.close(index);
            });
        }
        function examine(e) {
            var id = e;
            layer.alert("是否确认该数据提现？",{icon:3},function (index) {
                location.href="{{route('finance.cashOutExamine')}}?id="+id;
                layer.close(index);
            });
        }
    </script>

@endsection