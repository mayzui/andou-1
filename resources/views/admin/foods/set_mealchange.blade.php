@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>新增套餐</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>

                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{route('foods.add')}}" method="post" accept-charset="UTF-8">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{ $data->id or '' }}" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label">套餐名称：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="name" value="{{$data->name or ''}}" required placeholder="请输入套餐名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">套餐图片：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="image" value="{{$data->image or ''}}" required placeholder="请输入套餐图片">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">套餐价格：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="price" value="{{$data->price or ''}}" required placeholder="请输入套餐价格">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">几人餐：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="num" value="{{$data->num or ''}}" required placeholder="请输入几人餐">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">有无包间：</label>
                        <input type="checkbox" class="form-control" name="room" value="{{$data->room or ''}}" >
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">包间价格：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="room_price" value="{{$data->room_price or ''}}" required placeholder="请输入包间价格">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <div class="col-sm-12 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>　<button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>

@endsection
