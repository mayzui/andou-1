@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>广告管理</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <a href="{{route('banner.add')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 新增广告</button></a>
                <form method="post" action="{{route('banner.index')}}" name="form">

                    <style>
                        th ,td{
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th width="100">ID</th>
                            <th>描述</th>
                            <th>地址</th>
                            <th>图片</th>
                            <th>排序</th>
                            <th>广告位置</th>
                            <th>发布</th>
                            <th>创建时间</th>
                            <th>更新时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->desc}}</td>
                                <td>{{$item->url}}</td>
                                <td><img src="{{ env('IMAGE_PATH_PREFIX')}}{{$item->img}}" alt="" style="width: 50px;height: 50px;"></td>
                                <td>{{$item->sort}}</td>
                                <td>{{$item->position->name}}</td>
                                <td>
                                    @if ($item->status == 1)
                                        <span class="text-info">发布</span>
                                    @else
                                        <span class="text-danger">未发布</span>
                                    @endif
                                </td>
                                <td>{{$item->created_at}}</td>
                                <td>{{$item->updated_at}}</td>
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