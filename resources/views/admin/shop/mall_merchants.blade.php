@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>商户管理</h5>
            </div>
            <div class="ibox-content">

                <form method="post" action="{{route('hotel.merchant')}}" name="form">
                    {{ csrf_field() }}
                    <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                    <input type="text" style="height: 25px;margin-left: 10px;" value="{{$wheres['where']['name']}}" name="name" placeholder="商家名字">
                    <button style="height: 25px;margin-left: 10px;" type="submit">按条件查询</button>

                    <a href="{{url('/admin/shop/mall_merchants?status=0')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            全部</button>
                    </a>
                    <a href="{{url('/admin/shop/mall_merchants?status=1')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            已审核</button>
                    </a>
                    <a href="{{url('/admin/shop/mall_merchants?status=2')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            未审核</button>
                    </a>
                    <a href="{{url('/admin/shop/mall_merchants?status=3')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            已禁用</button>
                    </a>
                    <a href="{{url('/admin/shop/mall_merchants?status=4')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            已启用</button>
                    </a>
                </form>
                <style>
                    th ,td{
                        text-align: center;
                    }
                </style>
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th width="100">商户ID</th>
                        <th>商户名字</th>
                        <th>用户名字</th>
                        <th>商家logo图</th>
                        <th>地址</th>
                        <th>商户类型</th>
                        <th>是否审核</th>
                        <th>状态</th>
                        <th style="width: 150px">申请时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($data) > 0)
                        @foreach($data as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->name}}</td>
                                <th>{{$item->username}}</th>
                                <td><img src="{{$item->logo_img}}" alt="" style="width: 50px;height: 50px;"></td>
                                <td>{{$item->address}}</td>
                                <td>{{$item->merchant_type_id}}</td>
                                <td>@if($item->is_reg==1)
                                        <p style="color: green">已审核</p>
                                    @elseif($item->is_reg==2)
                                        <p style="color: red">已驳回</p>
                                    @else
                                        <p style="color: red">未审核</p>
                                    @endif
                                </td>
                                <td>@if($item->status==1)
                                        <p style="color: green">启用中</p>
                                    @else
                                        <p style="color: red">未启用</p>
                                    @endif
                                </td>
                                <td>{{$item->created_at}}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{route('shop.information')}}?id={{$item->id}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 详情</button></a>
                                        @if($item->is_reg == 1)
                                            @if($item->status==1)
                                                <a onclick="del({{$item->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-ban"></i> 禁用</button></a>
                                            @else
                                                <a onclick="del({{$item->id}})"><button class="btn btn-success btn-xs" type="button"><i class="fa fa-check"></i> 启用</button></a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10">未查询到相关内容</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                {{--{{$data}}--}}
                @if(count($data)>0)
                    {{ $data->appends(['status'=>$status]) }}
                @endif
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <script type="text/javascript">
        function del(e) {
            var id = e;
            layer.alert("是否更新状态？",{icon:3},function (index) {
                location.href="{{route('shop.shopStatus')}}?id="+id;
                layer.close(index);
            });
        }
    </script>
@endsection