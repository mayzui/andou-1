@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>新增菜品详情</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" id="sub" action="{{route('foods.informationadd')}}" method="post" accept-charset="UTF-8">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{ $data->id or '' }}" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label">菜品分类：</label>
                        <select style="height: 25px;width: 273px;" name="classification_id" id="classification_id">
                            <option value="0" >——请选择菜品分类——</option>
                            @if(count($type) > 0)
                                @foreach($type as $v)
                                    @if($v->id == $data->classification_id)
                                        <option value="{{ $v->id }}" selected >{{ $v->name }}</option>
                                        @else
                                        <option value="{{ $v->id }}" >{{ $v->name }}</option>
                                    @endif
                                @endforeach
                                @else
                                <option value="0" >——请选择菜品分类——</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">菜品名称：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="name" id="name" value="{{$data->name or ''}}" required placeholder="请输入菜品名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">菜品价格：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="price" id="price" value="{{$data->price or ''}}" required placeholder="请输入菜品价格">
                        </div>
                    </div>
                    {{--<div class="form-group">--}}
                        {{--<label class="col-sm-2 control-label">菜品规格：</label>--}}
                        {{--<div class="input-group col-sm-2 checkbox">--}}
                            {{--@foreach($spec as $k => $v)--}}
                                {{--<label><input type="checkbox" @if(in_array($v->name,$data->specifications)) checked="checked" @endif name="specifications[]" value="{{ $v->id }}" />{{ $v->name }}</label>--}}
                            {{--@endforeach--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">菜品介绍：</label>
                        <div class="input-group col-sm-2">
                            <textarea cols="36" rows="10" name="remark" id="remark">{{ $data -> remark or '' }}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">菜品图片：</label>
                        <div class="input-group col-sm-2">
                            <div class="layui-upload">
                                <input type="hidden" name="img" id="img" value="{{ $data->image or '' }}"/>
                                <button type="button" class="layui-btn" id="image">上传图片</button>
                                <div class="layui-upload-list">
                                    @if(!empty($data->image))
                                        <img class="layui-upload-img" id="showImage" src="{{ $data->image or '' }}" style="width: 60%;height: 30%">
                                    @else
                                        <img class="layui-upload-img" id="showImage" style="width: 60%;height: 30%">
                                    @endif
                                    <p id="demoText"></p>
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
                    @if(!empty($data -> quantitySold))
                        <div class="form-group">
                            <label class="col-sm-2 control-label">销售数量：</label>
                            <div class="input-group col-sm-2">
                                <input type="text" class="form-control" name="quantitySold" id="price" value="{{$data->quantitySold or ''}}" required placeholder="销售数量">
                            </div>
                        </div>
                        @else

                    @endif
                    @if(!empty($data -> num))
                        <div class="form-group">
                            <label class="col-sm-2 control-label">点赞数量：</label>
                            <div class="input-group col-sm-2">
                                <input type="text" class="form-control" name="num" id="price" value="{{$data->num or ''}}" required placeholder="点赞数量">
                            </div>
                        </div>
                        @else

                    @endif

                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <div class="col-sm-12 col-sm-offset-2">
                            <button class="btn btn-primary" onclick="save();" type="button"><i class="fa fa-check"></i>&nbsp;保 存</button>　<button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function save() {
            // 获取下拉框中的数据
            var classification_id = document.getElementById("classification_id");
            var classification = classification_id.selectedIndex;
            // 获取菜品名称
            var name = document.getElementById('name').value;
            // 获取菜品价格
            var price = document.getElementById('price').value;
            // 获取多选框中的数据
            // var obj = document.getElementsByName('specifications[]');
            // var s = "";
            // for (var i = 0; i < obj.length; i++) {
            //     if (obj[i].checked) s += obj[i].value + ',';
            // }
            // 获取文本域中的值
            var remark = document.getElementById('remark').value;
            // 判断
            if(classification == "" || classification == null){
                layer.alert("请选择菜品分类",{icon:7})
            }else if(!name.trim()){
                layer.alert("菜品名称不能为空",{icon:7})
            }else if(!price.trim()){
                layer.alert("菜品价格不能为空",{icon:7})
            }else if(!remark.trim()){
                layer.alert("菜品介绍不能为空",{icon:7})
            }else{
                var form = document.getElementById('sub');
                form.submit();
            }
        }
    </script>
@endsection
