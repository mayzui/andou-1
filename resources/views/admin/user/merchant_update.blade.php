@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>修改商户信息</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{ route('user.merchant_update') }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{$data->id or ''}}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商户名字：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="name" value="{{$data->name or ''}}" required data-msg-required="商户名字">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商户logo：</label>
                        <div class="input-group col-sm-2">
                            <input type="file" name="file" class="form-control">
                            <div class="gallery" id="show">
                                @if($data->logo_img)
                                <img class="img" src="/{{$data->logo_img}}">
                                @endif
                            </div>
                        </div>
                    </div>
                    <style type="text/css">
                        .gallery .img-item {
                            margin-right: 10px;
                            position: relative;
                        }
                         
                        .gallery .img-item .delete {
                            position: absolute;
                            display: block;
                            width: 15px;
                            height: 15px;
                            color: #fff;
                            background: rgba(0, 0, 0, 0.7);
                            line-height: 15px;
                            text-align: center;
                            border-radius: 50%;
                            right: 0px;
                            cursor: pointer;
                        }
                         
                        .img {
                            width: 100px;
                            height: 100px;
                            /*margin: 20px;*/
                        }

                        </style>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商户简介：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="desc" value="{{$data->desc or ''}}" required data-msg-required="商户简介">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商户地址：</label>
                        <div class="input-group col-sm-2" style="width: 500px;">
                        
                            <select class="form-control" name="province_id" id="province" style="width: 100px;">
                                @foreach($configure['province'] as $k => $province)
                                    <option value="{{$province->id}}" @if($province->id === $data->province_id) selected="selected" @endif>{{$province->name}}</option>
                                @endforeach
                            </select>
                            
                            <select class="form-control" name="city_id" id="city" style="float: left;margin-left:10px;width: 100px;@if($data->province_id == 0) display:none; @endif">
                                @foreach($configure['city'] as $k => $city)
                                    <option value="{{$city->id}}" @if($city->id === $data->city_id) selected="selected" @endif>{{$city->name}}</option>
                                @endforeach
                            </select>
                            <select class="form-control" name="area_id" id="area" style="float: left;margin-left: 10px;width: 100px;@if($data->city_id == 0) display:none; @endif">
                                @foreach($configure['area'] as $k => $area)
                                    <option value="{{$area->id}}" @if($area->id === $data->area_id) selected="selected" @endif>{{$area->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商户详细地址：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="address" value="{{$data->address or ''}}" required data-msg-required="商户详细地址">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商户类型：</label>
                        <div class="input-group col-sm-2">
                            <select class="form-control" name="merchant_type_id">
                                @foreach($configure['type'] as $k => $type)
                                 <option value="{{$type->id}}" @if($type->id === $data->merchant_type_id) selected="selected" @endif>{{$type->type_name}}</option>
                                @endforeach
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
<script src="{{loadEdition('/admin/js/jquery-1.9.1.js')}}"></script>
<script>
    $("#province").change(function() {
        var province_id=$("#province").val();
        $.get("{{route('user.address')}}",{id:province_id},function(res){
            var html='';
                for (var i = 0; i < res.data.length; i++) {
                         html+='<option value="'+res.data[i].id+'">'+res.data[i].name+'</option>';           
                    }
                
                    $.get("{{route('user.address')}}",{id:res.data[0].id},function(res){
                        var html1='';
                    for (var i = 0; i < res.data.length; i++) {
                             html1+='<option value="'+res.data[i].id+'">'+res.data[i].name+'</option>';           
                        }
                    $("#area").show();
                    $("#area").html(html1);
                    $("#city").show();                                
                    $("#city").html(html);
                    },"json");

        },"json")
    });
    $("#city").change(function() {
        var city_id=$("#city").val();
        $.get("{{route('user.address')}}",{id:city_id},function(res){
            var html='';
                for (var i = 0; i < res.data.length; i++) {
                         html+='<option value="'+res.data[i].id+'">'+res.data[i].name+'</option>';           
                    }
                $("#area").show();                            
                $("#area").html(html);
        },"json")
    });
</script>
@endsection
