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
                <input type="text" style="height: 25px;margin-left: 10px;" name="name" placeholder="商家名字">
                <select style="height: 25px;margin-left: 10px;" name="merchant_type_id">
                    <option value="0">商家分类</option>
                    @foreach($type as $k => $item)
                    <option value="{{$item->id}}">{{$item->type_name}}</option>
                    @endforeach
                </select>
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
                            <th>商户名字</th>
                            <th>用户名字</th>
                            <th>商家logo图</th>
                            <th>地址</th>
                            <th>商户类型</th>
                            <th>是否认证</th>
                            <th>创建时间</th>
                            <th>更新时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->name}}</td>
                                <th>{{$item->username}}</th>
                                <td><img src="{{ env('IMAGE_PATH_PREFIX')}}{{$item->logo_img}}" alt="" style="width: 50px;height: 50px;"></td>
                                <td>{{$item->address}}</td>
                                <td>{{$item->merchant_type_id}}</td>
                                <td>@if($item->is_reg==1)
                                        已认证
                                    @else
                                        未认证
                                    @endif
                                </td>
                                <td>{{$item->created_at}}</td>
                                <td>{{$item->updated_at}}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                    @if($item->is_reg==1)
                                        <a href="{{route('merchants.reg')}}?id={{$item->id}}&is_reg=0"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 禁用</button></a>
                                    @else
                                        <a href="{{route('merchants.reg')}}?id={{$item->id}}&is_reg=1"><button class="btn btn-group btn-xs" type="button">启用</button></a>
                                    @endif
                                         
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{$data}}
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
@endsection