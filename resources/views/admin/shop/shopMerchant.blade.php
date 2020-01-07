@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>商户管理</h5>
            </div>
            <div class="ibox-content">
                
                <form method="post" action="{{route('merchants.index')}}" name="form">
                {{ csrf_field() }}
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                {{--<input type="text" style="height: 25px;margin-left: 10px;" value="{{$wheres['where']['name']}}" name="name" placeholder="商家名字">--}}
                {{--<select style="height: 25px;margin-left: 10px;" name="merchant_type_id">--}}
                    {{--<option value="0">商家分类</option>--}}
                    {{--@foreach($wheres['type'] as $k => $item)--}}
                    {{--<option value="{{$item->id}}" @if($wheres['where']['merchant_type_id'] == $item->id) selected="selected" @endif>{{$item->type_name}}</option>--}}
                    {{--@endforeach--}}
                {{--</select>--}}
                {{--<button style="height: 25px;margin-left: 10px;" type="submit">按条件查询</button>--}}
                </form>
                    <style>
                        th ,td{
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md" >
                        <thead>
                        <tr>
                            <th width="100">ID</th>
                            <th>商户名字</th>
                            <th>用户名字</th>
                            <th>商家logo图</th>
                            <th>地址</th>
                            <th>商户类型</th>
                            <th>是否认证</th>
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
                                        <p style="color: green">已认证</p>
                                    @elseif($item->is_reg==2)
                                        <p style="color: red">已驳回</p>
                                    @else
                                        <p style="color: red">未认证</p>
                                    @endif
                                </td>
                                <td>{{$item->created_at}}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{route('shop.information')}}?id={{$item->id}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 详情</button></a>
                                        @if($item->is_reg==1)
                                            <a href="{{route('shop.shopMerchantOrder')}}?id={{$item->id}}"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-check"></i> 查询订单</button></a>
                                            <a href="{{route('shop.shopMerchantMoney')}}?id={{$item->id}}"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-money"></i> 资金流水</button></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                            @else
                            <tr>
                                <td colspan="9">未查询到相关内容</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    {{$data}}
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
@endsection