@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
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
                        <div class="input-group col-sm-2">
                            <input type="text" name="goods_cate_i" value="">
                            <div id="test13" class="demo-tree-more"></div>
                            <div id="goodsCate" class="demo-tree-more"></div>
                        </div>

                    </div>

                    <script>
                    layui.use(['tree', 'util'],function () {
                        var dataCate = "{{$goodsCate}}";
                        var tree = layui.tree
                            ,layer = layui.layer
                            ,util = layui.util
                            //模拟数据1
                            ,data1 = [{
                                title: '江西'
                                ,id: 1
                                ,children: [{
                                    title: '南昌'
                                    ,id: 1000
                                    ,children: [{
                                        title: '青山湖区'
                                        ,id: 10001
                                    },{
                                        title: '高新区'
                                        ,id: 10002
                                    }]
                                },{
                                    title: '九江'
                                    ,id: 1001
                                },{
                                    title: '赣州'
                                    ,id: 1002
                                }]
                            },{
                                title: '广西'
                                ,id: 2
                                ,children: [{
                                    title: '南宁'
                                    ,id: 2000
                                },{
                                    title: '桂林'
                                    ,id: 2001
                                }]
                            },{
                                title: '陕西'
                                ,id: 3
                                ,children: [{
                                    title: '西安'
                                    ,id: 3000
                                },{
                                    title: '延安'
                                    ,id: 3001
                                }]
                            }]
                        //无连接线风格
                        tree.render({
                            elem: '#goodsCate'
                            ,data : dataCate
                            ,showLine: true  //是否开启连接线
                        });
                    });

                    </script>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">品牌：</label>
                        <div class="input-group col-sm-2">
                            <select name="goods_brand_id" class="form-control form-select">
                                @foreach($goodBrands as $k=>$b)
                                    <option value="{{$b->id}}">{{$b->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品名称：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="name" value="{{old('name')}}" required data-msg-required="请输入商品名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品描述：</label>
                        <div class="input-group col-sm-4">
                            <textarea type="text" rows="5" name="desc" id="desc" placeholder="商品简介" class="form-control" required data-msg-required="请输入跳转链接">{{old('desc')}}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品价格：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="price" value="{{old('price')}}" required data-msg-required="请输入商品价格">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品邮费：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="dilivery" value="{{old('dilivery')}}" required data-msg-required="请输入商品邮费">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">排序：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="sort" value="{{old('sort')}}" required data-msg-required="排序">
                        </div>
                    </div>

                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">图片：</label>
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
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">状态：</label>
                        <div class="input-group col-sm-1">
                            <select class="form-control" name="status">
                                <option value="1" @if(old('status') == 1) selected="selected" @endif>正常</option>
                                <option value="2" @if(old('status') == 0) selected="selected" @endif>未发布</option>
                            </select>
                            @if ($errors->has('status'))
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('status')}}</span>
                            @endif
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
