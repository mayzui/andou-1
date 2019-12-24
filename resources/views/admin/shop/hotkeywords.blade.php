@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>商户分类管理</h5>
            </div>
            <div class="ibox-content">
                
                
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>&nbsp;
                <a href="{{route('shop.hotkeywordsedit')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 新增公告</button></a>
                    <style>
                        th ,td{
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th width="100">ID</th>
                            <th>搜索词</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->name}}</td>
                                <td>@if($item->status==1)
                                        已发布
                                    @else
                                        未发布
                                    @endif
                                </td>
                                
                                <td class="text-center">
                                    <div class="btn-group">
                                    <a href="{{route('shop.hotkeywordsedit')}}?id={{$item->id}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>
                                    @if($item->status==1)
                                    <a href="{{route('shop.hotkeywordsdel')}}?id={{$item->id}}&status=0"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 取消发布</button></a>
                                    @else
                                    <a href="{{route('shop.hotkeywordsdel')}}?id={{$item->id}}&status=1"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 发布</button></a>
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