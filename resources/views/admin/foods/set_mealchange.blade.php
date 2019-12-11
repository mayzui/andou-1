@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>新增套餐</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>

                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{route('foods.set_mealchange')}}" method="post" accept-charset="UTF-8">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{ $data->id or '' }}" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label">套餐名称：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="name" value="{{$data->name or ''}}" required placeholder="请输入套餐名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">套餐图片：</label>
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
                    <div class="form-group">
                        <label class="col-sm-2 control-label">套餐价格：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="price" value="{{$data->price or ''}}" required placeholder="请输入套餐价格">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">几人餐：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="num" value="{{$data->num or ''}}" required placeholder="请输入几人餐">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">有无包间：</label>
                        <input type="checkbox" class="form-control" id="room" name="room" @if($data->room == 1) checked @endif value="{{$data->room or '0'}}" onclick="checke()" >
                    </div>
                    <div class="form-group hidden" id="room_price">
                        <label class="col-sm-2 control-label">包间价格：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="room_price" value="{{$data->room_price or ''}}" placeholder="请输入包间价格">
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
    <script type="text/javascript">

        @if($data->room == 1) document.getElementById('room_price').classList.remove('hidden'); @endif

        // js 显示或隐藏有无包间
        function checke() {
            var ch = document.getElementsByName('room');
            if(ch[0].checked == true){
                // 如果选中复选框，则显示包间价格
                document.getElementById('room_price').classList.remove('hidden');
                document.getElementById('room').value='1';
            }else{
                // 如果复选框未选中，则隐藏包间价格
                document.getElementById('room_price').classList.add('hidden');
                document.getElementById('room').value='0';
            }
        }

    </script>

@endsection
