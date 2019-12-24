@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>关于我们</h5>
            </div>
            <div class="ibox-content">
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{route('about.indexChange')}}" method="post" accept-charset="UTF-8">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{ $data -> id or '' }}" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label">图片：</label>
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
                        <label class="col-sm-2 control-label">标题：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="title" value="{{$data -> title or ''}}" required placeholder="请输入标题">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">内容：</label>
                        <div class="input-group col-sm-2">
                            <textarea cols="36" rows="10" name="content">{{ $data -> content or '' }}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">版本号：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="value" value="{{$data->value or ''}}" readonly required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">版权信息：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="copyright" value="{{$data->copyright or ''}}" required placeholder="请输入版权信息">
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
