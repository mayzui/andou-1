@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>饭店管理</h5>
            </div>
            <div class="ibox-content">
                <form method="post" action="{{route('foods.administration')}}" name="form">
                    {{ csrf_field() }}
                    <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                    <input type="text" style="height: 25px;margin-left: 10px;" value="{{ $name or '' }}" name="name" placeholder="饭店名称">
                    <button style="height: 25px;margin-left: 10px;" type="submit">按条件查询</button>

                    <a href="{{url('/admin/foods/administration?status=0')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            全部 {{count($data)}}</button>
                    </a>
                    <a href="{{url('/admin/foods/administration?status=1')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            已审核 {{count($old)}}</button>
                    </a>
                    <a href="{{url('/admin/foods/administration?status=2')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            待审核 {{ count($wait) }}</button>
                    </a>
                    <a href="{{url('/admin/foods/administration?status=4')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            已启用 {{count($old)}}</button>
                    </a>
                </form>
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
                            <th>状态</th>
                            <th>操作</th>
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
                                        <th>
                                            {{--@if($v->reg == 0)--}}
                                                {{--<p style="color: green">待审核</p>--}}
                                            {{--@else--}}
                                            @if($v->is_reg == 0)
                                                <p style="color: blue">未审核</p>
                                            @elseif($v->foods_status == 1)
                                                <p style="color: green">启用中</p>
                                            @else
                                                <p style="color: blue">未启用</p>
                                            @endif
                                            {{--@endif--}}
                                        </th>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                @if(empty($i))
                                                    @if($v->foods_status==1)
                                                        <a href="{{route('foods.administrationStatus')}}?id={{$v->foods_id}}&is_reg=1"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 禁用</button></a>
                                                    @else
                                                        <a href="{{route('foods.administrationStatus')}}?id={{$v->foods_id}}&is_reg=0"><button class="btn btn-group btn-xs" type="button"><i class="fa fa-adn"></i>启用</button></a>
                                                    @endif
                                                @endif
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
                {{$data}}
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <script type="text/javascript">
        function del(e) {
            var id = e;
            layer.alert("是否更新状态？",{icon:3},function (index) {
                location.href="{{route('foods.del')}}?id="+id;
                layer.close(index);
            });
        }
    </script>

@endsection