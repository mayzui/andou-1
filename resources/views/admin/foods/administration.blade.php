@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>商户管理</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>&nbsp;
                {{--判断用户是否是超级管理员，超级管理员不能新增菜品--}}
                {{--@if($id)--}}
                    {{--<a href="{{route('foods.add')}}" link-url="javascript:void(0)">--}}
                        {{--<button class="btn btn-primary btn-sm" type="button">--}}
                            {{--<i class="fa fa-plus-circle"></i> 新增分类</button>--}}
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
                            <th>商户ID</th>
                            <th>商户类型</th>
                            <th>饭店名称</th>
                            <th>菜品分类</th>
                            <th>饭店地址</th>
                            {{--<th>操作</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                            @if(count($data) > 0)
                                @foreach($data as $v)
                                    <tr>
                                        <th>{{$v->id}}</th>
                                        <th>{{$v->type_name}}</th>
                                        <th>{{$v->name2}}</th>
                                        <th>{{$v->name}}</th>
                                        <th>{{$v->address}}</th>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                {{--<button class="btn btn-primary btn-xs" type="button">详情</button>--}}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @else
                                <tr>
                                    <th colspan="7">暂时还没有数据</th>
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
                location.href="{{route('foods.del')}}?id="+id;
                layer.close(index);
            });
        }
    </script>

@endsection