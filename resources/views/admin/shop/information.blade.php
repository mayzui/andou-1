@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
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
                    @if($data -> merchant_type_id == 4)
                        <div class="form-group">
                            <label class="col-sm-2 control-label">经营品种：</label>
                            <div class="input-group col-sm-2">
                                <input type="text" class="form-control" name="management_type" value="{{$data->management_type or ''}}" required data-msg-required="经营品种">
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商户类型：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="type_name" value="{{$data->type_name or ''}}" readonly required data-msg-required="商户类型">
                        </div>
                    </div>
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
                        <div class="input-group col-sm-2">
                            <textarea cols="35" rows="9" name="desc">{{ $data -> desc or '' }}</textarea>
                        </div>
                    </div>
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
                                        <img class="layui-upload-img" id="logoShowImage" src="{{ $data->logo_img or '' }}" style="width: 60%;height: 30%">
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
                                        <img class="layui-upload-img" id="doorShowImage" src="{{ $data->door_img or '' }}" style="width: 60%;height: 30%">
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
                                        <img class="layui-upload-img" id="managementShowImage" src="{{ $data->management_img or '' }}" style="width: 60%;height: 30%">
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
                                        <img class="layui-upload-img" id="goodsShowImage" src="{{ $data->goods_img or '' }}" style="width: 60%;height: 30%">
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
                            @if($data -> is_reg == 0)
                                <button class="btn btn-primary" type="button" onclick="adopt({{ $data -> id }});"><i class="fa fa-check"></i>&nbsp;审核通过</button>　
                                <button class="btn btn-danger" type="button" onclick="reject({{ $data -> id }});"><i class="fa fa-repeat"></i>&nbsp;驳回请求</button>
                            @endif
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function adopt(id) {
            layer.alert("是否通过当前审核?",{icon:3},function (index) {
                $.post("{{route('shop.information')}}",{id:id,is_reg:1,_token:'{{csrf_token()}}'},function (data) {
                    if(data == 1){
                        layer.alert('已通过该商家的申请',{icon:1},function (index) {
                            location.href="{{ route('shop.shopMerchant') }}";
                            layer.close(index);
                        })
                    }else{
                        layer.alert(data,{icon:7});
                    }
                });
                layer.close(index);
            });
        }
        function reject(id) {
            layer.alert("是否驳回该商家的审核?",{icon:3},function (index) {
                $.post("{{route('shop.information')}}",{id:id,is_reg:1,_token:'{{csrf_token()}}'},function (data) {
                    if(data == 1){
                        layer.alert('已驳回该商家的申请',{icon:1},function (index) {
                            location.href="{{ route('shop.shopMerchant') }}";
                            layer.close(index);
                        })
                    }else{
                        layer.alert(data,{icon:7});
                    }
                });
                layer.close(index);
            })
        }
    </script>

@endsection
