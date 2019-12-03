@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>管理员管理</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <a href="{{route('banner.add')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 新增广告</button></a>
                <form method="post" action="{{route('banner.index')}}" name="form">
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th class="text-center" width="100">ID</th>
                            <th>描述</th>
                            <th>地址</th>
                            <th>图片</th>
                            <th>排序</th>
                            <th>广告位置</th>
                            <th class="text-center">创建时间</th>
                            <th class="text-center">更新时间</th>
                            <th class="text-center">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $k => $item)
                            <tr>
                                <td class="text-center">{{$item->id}}</td>
                                <td>{{$item->desc}}</td>
                                <td>{{$item->url}}</td>
                                <td><img src="{{$item->img}}" alt="" style="width: 50px;height: 50px;"></td>
                                <td class="text-center">{{$item->created_at}}</td>
                                <td class="text-center">{{$item->updated_at}}</td>
                                <td class="text-center">
                                    @if($item->status == 1)
                                        <span class="text-navy">正常</span>
                                    @elseif($item->status == 2)
                                        <span class="text-danger">锁定</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->allow_in == 1)
                                        <a href="{{route('admins.allow',['allow_in'=>0,'id'=>$item->id])}}"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-warning"></i> 可登录</button></a>
                                    @elseif($item->allow_in == 0)
                                        <a href="{{route('admins.allow',['allow_in'=>1,'id'=>$item->id])}}"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-warning"></i>禁止登录</button></a>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{route('admins.edit',$item->id)}}">
                                            <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button>
                                        </a>
                                        @if($item->status == 2)
                                            <a href="{{route('admins.status',['status'=>1,'id'=>$item->id])}}"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-warning"></i> 恢复</button></a>
                                        @else
                                            <a href="{{route('admins.status',['status'=>2,'id'=>$item->id])}}"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-warning"></i> 禁用</button></a>
                                        @endif
                                        <a href="{{route('admins.delete',$item->id)}}"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{$admins->links()}}
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
@endsection