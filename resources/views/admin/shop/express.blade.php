@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>商品管理</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>

                <a href="{{route('shop.createExpress')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                        <i class="fa fa-plus-circle"></i>新增模板</button>
                </a>

                <form method="post" action="{{route('shop.express')}}" name="form">
                    <style>
                        th ,td{
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th width="15">ID</th>
                            <th>名称</th>
                            <th>添加用户</th>
                            <th>发货地址</th>
                            <th>免运费</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->name}}</td>
                                <td>{{$item->merchant->name}}</td>
                                <td>{{$item->ship_address}}</td>
                                <td>{{$item->is_free}}</td>
                                <td>
                                    <a href="{{route('shop.addExpressAttrs',$item->id)}}">
                                        <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i>添加运费详细</button>
                                    </a>
                                    <a href="{{route('shop.updateExpress',$item->id)}}">
                                        <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button>
                                    </a>
                                    <a href="{{route('shop.deleteExpress',$item->id)}}"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
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