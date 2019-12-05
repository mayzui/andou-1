@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>添加配置</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <a href="{{route('config.index')}}"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 配置管理</button></a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{ route('config.store') }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">配置名称：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="key" value="{{old('key')}}" required data-msg-required="请输入配置名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">配置值：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="value" value="{{old('value')}}" required data-msg-required="请输入跳转链接">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">配置描述：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="desc" value="{{old('desc')}}" required data-msg-required="请输入配置描述">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">数据类型：</label>
                        <div class="input-group col-sm-2">
                            <select name="type" class="form-control form-select">
                                    <option value="1">JSON格式</option>
                                    <option value="2">数字</option>
                                    <option value="3">字符串</option>
                            </select>
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
