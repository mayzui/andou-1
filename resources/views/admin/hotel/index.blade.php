@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>管理员管理</h5>
            </div>
            <div class="ibox-content">
                
                
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>&nbsp;
                <a href="{{route('hotel.add')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 新增房间</button></a>
                    <style>
                        th ,td{
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th width="100">ID</th>
                            <th>房间名称</th>
                            <th>所属商户</th>
                            <th>所属用户</th>
                            <th>房间价格</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->house_name}}</td>
                                <td>{{$item->merchant_id}}</td>
                                <td>{{$item->user_id}}</td>
                                <td>{{$item->price}}</td>
                                <td>@if($item->status==1)
                                        上架中
                                    @else
                                        下架中
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                    <a href="{{route('hotel.add')}}?id={{$item->id}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>
                                        
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