@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>商户菜品规格</h5>
            </div>
            <div class="ibox-content">
                <form method="post" action="{{route('foods.spec')}}" name="form">
                    {{ csrf_field() }}
                    <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                    {{--判断用户是否是超级管理员，超级管理员不能新增规格--}}
                    {{--@if($id)--}}
                    <a href="{{route('foods.specadd')}}" link-url="javascript:void(0)">
                        <button class="btn btn-primary btn-sm" type="button">
                            <i class="fa fa-plus-circle"></i> 新增规格</button>
                    </a>
                    {{--@endif--}}
                    <input type="text" style="height: 25px;margin-left: 10px;" value="{{ $name or '' }}" name="name" placeholder="商户名称">
                    <button style="height: 25px;margin-left: 10px;" type="submit">按条件查询</button>
                </form>

                <style>
                    th ,td{
                        text-align: center;
                    }
                </style>
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th width="100">ID</th>
                        <th>商户名称</th>
                        <th>规格名称</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($data) > 0)
                        @foreach($data as $v)
                            <tr>
                                <th>{{ $v->id }}</th>
                                <th>{{ $v->merchants_name }}</th>
                                <th>{{ $v->spec_name }}</th>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{route('foods.specadd')}}?id={{$v->id}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>
                                        <a onclick="del({{$v->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-ban"></i> 删除</button></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr style="height: 35px">
                            <td  colspan="7" style="text-align: center;">{{$data}}</td>
                        </tr>
                    @else
                        <tr>
                            <th colspan="4">暂时还没有数据</th>
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
                location.href="{{route('foods.specdel')}}?id="+id;
                layer.close(index);
            });
        }
    </script>

@endsection