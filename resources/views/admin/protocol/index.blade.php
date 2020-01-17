@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>协议管理</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <a href="{{route('about.protocolAdd')}}" link-url="javascript:void(0)">
                    <button class="btn btn-primary btn-sm" type="button">
                        <i class="fa fa-plus-circle"></i> 新增协议
                    </button>
                </a>
                {{--<form method="post" action="{{route('config.index')}}" name="form">--}}
                    <style>
                        th ,td{
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th width="100">ID</th>
                            <th>协议名称</th>
                            <th>是否启用</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                                <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->name}}</td>
                                <td>
                                    @if($item->status == 1)
                                       <span class="text-navy">启用</span>
                                    @else
                                        <span style="color: red">禁用</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('about.protocolAdd')}}?id={{$item->id}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>
                                    @if($item->status == 0)
                                        <a href="{{route('about.upd',['status'=>0,'id'=>$item->id])}}"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-warning"></i> 恢复</button></a>
                                    @else
                                        <a href="{{route('about.upd',['status'=>1,'id'=>$item->id])}}"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-warning"></i> 禁用</button></a>
                                    @endif
                                </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
@endsection