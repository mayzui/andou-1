@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-warning alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
              非专业技术人员请勿修改、增加、删除等操作。
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>分类列表</h5>
            </div>
            <div class="ibox-content">
                <a href="{{route('shop.cateAdd')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加分类</button></a>
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th>分类名称</th>
                        <th>缩略片</th>
                        <th class="text-center" width="100">排序</th>
                        <th class="text-center" width="250">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($list as $k=>$item)
                        <tr>
                            <td>{{$item['_name']}}</td>
                            <td><img src="{{env('IMAGE_PATH_PREFIX')}}{{$item['img']}}" alt="" style="max-height: 50px;max-width: 100px"></td>
                            <td class="text-center">{{$item['sort']}}</td>
                            <td class="text-center">
                                <a href="{{route('shop.cateEdit',$item['id'])}}">
                                    <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button>
                                </a>
                                <a href="{{route('shop.cateDelete',$item['id'])}}"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    </div>
    </div>
@endsection