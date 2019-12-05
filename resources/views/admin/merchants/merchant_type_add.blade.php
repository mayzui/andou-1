@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>添加商户分类</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{ route('merchants.merchant_type_add') }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">分类名字：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="type_name" value="{{$data->type_name or ''}}" required data-msg-required="分类名字">
                        </div>
                    </div>

                    
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">创建子商户：</label>
                        <div class="input-group col-sm-1">
                            <select class="form-control" name="has_children">
                                <option value="1" @if($data->has_children == 1) selected="selected" @endif>允许</option>
                                <option value="0" @if($data->has_children == 0) selected="selected" @endif>不允许</option>
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
