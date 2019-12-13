@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>修改运费模板</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <a href="{{route('shop.express')}}"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 运费管理</button></a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{ route('shop.storeExpress') }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{$data->id}}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">运费名称：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="name" value="{{$data->name}}" required data-msg-required="运费名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">发货地址：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="ship_address" value="{{$data->ship_address}}" required data-msg-required="发货地址">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">包邮：</label>
                        <div class="input-group col-sm-2">
                            <div class="radio i-checks">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name='is_free' value="1" @if ($data->is_free == 1) checked="checked" @endif />开启&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name='is_free' value="0" @if ($data->is_free == 0) checked="checked" @endif/>关闭
                            </div>
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
