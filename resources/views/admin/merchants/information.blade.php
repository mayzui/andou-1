@extends('admin.layouts.layout')
@include('vendor.ueditor.assets')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
<link rel="stylesheet" href="{{loadEdition('/assets/plugins/bootstrap/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{loadEdition('/assets/css/font-awesome.min.css')}}">
<link rel="stylesheet" href="{{loadEdition('/assets/css/animate.css')}}">
<link rel="stylesheet" href="{{loadEdition('/assets/css/main.css')}}">

<script src="https://cdn.bootcss.com/webuploader/0.1.1/webuploader.js"></script>
<script src="{{loadEdition('/assets/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
<script src="{{loadEdition('/assets/plugins/waypoints/waypoints.min.js')}}"></script>
<script src="{{loadEdition('/assets/js/application.js')}}"></script>
<script src="{{loadEdition('/assets/plugins/wizard/js/loader.min.js')}}"></script>
<script src="{{loadEdition('/assets/plugins/wizard/js/jquery.form.js')}}"></script>
<script src="{{loadEdition('/assets/js/modernizr-2.6.2.min.js')}}"></script>
<script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/layer/2.3/layer.js"></script>
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>商户详情</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{ route('merchants.information') }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{$data->id or ''}}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商户类型：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="type_name" value="{{$data->type_name or ''}}" readonly required data-msg-required="商户类型">
                        </div>
                    </div>
                    @if($data -> merchant_type_id == 4)
                        <div class="form-group">
                            <label class="col-sm-2 control-label">经营品种：</label>
                            <div class="input-group col-sm-2">
                                <input type="text" class="form-control" name="management_type" value="{{$data->management_type or ''}}" required data-msg-required="经营品种">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">饭店分类：</label>
                            <select style="height: 25px;width: 273px;" name="cate_id" id="cate_id">
                                @if($data->cate_id == "")
                                    <option value="0" >——暂无饭店分类——</option>
                                @endif
                                @if(count($hotel_category_data) > 0)
                                    @foreach($hotel_category_data as $v)
                                        @if($v->id == $data->cate_id)
                                            <option value="{{ $v->id }}" selected >{{ $v->name }}</option>
                                        @else
                                            <option value="{{ $v->id }}" >{{ $v->name }}</option>
                                        @endif
                                    @endforeach
                                @else
                                    <option value="0" >——暂无饭店分类——</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">营业时间开始：</label>
                            <div class="input-group col-sm-2">
                                <input type="time" class="form-control" name="business_start" value="{{$data->business_start or ''}}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">营业时间结束：</label>
                            <div class="input-group col-sm-2">
                                <input type="time" class="form-control" name="business_end" value="{{$data->business_end or ''}}" required>
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商户名称：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="name" value="{{$data->name or ''}}" required data-msg-required="商户名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">联系人：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="user_name" value="{{$data->user_name or ''}}" required data-msg-required="联系人">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">联系电话：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="tel" value="{{$data->tel or ''}}" required data-msg-required="联系电话">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">店铺地址：省</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="province_id" value="{{$data->province_id or ''}}" required data-msg-required="店铺地址：省">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">店铺地址：市</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="city_id" value="{{$data->city_id or ''}}" required data-msg-required="店铺地址：市">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">店铺地址：区</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="area_id" value="{{$data->area_id or ''}}" required data-msg-required="店铺地址：区">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">详细地址：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="address" value="{{$data->address or ''}}" required data-msg-required="详细地址">
                        </div>
                    </div>
                    @if($data -> merchant_type_id == 2)
                    <div class="form-group">
                        <label class="col-sm-2 control-label">退货地址：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="return_address" value="{{$data->return_address or ''}}" required data-msg-required="退货地址">
                        </div>
                    </div>
                    @endif
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商家简介：</label>
                        <div class="input-group col-sm-2"  style="width: 500px">
                            <script id="container" name="desc" type="text/plain">{!!$data->desc or ''!!}</script>
                        </div>
                    </div>
                    <script type="text/javascript">
                        var ue = UE.getEditor('container',{
                            initialFrameWidth:null ,//宽度随浏览器自适应
                            wordCount: false, //关闭字数统计
                            elementPathEnabled : false,//隐藏元素路径
                            autoHeightEnabled: false,//是否自动长高
                            autoFloatEnabled: false//是否保持toolbar的位置不动
                        });
                        ue.ready(function() {
                            ue.setHeight(250);
                            ue.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
                        });
                    </script>
                    {{--商家海报图--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商家海报图：</label>
                        <div class="input-group col-sm-2">
                            <div class="layui-upload">
                                <input type="hidden" name="banner_img" id="banner_img" value="{{ $data->banner_img or '' }}"/>
                                <button type="button" class="layui-btn" id="bannerimage">上传图片</button>
                                <div class="layui-upload-list">
                                        <img class="layui-upload-img" id="bannerShowImage" src="{{ $data->banner_img or '' }}" style="width: 60%;height: 30%">
                                    <p id="bannerdemoText"></p>
                                </div>
                            </div>
                        </div>
                        {{--图片上传js--}}
                        <script>
                            layui.use('upload', function(){
                                var $ = layui.jquery
                                    ,upload = layui.upload;
                                // 图片上传
                                var uploadInst = upload.render({
                                    elem: '#bannerimage'
                                    ,url: '/admin/upload/uploadImage'
                                    ,headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                                    ,before: function(obj){
                                        // 预读本地文件示例
                                        obj.preview(function(index, file, result){
                                            $('#bannerShowImage').attr('src', result); //图片链接（base64）
                                        });
                                    }
                                    ,done: function(res){
                                        //如果上传失败
                                        if(res.code != 200){
                                            return layer.msg('上传失败');

                                        }
                                        $('#banner_img').val(res.path);
                                    }
                                    ,error: function(){
                                        //演示失败状态，并实现重传
                                        var demoText = $('#bannerdemoText');
                                        demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
                                        demoText.find('.demo-reload').on('click', function(){
                                            uploadInst.upload();
                                        });
                                    }
                                });
                            });
                        </script>
                    </div>
                    {{--商家Logo图--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商家Logo图：</label>
                        <div class="input-group col-sm-2">
                            <div class="layui-upload">
                                <input type="hidden" name="logo_img" id="logo_img" value="{{ $data->logo_img or '' }}"/>
                                <button type="button" class="layui-btn" id="logoimage">上传图片</button>
                                <div class="layui-upload-list">
                                        <img class="layui-upload-img" id="logoShowImage" src="{{ $data->logo_img or '' }}" style="width: 200px;height: 200">
                                    <p id="logodemoText"></p>
                                </div>
                            </div>
                        </div>
                        {{--图片上传js--}}
                        <script>
                            layui.use('upload', function(){
                                var $ = layui.jquery
                                    ,upload = layui.upload;
                                // 图片上传
                                var uploadInst = upload.render({
                                    elem: '#logoimage'
                                    ,url: '/admin/upload/uploadImage'
                                    ,headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                                    ,before: function(obj){
                                        // 预读本地文件示例
                                        obj.preview(function(index, file, result){
                                            $('#logoShowImage').attr('src', result); //图片链接（base64）
                                        });
                                    }
                                    ,done: function(res){
                                        //如果上传失败
                                        if(res.code != 200){
                                            return layer.msg('上传失败');

                                        }
                                        $('#logo_img').val(res.path);
                                    }
                                    ,error: function(){
                                        //演示失败状态，并实现重传
                                        var demoText = $('#logodemoText');
                                        demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
                                        demoText.find('.demo-reload').on('click', function(){
                                            uploadInst.upload();
                                        });
                                    }
                                });
                            });
                        </script>
                    </div>
                    {{--商家门头图--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商家门头图：</label>
                        <div class="input-group col-sm-2">
                            <div class="layui-upload">
                                <input type="hidden" name="door_img" id="door_img" value="{{ $data->door_img or '' }}"/>
                                <button type="button" class="layui-btn" id="doorimage">上传图片</button>
                                <div class="layui-upload-list">
                                        <img class="layui-upload-img" id="doorShowImage" src="{{ $data->door_img or '' }}" style="width: 200px;height: 200px">
                                    <p id="doordemoText"></p>
                                </div>
                            </div>
                        </div>
                        {{--图片上传js--}}
                        <script>
                            layui.use('upload', function(){
                                var $ = layui.jquery
                                    ,upload = layui.upload;
                                // 图片上传
                                var uploadInst = upload.render({
                                    elem: '#doorimage'
                                    ,url: '/admin/upload/uploadImage'
                                    ,headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                                    ,before: function(obj){
                                        // 预读本地文件示例
                                        obj.preview(function(index, file, result){
                                            $('#doorShowImage').attr('src', result); //图片链接（base64）
                                        });
                                    }
                                    ,done: function(res){
                                        //如果上传失败
                                        if(res.code != 200){
                                            return layer.msg('上传失败');

                                        }
                                        $('#door_img').val(res.path);
                                    }
                                    ,error: function(){
                                        //演示失败状态，并实现重传
                                        var demoText = $('#doordemoText');
                                        demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
                                        demoText.find('.demo-reload').on('click', function(){
                                            uploadInst.upload();
                                        });
                                    }
                                });
                            });
                        </script>
                    </div>
                    {{--营业执照--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">营业执照：</label>
                        <div class="input-group col-sm-2">
                            <div class="layui-upload">
                                <input type="hidden" name="management_img" id="management_img" value="{{ $data->management_img or '' }}"/>
                                <button type="button" class="layui-btn" id="managementimage">上传图片</button>
                                <div class="layui-upload-list">
                                        <img class="layui-upload-img" id="managementShowImage" src="{{ $data->management_img or '' }}" style="width: 200px;height: 200px">
                                    <p id="managementdemoText"></p>
                                </div>
                            </div>
                        </div>
                        {{--图片上传js--}}
                        <script>
                            layui.use('upload', function(){
                                var $ = layui.jquery
                                    ,upload = layui.upload;
                                // 图片上传
                                var uploadInst = upload.render({
                                    elem: '#managementimage'
                                    ,url: '/admin/upload/uploadImage'
                                    ,headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                                    ,before: function(obj){
                                        // 预读本地文件示例
                                        obj.preview(function(index, file, result){
                                            $('#managementShowImage').attr('src', result); //图片链接（base64）
                                        });
                                    }
                                    ,done: function(res){
                                        //如果上传失败
                                        if(res.code != 200){
                                            return layer.msg('上传失败');

                                        }
                                        $('#management_img').val(res.path);
                                    }
                                    ,error: function(){
                                        //演示失败状态，并实现重传
                                        var demoText = $('#managementdemoText');
                                        demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
                                        demoText.find('.demo-reload').on('click', function(){
                                            uploadInst.upload();
                                        });
                                    }
                                });
                            });
                        </script>
                    </div>
                    {{--食品经营许可证--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">食品经营许可证：</label>
                        <div class="input-group col-sm-2">
                            <div class="layui-upload">
                                <input type="hidden" name="goods_img" id="goods_img" value="{{ $data->goods_img or '' }}"/>
                                <button type="button" class="layui-btn" id="goodsimage">上传图片</button>
                                <div class="layui-upload-list">
                                        <img class="layui-upload-img" id="goodsShowImage" src="{{ $data->goods_img or '' }}" style="width: 200px;height: 200px">
                                    <p id="goodsdemoText"></p>
                                </div>
                            </div>
                        </div>
                        {{--图片上传js--}}
                        <script>
                            layui.use('upload', function(){
                                var $ = layui.jquery
                                    ,upload = layui.upload;
                                // 图片上传
                                var uploadInst = upload.render({
                                    elem: '#goodsimage'
                                    ,url: '/admin/upload/uploadImage'
                                    ,headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                                    ,before: function(obj){
                                        // 预读本地文件示例
                                        obj.preview(function(index, file, result){
                                            $('#goodsShowImage').attr('src', result); //图片链接（base64）
                                        });
                                    }
                                    ,done: function(res){
                                        //如果上传失败
                                        if(res.code != 200){
                                            return layer.msg('上传失败');

                                        }
                                        $('#goods_img').val(res.path);
                                    }
                                    ,error: function(){
                                        //演示失败状态，并实现重传
                                        var demoText = $('#goodsdemoText');
                                        demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
                                        demoText.find('.demo-reload').on('click', function(){
                                            uploadInst.upload();
                                        });
                                    }
                                });
                            });
                        </script>
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
