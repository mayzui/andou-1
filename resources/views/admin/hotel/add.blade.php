@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>添加房间信息</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{ route('hotel.add') }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data" id="form">
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
                            <input type="checkbox" @if(in_array($item->id,$data->desc)) checked="checked" @endif style="margin-left: 3px;" value="{{$item->id}}" name="desc[]">&nbsp{{$item->name}}
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
                        <label class="col-sm-2 control-label">图片：</label>
                        <div class="layui-upload">
                            <input type="file" id="file" name="content_url"  multiple="multiple" style="display:none">
                            <button type="button"  class="layui-btn" id="upload">上传图片</button>
                            <div class="gallery" id="gallery"></div>
                            <div class="gallery" id="show">
                                @foreach($data->img as $k => $v)
                                <img class="img" src="/{{$v}}">
                                @endforeach
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
                            <input type="radio" @if($data->has_window == 1) checked="checked" @endif name="has_window" value="1">有窗
                            <input type="radio" @if($data->has_window == 0) checked="checked" @endif style="margin-left: 3px;" name="has_window" value="0">无窗
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">WIFI：</label>
                        <div class="input-group col-sm-2">
                            <input type="radio" @if($data->wifi == 1) checked="checked" @endif name="wifi" value="1">有
                            <input type="radio" @if($data->wifi == 0) checked="checked" @endif style="margin-left: 3px;" name="wifi" value="0">无
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">可住人数：</label>
                        <div class="input-group col-sm-2">
                            <input type="number" class="form-control" name="num_people" value="{{$data->num_people or ''}}" required data-msg-required="可住人数">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">早餐提供：</label>
                        <div class="input-group col-sm-2">
                            <input type="radio" @if($data->has_breakfast == 1) checked="checked" @endif name="has_breakfast" value="1">有
                            <input type="radio" @if($data->has_breakfast == 0) checked="checked" @endif style="margin-left: 3px;" name="has_breakfast" value="0">无
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
                            <a href="javascript:;" class="btn btn-primary" id="save"><i class="fa fa-check"></i>&nbsp;保 存</a>　<button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
<script src="{{loadEdition('/admin/js/jquery-1.9.1.js')}}"></script>
<script>
    var files = new Array();
    var id = 0;
        //选择图片按钮隐藏input[file]
        $("#upload").click(function() {
            // alert(1);
            $("#file").trigger("click");
        });
        $('#save').click(function () {
                // var status = $(this).attr('data-id')
                var uploadFile = new FormData($("#form")[0]);
                            //遍历图片数组把图片添加到FormData中
                // if (files.length<1) {
                //     alert('请至少上传一张图片');
                //     return;
                // }
                if (files.length>6) {
                    alert('最多上传6张图片');
                    return;
                }
                for (var i = 0; i < files.length; i++) {
                        uploadFile.append("imgs[]", files[i]);
                                // alert(files[i].name);
                }
                $.ajax({
                        url:'{{route('hotel.add') }}',
                        type:'POST',
                        data:uploadFile,
                        dataType: 'json',
                        async: false,
                        cache: false,
                        contentType: false, //不设置内容类型
                        processData: false, //不处理数据
                        success:function(res){
                             if (res.code==200) {
                                setTimeout(function () {
                                    window.location.href = '{{route('hotel.index') }}'
                                }, 1000)
                             }
                            layer.msg(res.msg, {time: 1000})
                        }
                })  
        })
        //选择图片
        $("#file").change(function() {
            $("#show").hide();
            var img = document.getElementById("file").files;
            //遍历
            for (var i = 0; i < img.length; i++) {
                //得到图片
                var file = img[i];
                //把图片存到数组中
                files[id] = file;
                id++;
                //获取图片路径
                var url = URL.createObjectURL(file);
                //创建img
                var box = document.createElement("img");
                box.setAttribute("src", url);
                box.className = 'img';
                //创建div
                var imgBox = document.createElement("div");
                imgBox.style.display = 'inline-block';
                imgBox.className = 'img-item';
                //创建span
                var deleteIcon = document.createElement("span");
                deleteIcon.className = 'delete';
                deleteIcon.innerText = 'x';
                //把图片名绑定到data里面
                deleteIcon.id = img[i].name;
                //把img和span加入到div中
                imgBox.appendChild(deleteIcon);
                imgBox.appendChild(box);
                //获取id=gallery的div
                var body = document.getElementsByClassName("gallery")[0];
                body.appendChild(imgBox);
                //点击span事件
                $(deleteIcon).click(function() {
                    //获取data中的图片名
                    var filename = $(this).attr('id');
                    // alert(filename);
                    //删除父节点
                    $(this).parent().remove();
                    var fileList = Array.from(files);
                    // console.log(fileList);
                    //遍历数组
                    for (var j = 0; j < fileList.length; j++) {
                        //通过图片名判断图片在数组中的位置然后删除
                        // alert(fileList[j].name+"111");
                        if (fileList[j].name == filename) {
                            fileList.splice(j, 1);
                            id--;
                            break;
                        }
                    }
                    files = fileList;
                });
                // console.log(files);
            }
        });
</script>
@endsection