<!--右侧部分开始-->
@php
    $admin = Auth::guard('admin')->user();
    $sid =  Auth::id();
    $data = DB::table("merchants") -> where('user_id',$sid) -> first();

@endphp
<link href="{{loadEdition('/admin/css/base.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/layui.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/style.css')}}" rel="stylesheet">
<div class="content-right">
    <div class="sign layui-nav">
        <div style="display: none;" id="voice_play">
            <audio id="play">
                <source src="{{loadEdition('/admin/yinxiao1323.mp3')}}" type="audio/mp3"/>
                <source src="{{loadEdition('/admin/yinxiao1323.mp3')}}" type="audio/mpeg"/>
            </audio>
        </div>
        <a href="{{route('admin.logout')}}" class="a">
            <i class="sign-out"></i>
            <span>退出</span>
        </a>
        <a href="{{route('admin.redis')}}" class="a">
            <span>刷新缓存</span>
        </a>
        <div class="a layui-nav-item">
            <img src="{{$admin->avator}}" />
            <a href="javascript:;">{{$admin->name}}</a>
            <!-- <span>超级管理员</span> -->
            <i class=""></i>
            <dl class="layui-nav-child">
                <dd><a href="" data-toggle="modal" data-target="#myModal">修改密码</a></dd>
                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                    &times;
                                </button>
                                <h4 class="modal-title" id="myModalLabel">
                                    修改密码
                                </h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">账号：</label>
                                    <div class="input-group col-sm-6">
                                        <input type="text" class="form-control" name="mobile" id="mobile" value="{{ $admin -> mobile }}" required readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">旧密码：</label>
                                    <div class="input-group col-sm-6">
                                        <input type="password" class="form-control" name="old_pwd" id="old_pwd" value="" required placeholder="请输入旧密码">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">新密码：</label>
                                    <div class="input-group col-sm-6">
                                        <input type="password" class="form-control" name="new_pwd" id="new_pwd" value="" required placeholder="请输入新密码">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">确认密码：</label>
                                    <div class="input-group col-sm-6">
                                        <input type="password" class="form-control" name="con_pwd" id="con_pwd" value="" required placeholder="请输入确认密码">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                                </button>
                                <button type="button" class="btn btn-primary" onclick="save();">提交更改</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div>
                </div>
            </dl>
        </div>
    </div>



    <script src="{{loadEdition('/admin/plugins/layui/layui.js')}}"></script>
    <script>
        function save(){
            layer.alert("是否修改当前密码？",{icon:3},function (index) {
                var id = "{{ Auth::id() }}";
                var old_pwd = document.getElementById('old_pwd').value;
                var new_pwd = document.getElementById('new_pwd').value;
                var con_pwd = document.getElementById('con_pwd').value;
                if(!new_pwd.trim()){
                    layer.alert("新密码不能为空",{icon:2})
                }else{
                    $.post("{{ route('index.updataPwd') }}",{id:id,old_pwd:old_pwd,new_pwd:new_pwd,con_pwd:con_pwd,_token:"{{ csrf_token() }}"},function (data) {
                        if(data == 1){
                            layer.alert("密码修改成功",{icon: 1},function (index) {
                                location.href="{{ route('admin.logout') }}";
                                layer.close(index);
                            })
                        }else{
                            layer.alert(data,{icon:2})
                        }
                    });
                }

                layer.close(index);
            });
        }
        layui.use('element', function(){
            var element = layui.element; //导航的hover效果、二级菜单等功能，需要依赖element模块

            //监听导航点击
            element.on('nav(demo)', function(elem){

            });
        });

    </script>
    <div class="content-wrapper  J_mainContent" id="content-main">
        <iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="{{route('index.main')}}" frameborder="0" data-id="index_v1.html" seamless></iframe>
    </div>
</div>
<script type="text/javascript">
    setInterval("voice_play()",3000);
    function voice_play(){
        $.ajax({
            type:'post',
            url:'{{route('indexs.voice_play')}}',
            timeout:3000,
            data : {_token:'{{ csrf_token() }}'},
            async : true,
            dataType:'json',
            success : function (data) {
                var leng = data.length;
                var num = 0;
                for (var i = 0;i<leng;i++){
                    var num = num+data[i].length;
                }
                // 判断是否有新订单生成
                // console.log(num);
                if(num != 0){
                    // 如果有新订单，则提醒
                    var audio  = document.getElementById('play');
                    audio.play();
                }
            }
        });
    }
</script>



<!--右侧部分结束-->
