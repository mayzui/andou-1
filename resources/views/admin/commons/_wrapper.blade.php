<!--右侧部分开始-->
@php
    $admin = Auth::guard('admin')->user();
    $id = $admin->id;   //用户id

    $data = DB::table('merchants')->where("user_id",$id) -> first();
    $sid =  $data->id;
@endphp
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
                <dd><a href="">修改密码</a></dd>
                <dd><a href="{{route('merchants.information')}}?id=@php echo $sid; @endphp">修改商户信息</a></dd>
            </dl>
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


<!--右侧部分结束-->