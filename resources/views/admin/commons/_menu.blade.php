<!--左侧导航开始-->
<link href="{{loadEdition('/admin/css/base.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/layui.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/style.css')}}" rel="stylesheet">
<link rel="stylesheet" type="text/css" media="(max-width:750px)" href="{{loadEdition('/admin/css/wap.css')}}"/>
@php
    $admin = Auth::guard('admin')->user();
@endphp
        <div class="andou_left navbar-default">
            <div class="logo"><img class="transform" src="{{loadEdition('/admin/images/logo.png')}}" ></div>
            <div class="ad-user">
                {{--<img src="{{$admin->avator}}" >--}}
                {{--<p>{{$admin->name}}</p>--}}
            </div>

            <ul class="layui-nav layui-nav-tree layui-inline" lay-filter="demo" id="side-menu">
            @foreach(Auth::guard('admin')->user()->getMenus() as $key => $rule)
                @if($rule['route'] == 'index.index')
                    {{--跳转后台首页--}}
                    <li class="layui-nav-item layui-nav-itemed" >
                        <i class="icon10"></i>
                        <a title="{{$rule['name']}}" href="{{route($rule['route'])}}" target="_blank">
                            {{$rule['name']}}
                        </a>
                    <li>
                @else
                    {{--下拉列表--}}
                    <li class="layui-nav-item">
                        <i class="icon{{$key}}"></i>
                        {{--<a title="{{$rule['name']}}">--}}
                            {{--<span class="nav-label">{{$rule['name']}}</span>--}}
                            {{--<span class="layui-nav-more"></span>--}}
                        {{--</a>--}}
                        <a href="javascript:;">{{$rule['name']}}</a>
                        @if(isset($rule['children']))
                            <ul class="nav nav-second-level collapse">
                                @foreach($rule['children'] as $k=>$item)
                                    <li>
                                        <a class="J_menuItem" href="{{ route($item['route']) }}" data-index="index_v1.html">
                                            {{$item['name']}}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endif
                <li>
            @endforeach
            </ul>
        </div>
<!--左侧导航结束-->