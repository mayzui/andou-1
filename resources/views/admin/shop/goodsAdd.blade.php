@extends('admin.layouts.layout')
<script src="{{loadEdition('/js/jquery.min.js')}}"></script>
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
<script  src="{{loadEdition('/admin/plugins/webupload/webuploader.min.js')}}"></script>

<style>
    .dropDown{display:inline-block}.dropDown_A{display:inline-block}
    .dropDown-menu{ display:none;transition: all 0.3s ease 0s}
    .dropDown:focus,.dropDown-menu:focus {outline:0}
    .dropDown-menu li.arrow{ position:absolute;display:block; width:12px; height:8px; margin-top:-13px; margin-left:20%; line-height:0;background:url(../images/icon-jt.png) no-repeat 0 0}

    /*鼠标经过	*/
    .dropDown.hover.dropDown_A,.dropDown.open.dropDown_A{text-decoration:none;background-color:rgba(255,255,255,0.2)}
    .dropDown.open.dropDown_A.menu_dropdown-arrow{transition-duration:0.3s ;transition-property:all;_background-position:0 0}
    .dropDown.open.dropDown_A.menu_dropdown-arrow{transform: rotate(180deg)}
    .menu{background-color:#fff;border:solid 1px #f2f2f2; display: inline-block}
    .menu.radius{border-top-left-radius:0;border-top-right-radius:0}
    .menu.box-shadow{border-top:none}
    .menu > li{ position: relative; float: none;display:block}
    .menu > li > a{ display: block;clear: both;border-bottom:solid 1px #f2f2f2;padding:6px 20px;text-align:left;line-height:1.5;font-weight: normal;white-space:nowrap}
    .menu > li:last-child > a{ border-bottom:none}
    .menu > li > a:hover,.menu > li > a:focus,.menu > li.open > a{ text-decoration:none;background-color:#fafafa}
    .menu > li > a.arrow{ position:absolute; top:50%; margin-top:-10px; right:5px;line-height: 20px; height: 20px; color: #999}
    .menu > li >.menu{ display: none}
    .menu > li.open >.menu{ display: inline-block;position: absolute; left:100%;top:-1px;min-width:100%}
    /*禁用菜单*/
    .menu > li.disabled > a{color:#999;text-decoration:none; cursor:no-drop; background-color:transparent}
    /*线条*/
    .menu > li.divider{ display:block;height:0px; line-height:0px;margin:9px 0;overflow:hidden; border-top:solid 1px #eee}
    /*打开菜单*/
    .dropDown >.dropDown-menu{ display: none}
    .dropDown.open{position:relative;z-index:990}
    /*默认左对齐*/
    .dropDown.open >.dropDown-menu{position:absolute;z-index:1000;display:inline-block;top:100%;left:-1px;min-width:100%;background-color:#fff;border:solid 1px #f2f2f2}
    /*右对齐*/
    .dropDown.open.right >.dropDown-menu{right:-1px!important;left:auto!important}
</style>
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>添加商品</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <a href="{{route('shop.goods')}}"><button class="btn btn-primary btn-sm" type="button">商品管理</button></a>
                <a href="{{route('shop.addAttr')}}"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i>添加商品属性</button></a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{ route('shop.store') }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}


                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品分类：</label>
                        <div class="input-group col-sm-2" style="width: 500px;">
                            <select class="form-control" name="goods_cate_level_1" id="goods_cate_level_1" style="width: 100px;">
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
                        <label class="col-sm-2 control-label">：</label>
                        <div class="input-group col-sm-2">

{{--                            <ul class="dropDown-menu menu radius box-shadow" >--}}
{{--                                @foreach($goodsCate as $v)--}}
{{--                                <li><a href="#">{{$v->name}}<i class="arrow Hui-iconfont"></i></a>--}}
{{--                                    @if ($v->children)--}}
{{--                                    <ul class="menu" >--}}
{{--                                        @foreach($v->children as $vv)--}}
{{--                                        <li><a href="javascript:;">{{$vv->name}}<i class="arrow Hui-iconfont"></i></a>--}}
{{--                                            @if ($vv->children)--}}
{{--                                            <ul class="menu"  >--}}
{{--                                                @foreach($vv->children as $vvv)--}}
{{--                                                    <li><a href="javascript:;">{{$vvv->name}}</a></li>--}}
{{--                                                @endforeach--}}
{{--                                            </ul>--}}
{{--                                            @endif--}}
{{--                                        </li>--}}
{{--                                        @endforeach--}}
{{--                                    </ul>--}}
{{--                                    @endif--}}
{{--                                </li>--}}
{{--                              @endforeach--}}
{{--                            </ul>--}}
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品品牌：</label>
                        <div class="input-group col-sm-2">
                            <select name="goods_brand_id" class="form-control form-select">
                                @foreach($goodBrands as $k=>$b)
                                    <option value="{{$b->id}}">{{$b->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品名称：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="name" value="{{old('name')}}" required data-msg-required="请输入商品名称">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品描述：</label>
                        <div class="input-group col-sm-4">
                            <textarea type="text" rows="5" name="desc" id="desc" placeholder="商品简介" class="form-control" required data-msg-required="请输入跳转链接">{{old('desc')}}</textarea>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品价格：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="price" value="{{old('price')}}" required data-msg-required="请输入商品价格" >
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品邮费：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="dilivery" value="{{old('dilivery')}}" required data-msg-required="请输入商品邮费">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">热卖：</label>
                        <div class="input-group col-sm-2">
                            <div class="radio i-checks">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name='is_hot' value="1" checked="checked"/>开启&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name='is_hot' value="0" />关闭
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">推荐：</label>
                        <div class="input-group col-sm-2">
                            <div class="radio i-checks">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name='is_recommend' value="1" checked="checked"/>开启&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name='is_recommend' value="0" />关闭
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">特价：</label>
                        <div class="input-group col-sm-2">
                            <div class="radio i-checks">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name='is_bargain' value="1" checked="checked"/>开启&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name='is_bargain' value="0" />关闭
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">封面图片：</label>
                        <div class="layui-upload">
                            <input type="hidden" name="img" id="img" value=""/>
                            <button type="button" class="layui-btn" id="image" style="display: none;">上传图片</button>
                            <div class="layui-upload-list">
                                <img class="layui-upload-img" id="showImage">
                                <p id="demoText"></p>
                            </div>
                        </div>
                        <script>
                            layui.use('upload', function(){
                                var $ = layui.jquery
                                    ,upload = layui.upload;
                                // 图片上传
                                var uploadInst = upload.render({
                                    elem: '#image'
                                    ,url: '/admin/upload/uploadImage'
                                    ,headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                                    ,before: function(obj){
                                        // 预读本地文件示例
                                        obj.preview(function(index, file, result){
                                            $('#showImage').attr('src', result); //图片链接（base64）
                                        });
                                    }
                                    ,done: function(res){
                                        //如果上传失败
                                        if(res.code != 200){
                                            return layer.msg('上传失败');

                                        }
                                        $('#img').val(res.path);
                                    }
                                    ,error: function(){
                                        //演示失败状态，并实现重传
                                        var demoText = $('#demoText');
                                        demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
                                        demoText.find('.demo-reload').on('click', function(){
                                            uploadInst.upload();
                                        });
                                    }
                                });
                            });
                        </script>
                    </div>
                    <div class="hr-line-dashed"></div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label">相册：</label>
                        <div class="input-group col-sm-2">
                            <input type="hidden" id="data_photo" name="photo" >
                            <div id="fileList" class="uploader-list" style="float:right"></div>
                            <div id="imgPicker" style="float:left">选择图片</div>
                            <img id="img_data" height="100px" style="float:left;margin-left: 50px;margin-top: -10px;" src="/admin/images/no_img.jpg"/>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <script type="text/javascript">
                        var $list = $('#fileList');
                        //上传图片,初始化WebUploader
                        var uploader = WebUploader.create({
                            auto: true
                            ,swf: "{{loadEdition('/admin/plugins/webupload/Uploader.swf')}}"
                            ,url: '/admin/upload/uploadImage'
                            ,headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                            ,duplicate :true,// 重复上传图片，true为可重复false为不可重复
                            pick: '#imgPicker',// 选择文件的按钮。可选。
                            accept: {
                                title: 'Images',
                                extensions: 'gif,jpg,jpeg,bmp,png',
                                mimeTypes: 'image/jpg,image/jpeg,image/png'
                            },
                            'onUploadSuccess': function(file, data, response) {
                                $("#data_photo").val(data._raw);
                                $("#img_data").attr('src', '/uploads/images/' + data._raw).show();
                            }
                        });

                        uploader.on( 'fileQueued', function( file ) {
                            $list.html( '<div id="' + file.id + '" class="item">' +
                                '<h4 class="info">' + file.name + '</h4>' +
                                '<p class="state">正在上传...</p>' +
                                '</div>' );
                        });

                        // 文件上传成功
                        uploader.on( 'uploadSuccess', function( file ) {
                            $( '#'+file.id ).find('p.state').text('上传成功！');
                        });

                        // 文件上传失败，显示上传出错。
                        uploader.on( 'uploadError', function( file ) {
                            $( '#'+file.id ).find('p.state').text('上传出错!');
                        });

                    </script>



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
