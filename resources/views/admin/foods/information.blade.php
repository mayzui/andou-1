@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>商户菜品详情</h5>
            </div>
            <div class="ibox-content">
                <form method="post" action="{{route('foods.information')}}" name="form">
                    {{ csrf_field() }}
                    <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                    {{--判断用户是否是超级管理员，超级管理员不能新增菜品--}}
                    {{--@if($id)--}}
                    <a href="{{route('foods.informationadd')}}" link-url="javascript:void(0)">
                        <button class="btn btn-primary btn-sm" type="button">
                            <i class="fa fa-plus-circle"></i> 新增菜品</button>
                    </a>
                    {{--@endif--}}
                    <input type="text" style="height: 25px;margin-left: 10px;" value="{{ $name or '' }}" name="name" placeholder="菜品名称">
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
                            <th>分类名称</th>
                            <th style="width: 150px;">菜品名称</th>
                            <th>菜品价格</th>
                            <th>菜品图片</th>
                            {{--<th>菜品规格</th>--}}
                            <th style="width: 200px;">菜品介绍</th>
                            <th>每月销售数量</th>
                            <th>点赞</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($data) > 0)
                                @foreach($data as $v)
                                    <tr>
                                        <th>{{ $v-> id }}</th>
                                        <th>{{ $v-> merchants_name }}</th>
                                        <th>{{ $v-> class_name }}</th>
                                        <th><p style="width: 150px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">{{ $v -> info_name }}</p></th>
                                        <th>{{ $v-> price }}￥</th>
                                        <td>
                                            <img src="{{ $v->image }}" style="width: 80px;height: 80px">
                                        </td>
                                        {{--<th>{{ $v->specifications }}</th>--}}
                                        <th><p style="width: 200px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">{{ $v->remark }}</p></th>
                                        <th>{{ $v->quantitySold }}</th>
                                        <th>{{ $v->num }}</th>
                                        <th>
                                            @if($v->status == 1)
                                                <span style="color: blue">上架中</span>
                                                @else
                                                <span style="color: red">未上架</span>
                                            @endif
                                        </th>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{route('foods.informationadd')}}?id={{$v->id}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>
                                                <a onclick="del({{$v->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-ban"></i> 删除</button></a>
                                                @if($v -> status == 0)
                                                    <a onclick="statuse({{$v->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-check"></i> 上架</button></a>
                                                    @else
                                                    <a onclick="statuse({{$v->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-check"></i> 下架</button></a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr style="height: 35px">
                                    <td  colspan="11" style="text-align: center">{{$data}}</td>
                                </tr>
                            @else
                            <tr>
                                <th colspan="12">暂时还没有数据</th>
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
                location.href="{{route('foods.informationdel')}}?id="+id;
                layer.close(index);
            });
        }
        function statuse(e) {
            var id = e;
            layer.alert("是否修改该商品状态？",{icon:3},function (index) {
                location.href="{{route('foods.informationStatus')}}?id="+id;
                layer.close(index);
            });
        }
    </script>

@endsection