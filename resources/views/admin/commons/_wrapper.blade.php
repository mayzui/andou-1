<!--右侧部分开始-->
@php
    $admin = Auth::guard('admin')->user();
    $id = $admin->id;   //用户id

    $data = DB::table('merchants')->where("user_id",$id) -> first();
    $sid =  $data->id;
@endphp
<link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link href="{{loadEdition('/admin/css/base.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/layui.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/style.css')}}" rel="stylesheet">
<div class="ad-right">
    <div class="sign layui-nav">
        <a href="{{route('admin.logout')}}" class="a">
            <i class="sign-out"></i>
            <span>退出</span>
        </a>
        <div class="a layui-nav-item">
            <img src="{{$admin->avator}}" />
            <a href="javascript:;">{{$admin->name}}</a>
            <!-- <span>超级管理员</span> -->
            <i class=""></i>
            <dl class="layui-nav-child">
                <dd class="dd" data-toggle="modal" data-target="#myModal"><a href="#">修改密码</a></dd>
                <dd><a href="{{route('merchants.information')}}?id=@php echo $sid; @endphp">修改商户信息</a></dd>
            </dl>
        </div>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
           <form action="">
            <div class="modal-content">
             
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        模态框（Modal）标题
                    </h4>
                </div>
                <div class="modal-body">
                    输入修改的密码: <input type="password" name="password" class="input-group password">
                </div>
                <div class="modal-body">
                    请确认密码: <input type="password" name="pwd"  class="input-group pwd">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                    </button>
                    <button type="button" class="btn btn-primary" id="brs">
                        提交更改
                    </button>
                 </div>
              </div>
            </form>
        </div>
    </div>

    <script src="{{loadEdition('/admin/plugins/layui/layui.js')}}"></script>
    <script>
        layui.use('element', function(){
            var element = layui.element; //导航的hover效果、二级菜单等功能，需要依赖element模块

            //监听导航点击
            element.on('nav(demo)', function(elem){
                //console.log(elem)
                // layer.msg(elem.text());
            });
        });
    </script>

    <div class="ad-wrapper  J_mainContent" id="content-main">
        <iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="{{route('index.main')}}" frameborder="0" data-id="index_v1.html" seamless></iframe>
    </div>
</div>

<script src="{{loadEdition('/js/jquery.min.js')}}"></script>
<script type="text/javascript">
      $("#brs").click(function () {
          var password = $(".password").val()
          var pwd = $(".pwd").val()
         if (password != pwd){
             alert("您输入的两次密码不一致")
         }
          $.ajax({
              url:'{{url('admin/admins/updpwd')}}',
              type:"post",
              data:{
                  '_token':'{{csrf_token()}}',
                  'password':password
              },
              success:function (e) {
                 var code = JSON.parse(e)
                  if(code.code == 0){
                      alert(code.msg);
                      location.href='logout'
                  }
                  if(code.code ==1){
                      alert(code.msg);
                      location.href='idnex'
                  }
              }
          })
      })
</script>


<!--右侧部分结束-->