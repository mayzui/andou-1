@extends('admin.layouts.layout')
<script src="{{loadEdition('/js/jquery.min.js')}}"></script>
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
<script  src="{{loadEdition('/admin/plugins/webupload/webuploader.min.js')}}"></script>
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
                            <select class="form-control pull-left" id="level1"  onchange="getChildren(this,1)" name="goods_cate_id1" style="width: 100px;" >
                                @foreach($goodsCate as $item)
                                    <option value="{{$item->id}}"> {{$item->name}}</option>
                                @endforeach
                            </select>
                            &nbsp;&nbsp;
                            <select class="form-control col-sm-2pull-left " id="level2"  onchange="getChildren(this,2)" name="goods_cate_id2" style="width: 100px;">
                            </select>
                            &nbsp;&nbsp;
                            <select class="form-control col-sm-2" id="level3" onchange="getChildren(this,3)" style="width: 100px;"  name="goods_cate_id3"></select>
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
                        <label class="col-sm-2 control-label">团购商品：</label>
                        <div class="input-group col-sm-2">
                            <div class="radio i-checks">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name='is_' value="1" checked="checked"/>开启&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name='is_hot' value="0" />关闭
                            </div>
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


                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品属性：</label>
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
                        <label class="col-sm-2 control-label">商品属性：</label>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">特价：</label>
                        <div class="input-group col-sm-2">
                            <div class="radio i-checks">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name='is_bargain' value="1" checked="checked"/>开启&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;
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
                        <div class="input-group">
                            <div id="fileList"></div>
                            <div id="imgPicker" style="float:left" style="display: none">选择图片</div>
                            <div class="form-group" id="img-contener"></div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <script type="text/javascript">
                        var $list = $('#fileList');
                        //上传图片,初始化WebUploader
                        var uploader = WebUploader.create({
                            auto: true,// 选完文件后，是否自动上传。
                            swf: '/static/admin/webupload/Uploader.swf',// swf文件路径
                            server: '/admin/upload/uploadImage',
                            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            duplicate :true,// 重复上传图片，true为可重复false为不可重复
                            pick: '#imgPicker',// 选择文件的按钮。可选。
                            accept: {
                                title: 'Images',
                                extensions: 'gif,jpg,jpeg,bmp,png',
                                mimeTypes: 'image/jpg,image/jpeg,image/png'
                            },
                            'onUploadSuccess': function(file, data, response) {
                                var contener = $('#img-contener').html();
                                contener += '<span>';
                                contener += '<input type="hidden"  name="images[]"  value="" >';
                                contener += '<img  ondblclick="$(this).parent().remove()" height="100px" style="float:left;margin-left: 50px;margin-top: -10px;" src="http://'+data.showUrl+'"/>';
                                contener += '</span>';
                                $('#img-contener').html(contener);
                            }
                        });
                        uploader.on( 'fileQueued', function( file ) {});
                        uploader.on( 'uploadSuccess', function( file ) {});
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

    <script>
        // 加载就执行一下
        $('#level1').change();
        function  getChildren (obj,level) {
            var next = level+1;
            var id = $(obj).val();
            $.ajax({
                url:'/admin/shop/getCateChildren',
                type:'post',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data:{id:id},
                dataType:'JSON',
                success:function (res) {
                    if (res.code == 200) {
                        var html = '';
                        for (i in res.data) {
                            html += '<option value="'+res.data[i].id+'">'+res.data[i].name+'</option>';
                        }
                        $('#level'+ next).html(html);
                    }
                    $('#level'+next).change();
                }

            })
        }
    </script>
@endsection
