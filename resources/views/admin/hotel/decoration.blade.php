@extends('admin.layouts.layout')
@include('vendor.ueditor.assets')
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
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
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
                <h5>环境设施</h5>
            </div>
            <div class="ibox-content">
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal" action="{{ route('shop.store') }}" method="post" id='addGoods' accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                <div class="form-group">
                    <label class="col-sm-2 control-label"><em style="margin-right:5px;vertical-align: middle;color: #fe0000;">*</em>详情图片：</label>
                    <div class="col-sm-6 add_div">
                        <p>
                            <input type="file" name="choose-file[]" id="choose-file" multiple="multiple"/>
                        </p>

                        @if(!empty($list[0]->facilities))
                        <p>

                            <ul class="file-list image_ul " style="display: block;">
                                @foreach($list as$k=> $v)
                                    <li style="border:1px gray solid; margin:5px 5px;" class="file-item">
                                        <input type="hidden" name="choose_file[]" value="" />
                                        <img src="" alt="" height="70">
{{--                                        <span class="btn btn-danger file-del">删除</span>--}}
                                    </li>
                                @endforeach
                            </ul>
                              @else
                            <ul class="file-list image_ul " style="display: block;"></ul>
                            </p>
                        @endif
                    </div>
                </div>
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

                //限制单次批量上传的数量
                var num = e.target.files.length;
                var numall = numold + num;
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

            {{--$("#imageSub").on('click',function () {--}}
            {{--        var form = document.getElementById("imageSubForm");--}}
            {{--        var formData = new FormData(form);--}}
            {{--        for (var i = 0, len = fileList.length; i < len; i++) {--}}
            {{--            //console.log(fileList[i]);--}}
            {{--            formData.append('choose-file[]',fileList[i]);--}}
            {{--        }--}}
            {{--        $.ajax({--}}
            {{--            url: "{{ route('shop.storeAlbum') }}",--}}
            {{--            type: 'post',--}}
            {{--            data: formData,--}}
            {{--            dataType: 'json',--}}
            {{--            processData: false,--}}
            {{--            contentType: false,--}}
            {{--            success: function (data) {--}}
            {{--                if(data == 1){--}}
            {{--                    layer.alert("图片上传成功,即将跳转下一步...",{icon:1},function (index) {--}}
            {{--                        $("#next").click();--}}
            {{--                        layer.close(index);--}}
            {{--                    });--}}
            {{--                }else{--}}
            {{--                    layer.alert(data,{icon:2});--}}
            {{--                }--}}
            {{--            },--}}
            {{--            error:function (e) {--}}

            {{--                layer.alert(e.responseText,{icon:2});--}}
            {{--            }--}}
            {{--        })--}}

            {{--})--}}

        })

    </script>
@endsection
