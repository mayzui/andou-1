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
                <form class="form-horizontal m-t-md" action="{{ route('hotel.add') }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{$data->id or ''}}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">房间名字：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="house_name" value="{{$data->house_name or ''}}" required data-msg-required="房间名字">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">房间配置：</label>
                        <div class="input-group col-sm-2">
                            @foreach($desc as $k => $item)
                            <input type="checkbox" style="margin-left: 3px;" value="{{$item->id}}" name="desc">&nbsp{{$item->name}}
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">价格：</label>
                        <div class="input-group col-sm-2">
                            <input type="number" class="form-control" name="price" value="{{$data->price or ''}}" required data-msg-required="价格">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">库存：</label>
                        <div class="input-group col-sm-2">
                            <input type="number" class="form-control" name="num" value="{{$data->num or ''}}" required data-msg-required="库存">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">图片上传：</label>
                        <div class="input-group col-sm-2">
                            <input type="number" class="form-control" name="num" value="{{$data->num or ''}}" required data-msg-required="库存">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">面积：</label>
                        <div class="input-group col-sm-2">
                            <input type="number" class="form-control" name="areas" value="{{$data->areas or ''}}" required data-msg-required="面积">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">窗户：</label>
                        <div class="input-group col-sm-2">
                            <input type="radio" name="has_window" value="1">有窗
                            <input type="radio" style="margin-left: 3px;" name="has_window" value="0">无窗
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">WIFI：</label>
                        <div class="input-group col-sm-2">
                            <input type="radio" name="wifi" value="1">有
                            <input type="radio" style="margin-left: 3px;" name="wifi" value="0">无
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">可住人数：</label>
                        <div class="input-group col-sm-2">
                            <input type="number" class="form-control" name="num" value="{{$data->num or ''}}" required data-msg-required="可住人数">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">早餐提供：</label>
                        <div class="input-group col-sm-2">
                            <input type="radio" name="has_breakfast" value="1">有
                            <input type="radio" style="margin-left: 3px;" name="has_breakfast" value="0">无
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">床型：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="bed_type" value="{{$data->bed_type or ''}}" required data-msg-required="床型">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">其它设施：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="other_sets" value="{{$data->other_sets or ''}}" required data-msg-required="其它设施">
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
