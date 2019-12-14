@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>套餐列表</h5>
            </div>
            <div class="ibox-content">
                <form method="post" action="{{route('foods.set_meal')}}" name="form">
                    {{ csrf_field() }}
                    <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                    {{--判断用户是否是超级管理员，超级管理员不能新增菜品--}}
                    {{--@if($id)--}}
                    <a href="{{route('foods.set_mealchange')}}" link-url="javascript:void(0)">
                        <button class="btn btn-primary btn-sm" type="button">
                            <i class="fa fa-plus-circle"></i> 新增套餐</button>
                    </a>
                    {{--@endif--}}
                    <input type="text" style="height: 25px;margin-left: 10px;" value="{{ $name or '' }}" name="name" placeholder="套餐名称">
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
                            <th>套餐名称</th>
                            <th style="width: 200px;">套餐图片</th>
                            <th>套餐价格</th>
                            <th>几人餐</th>
                            <th>有无包间</th>
                            <th>包间价格</th>
                            <th>套餐状态</th>
                            <th style="width: 300px;">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            @if(count($data) > 0)
                                @foreach($data as $v)
                                    <tr>
                                        <th>{{$v->id}}</th>
                                        <th>{{$v->merchants_name}}</th>
                                        <th>{{$v->set_meal_name}}</th>
                                        <th><img src="{{$v->image}}" style="width: 100px;"></th>
                                        <th>{{$v->price}}</th>
                                        <th>{{$v->num}}</th>
                                        <th>{{$v->room == 1 ? "有包间" : "无包间"}}</th>
                                        <th>{{$v->room_price}}</th>
                                        <th style="color: blue">{{$v->status == 1 ? "上架中" : "未上架"}}</th>
                                        <td class="text-center">
                                            <div cass="btn-group">
                                                <a href="{{route('foods.set_meal_information')}}?id={{$v->id}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-rebel"></i> 菜品信息</button></a>
                                                <a href="{{route('foods.set_mealchange')}}?id={{$v->id}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>
                                                <a onclick="del({{$v->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-ban"></i> 删除</button></a>
                                                @if($v->status == 1)
                                                    <a onclick="statuse({{$v->id}})"><button class="btn btn-group btn-xs" type="button"><i class="fa fa-calendar"></i> 下架</button></a>
                                                    @else
                                                    <a onclick="statuse({{$v->id}})"><button class="btn btn-group btn-xs" type="button"><i class="fa fa-calendar"></i> 上架</button></a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @else
                                <tr>
                                    <th colspan="10">暂时还没有数据</th>
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
                location.href="{{route('foods.set_mealdel')}}?id="+id;
                layer.close(index);
            });
        }
        function statuse(e) {
            var id = e;
            layer.alert("是否修改上/下架状态？",{icon:3},function (index) {
                location.href="{{route('foods.set_mealstatus')}}?id="+id;
                layer.close(index);
            });
        }
    </script>

@endsection