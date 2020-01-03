@extends('admin.layouts.layout')
@include('vendor.ueditor.assets')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
<link rel="stylesheet" href="{{loadEdition('/assets/plugins/bootstrap/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{loadEdition('/assets/css/font-awesome.min.css')}}">
<link rel="stylesheet" href="{{loadEdition('/assets/css/animate.css')}}">
<link rel="stylesheet" href="{{loadEdition('/assets/css/main.css')}}">
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">

<script src="{{loadEdition('/assets/js/jquery.min.js')}}"></script>
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
<script src="https://cdn.bootcss.com/webuploader/0.1.1/webuploader.js"></script>
<script src="{{loadEdition('/assets/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
<script src="{{loadEdition('/assets/plugins/waypoints/waypoints.min.js')}}"></script>
<script src="{{loadEdition('/assets/js/application.js')}}"></script>
<script src="{{loadEdition('/assets/plugins/wizard/js/loader.min.js')}}"></script>
<script src="{{loadEdition('/assets/plugins/wizard/js/jquery.form.js')}}"></script>
<script src="{{loadEdition('/assets/js/modernizr-2.6.2.min.js')}}"></script>
<script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/layer/2.3/layer.js"></script>

<style>
    .add_div {
        width: 700px;
        height: 500px;
        border: solid #ccc 1px;
        margin-top: 24px;
        padding-left: 20px;
    }

    .file-list {
        height: 125px;
        display: none;
        list-style-type: none;
    }

    .file-list img {
        width: 150px;
        height: 170px;
        vertical-align: middle;
        font-size: 12px;
    }

    .file-list .file-item {
        width: 150px;
        height: 207px;
        margin-bottom: 10px;
        float: left;
        margin-left: 20px;
    }

    .file-list .file-item .file-del {
        display: block;
        text-align: center;
        margin-top: 5px;
        cursor: pointer;
        font-size: 12px;
    }


</style>
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
                        <label class="col-sm-2 control-label"><em style="margin-right:5px;vertical-align: middle;color: #fe0000;">*</em>房间图片：</label>
                        <div class="col-sm-6 add_div">
                            <p>
                                <input type="file" name="choose-file[]" id="choose-file" multiple="multiple"/>
                            </p>
                            <p>
                            @if(!empty($data->img))
                                <ul class="file-list image_ul " style="display: block;">
                                    @foreach($data->img as $v)
                                        <li style="border:1px gray solid; margin:5px 5px;" class="file-item">
                                            <input type="hidden" name="choose_file[]" value="{{ $v }}" />
                                            <img src="{{ $v }}" alt="" height="70">
                                            <span class="btn btn-danger file-del">删除</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <ul class="file-list image_ul " style="display: block;"></ul>
                                @endif
                                </p>
                        </div>
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
        // //选择图片
        // $("#file").change(function() {
        //     $("#show").hide();
        //     var img = document.getElementById("file").files;
        //     //遍历
        //     for (var i = 0; i < img.length; i++) {
        //         //得到图片
        //         var file = img[i];
        //         //把图片存到数组中
        //         files[id] = file;
        //         id++;
        //         //获取图片路径
        //         var url = URL.createObjectURL(file);
        //         //创建img
        //         var box = document.createElement("img");
        //         box.setAttribute("src", url);
        //         box.className = 'img';
        //         //创建div
        //         var imgBox = document.createElement("div");
        //         imgBox.style.display = 'inline-block';
        //         imgBox.className = 'img-item';
        //         //创建span
        //         var deleteIcon = document.createElement("span");
        //         deleteIcon.className = 'delete';
        //         deleteIcon.innerText = 'x';
        //         //把图片名绑定到data里面
        //         deleteIcon.id = img[i].name;
        //         //把img和span加入到div中
        //         imgBox.appendChild(deleteIcon);
        //         imgBox.appendChild(box);
        //         //获取id=gallery的div
        //         var body = document.getElementsByClassName("gallery")[0];
        //         body.appendChild(imgBox);
        //         //点击span事件
        //         $(deleteIcon).click(function() {
        //             //获取data中的图片名
        //             var filename = $(this).attr('id');
        //             // alert(filename);
        //             //删除父节点
        //             $(this).parent().remove();
        //             var fileList = Array.from(files);
        //             // console.log(fileList);
        //             //遍历数组
        //             for (var j = 0; j < fileList.length; j++) {
        //                 //通过图片名判断图片在数组中的位置然后删除
        //                 // alert(fileList[j].name+"111");
        //                 if (fileList[j].name == filename) {
        //                     fileList.splice(j, 1);
        //                     id--;
        //                     break;
        //                 }
        //             }
        //             files = fileList;
        //         });
        //         // console.log(files);
        //     }
        // });
    </script>

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

        $(function () {
            ////////////////////////////////////////////////图片上传//////////////////////////////////////////////
            //声明变量
            var $button = $('#upload'),
                //选择文件按钮
                $file = $("#choose-file"),
                //回显的列表
                $list = $('.file-list'),
                //选择要上传的所有文件
                fileList = [];
            //当前选择上传的文件
            var curFile;
            $file.on('change', function (e) {
                //上传过图片后再次上传时限值数量
                var numold = $('.image_ul li').length;
                if(numold >= 6){
                    layer.alert('最多上传6张图片');
                    return;
                }
                //限制单次批量上传的数量
                var num = e.target.files.length;
                var numall = numold + num;
                if(num >6 ){
                    layer.alert('最多上传6张图片');
                    return;
                }else if(numall > 6){
                    layer.alert('最多上传6张图片');
                    return;
                }
                //原生的文件对象，相当于$file.get(0).files;//files[0]为第一张图片的信息;
                curFile = this.files;
                //将FileList对象变成数组
                fileList = fileList.concat(Array.from(curFile));
                //console.log(fileList);
                for (var i = 0, len = curFile.length; i < len; i++) {
                    reviewFile(curFile[i])
                }
                $('.file-list').fadeIn(1000);
            })


            function reviewFile(file) {
                //实例化fileReader,
                var fd = new FileReader();
                //获取当前选择文件的类型
                var fileType = file.type;
                //调它的readAsDataURL并把原生File对象传给它，
                fd.readAsDataURL(file);//base64
                //监听它的onload事件，load完读取的结果就在它的result属性里了
                fd.onload = function () {
                    if (/^image\/[jpeg|png|jpg|gif]/.test(fileType)) {
                        $list.append('<li style="border:1px gray solid; margin:5px 5px;" class="file-item"><img src="' + this.result + '" alt="" height="70"><span class="btn btn-danger file-del">删除</span></li>').children(':last').hide().fadeIn(1000);
                    } else {
                        $list.append('<li class="file-item"><span class="file-name">' + file.name + '</span><span class="btn btn-danger file-del" style="font-size: 12px;">删除</span></li>')
                    }

                }
            }

            //点击删除按钮事件：
            $(".file-list").on('click', '.file-del', function () {
                let $parent = $(this).parent();
                console.log($parent);
                let index = $parent.index();
                fileList.splice(index, 1);
                $parent.fadeOut(850, function () {
                    $parent.remove()
                });
                //$parent.remove()
            });

            $("#imageSub").on('click',function () {

                if(fileList.length > 6){
                    layer.alert('最多允许上传6张图片');
                    return;
                } else {
                    var form = document.getElementById("imageSubForm");
                    var formData = new FormData(form);
                    for (var i = 0, len = fileList.length; i < len; i++) {
                        //console.log(fileList[i]);
                        formData.append('choose-file[]',fileList[i]);
                    }
                    $.ajax({
                        url: "{{ route('shop.storeAlbum') }}",
                        type: 'post',
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            if(data == 1){
                                layer.alert("图片上传成功,即将跳转下一步...",{icon:1},function (index) {
                                    $("#next").click();
                                    layer.close(index);
                                });
                            }else{
                                layer.alert(data,{icon:2});
                            }
                        },
                        error:function (e) {

                            layer.alert(e.responseText,{icon:2});
                        }
                    })
                }
            })

        })

    </script>
@endsection