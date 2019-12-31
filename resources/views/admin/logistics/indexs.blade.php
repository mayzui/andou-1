@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>广告管理</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                {{--<a href="{{route('banner.add')}}" link-url="javascript:void(0)">--}}
                    {{--<button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 新增广告</button>--}}
                {{--</a>--}}
                <form method="post" action="{{route('')}}" name="form">

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
                            <th>用户名称</th>
                            <th>商品名称</th>
                            <th>商品数量</th>
                            <th>运费</th>
                            <th>总金额</th>
                            <th>快递公司id</th>
                            <th>快递单号</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->merchants_name}}</td>
                                <td>{{$item->users_name}}</td>
                                <td>{{$item->goods_name}}</td>
                                <td>{{$item->num}}</td>
                                <td>{{$item->shipping_free}}</td>
                                <td>{{$item->total}}</td>
                                <td>{{$item->express_id}}</td>
                                <td>{{$item->courier_num}}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        @if($item->status == 0)
                                            <a href="{{route('banner.status',['status'=>1,'id'=>$item->id])}}"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-warning"></i> 发布</button></a>
                                        @else
                                            <a href="{{route('banner.status',['status'=>0,'id'=>$item->id])}}"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-warning"></i> 关闭</button></a>
                                        @endif
                                        <a href="{{route('banner.update',$item->id)}}">
                                            <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button>
                                        </a>
                                        <a href="{{route('banner.delete',$item->id)}}"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{$list}}
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
@endsection