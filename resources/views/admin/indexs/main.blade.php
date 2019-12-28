@extends('admin.layouts.layout')

@section('title', '首页')
<link href="{{loadEdition('/admin/css/base.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/layui.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/style.css')}}" rel="stylesheet">
<link rel="stylesheet" type="text/css" media="(max-width:750px)" href="{{loadEdition('/admin/css/wap.css')}}"/>
<div class="member">
    <!-- 网站销售额统计 -->
    <div class="member-user clearfix mr20 mt60">
        <div class="statistics-left fl box-shadow w60 h500">
            <span class="company">网站销售额统计</span>
            <span class="transformX pt">总销售额:15452.55 元</span>
            <iframe id="round-frame" name="mainFrame" frameborder="0" src="{{route('index.census')}}" scrolling="no"></iframe>
        </div>
        <div class="statistics-right fr w37 bg-gray pt0">
            <div class="capital box-shadow ">
                <div class="capital-c">
                    <p class="t">资金统计</p>
                    <p class="money">￥<span>5425552</span></p>
                    <div class="clearfix">
                        <a class="fl"  onclick="clicke();">
                            <span>￥2565545</span>
                            <span>会员总资金</span>
                        </a>
                        <a class="fl"  onclick="clicke();">
                            <span>￥2565545</span>
                            <span>会员总资金</span>
                        </a>
                        <a class="fl" onclick="clicke();">
                            <span>￥2565545</span>
                            <span>会员总资金</span>
                        </a>
                    </div>
                </div>
            </div>
            <!-- 总会员 -->
            <div class="clearfix">
                <div class="z-member fl">
                    <div class="capital-c">
                        <h3>总会员<i class="m"></i></h3>
                        <span class="money">454522</span>
                        <h3>今日新增<i class="add"></i></h3>
                        <span class="money">122</span>
                    </div>
                </div>
                <div class="z-member box-shadow  fr">
                    <div class="capital-c">
                        <h2>系统概况</h2>
                        <p>欢迎：admin（系统管理员）</p>
                        <p>1：上次登录地址：重庆市电信</p>
                        <p>2：更新到2018.01.01</p>
                        <p>3：PHP版本：5.6.30</p>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <!-- 会员统计 start -->
    <div class="member-user clearfix">
        <div class="item box-shadow">
            <div class="p30">
                <div class="tit">
                    <a href="{{route('merchants.index')}}?merchant_type_id=2" class="green"><span class="b">{{ $merchants_num }}</span> 商场商家</a>
                </div>
                <p class="b">今日新增<a href="{{route('merchants.index')}}?merchant_type_id=2">0</a>个商家，总计商家 <a href="{{route('merchants.index')}}?merchant_type_id=2">{{ $merchants_num }}</a> 个</p>
                <p class="b">总商品<a href="{{route('shop.goods')}}">{{ $merchants_goods }}</a>个，今日新增 <a href="{{route('shop.goods')}}">0</a> 个</p>
                <p class="b">总订单<a href="{{route('shop.orders')}}">{{ $merchants_order }}</a>单，今日订单 <a href="{{route('shop.orders')}}"> 0</a> 单</p>
                <p class="b">退款申请<a href="{{route('refund.aftermarket')}}">{{ $merchants_returns }}</a>条，待审核 <a href="{{route('refund.aftermarket')}}">0</a>条</p>
                <p class="b">总点评<a href="{{route('shop.commnets')}}">{{ $merchants_comments }}</a>条</p>
            </div>
            <img src="{{loadEdition('/admin/images/icon9.png')}}" >
        </div>
        <div class="item box-shadow">
            <div class="p30">
                <div class="tit">
                    <a href="{{route('hotel.merchant')}}" class="green"><span class="y">{{ $hotle_num }}</span> 酒店商家</a>
                </div>
                <p class="y">今日新增<a href="{{route('hotel.merchant')}}">0</a>个商家，总计商家 <a href="{{route('hotel.merchant')}}">{{ $hotle_num }}</a> 个</p>
                <p class="y">今日上新<a href="{{route('hotel.merchant')}}">0</a>个，待审核 <a href="{{route('hotel.merchant')}}">0</a> 个</p>
                <p class="y">总订单<a href="{{route('hotel.books')}}">{{ $hotle_order }}</a>单，今日订单 <a href="{{route('hotel.books')}}"> {{ $hotle_order }}</a> 单</p>
                <p class="y">退款申请<a href="{{route('hotel.books')}}">0</a>条，待审核 <a href="{{route('hotel.books')}}">0</a>条</p>
                <p class="y">总点评<a href="{{route('hotel.commnets')}}">{{ $hotle_comments }}</a>条</p>
            </div>
            <img src="{{loadEdition('/admin/images/icon10.png')}}" >
        </div>

        <div class="item box-shadow">
            <div class="p30">
                <div class="tit">
                    <a href="{{route('merchants.index')}}?merchant_type_id=4" class="green"><span class="g">{{ $goods_num }}</span> 饭店商家</a>
                </div>
                <p class="g">今日新增<a href="{{route('merchants.index')}}?merchant_type_id=4">{{ $goods_num }}</a>个商家，总计商家 <a href="{{route('merchants.index')}}?merchant_type_id=4">{{ $goods_num }}</a> 个</p>
                <p class="g">今日上新<a href="{{route('merchants.index')}}?merchant_type_id=4">0</a>个，待审核 <a href="{{route('merchants.index')}}?merchant_type_id=4">0</a> 个</p>
                <p class="g">总订单<a href="{{route('foods.orders')}}">{{ $goods_order }}</a>单，今日订单 <a href="{{route('foods.orders')}}">0</a> 单</p>
                <p class="g">退款申请<a href="{{route('foods.orders')}}">0</a>条，待审核 <a href="{{route('foods.orders')}}">0</a>条</p>
                <p class="g">总点评<a href="{{route('foods.commnets')}}">{{ $goods_commnets }}</a>条</p>
            </div>
            <img src="{{loadEdition('/admin/images/icon11.png')}}" >
        </div>
        <div class="item box-shadow">
            <div class="p30">
                <div class="tit">
                    <a onclick="clicke();" class="green"><span class="v">325852</span> 外卖商家</a>
                </div>
                <p class="v">今日新增<a onclick="clicke();">145</a>个商家，总计商家 <a  onclick="clicke();">15</a> 个</p>
                <p class="v">今日上新<a onclick="clicke();">1585</a>个，待审核 <a onclick="clicke();">56</a> 个</p>
                <p class="v">总订单<a onclick="clicke();">185</a>单，今日订单 <a onclick="clicke();"> 56</a> 单</p>
                <p class="v">退款申请<a onclick="clicke();">145</a>条，待审核 <a onclick="clicke();">15</a>条</p>
                <p class="v">总点评<a onclick="clicke();">145</a>条</p>
            </div>
            <img src="{{loadEdition('/admin/images/icon13.png')}}" >
        </div>
        <div class="item box-shadow">
            <div class="p30">
                <div class="tit">
                    <a onclick="clicke();" class="green"><span class="g1">2635</span> 农家乐</a>
                </div>
                <p class="g1">今日新增<a onclick="clicke();">145</a>个商家，总计商家 <a onclick="clicke();">15</a> 个</p>
                <p class="g1">今日上新<a onclick="clicke();">1585</a>个，待审核 <a onclick="clicke();">56</a> 个</p>
                <p class="g1">总订单<a onclick="clicke();">185</a>单，今日订单 <a onclick="clicke();"> 56</a> 单</p>
                <p class="g1">退款申请<a onclick="clicke();">145</a>条，待审核 <a onclick="clicke();">15</a>条</p>
                <p class="g1">总点评<a onclick="clicke();">145</a>条</p>
            </div>
            <img src="{{loadEdition('/admin/images/icon14.png')}}" >
        </div>
        <div class="item box-shadow">
            <div class="p30">
                <div class="tit">
                    <a onclick="clicke();" class="green"><span class="r">23232</span> 民宿</a>
                </div>
                <p class="r">今日新增<a onclick="clicke();">145</a>个商家，总计商家 <a onclick="clicke();">15</a> 个</p>
                <p class="r">今日上新<a onclick="clicke();">1585</a>个，待审核 <a onclick="clicke();">56</a> 个</p>
                <p class="r">总订单<a onclick="clicke();">185</a>单，今日订单 <a onclick="clicke();"> 56</a> 单</p>
                <p class="r">退款申请<a onclick="clicke();">145</a>条，待审核 <a onclick="clicke();">15</a>条</p>
                <p class="r">总点评<a onclick="clicke();">145</a>条</p>
            </div>
            <img src="{{loadEdition('/admin/images/icon15.png')}}" >
        </div>
        <div class="item box-shadow">
            <div class="p30">
                <div class="tit">
                    <a onclick="clicke();" class="green"><span class="y1">8566</span> 旅游商家</a>
                </div>
                <p class="y1">今日新增<a onclick="clicke();">145</a>个商家，总计商家 <a onclick="clicke();">15</a> 个</p>
                <p class="y1">今日上新<a onclick="clicke();">1585</a>个，待审核 <a onclick="clicke();">56</a> 个</p>
                <p class="y1">总订单<a onclick="clicke();">185</a>单，今日订单 <a onclick="clicke();"> 56</a> 单</p>
                <p class="y1">退款申请<a onclick="clicke();">145</a>条，待审核 <a onclick="clicke();">15</a>条</p>
                <p class="y1">总点评<a onclick="clicke();">145</a>条</p>
            </div>
            <img src="{{loadEdition('/admin/images/icon16.png')}}" >
        </div>
        <div class="item box-shadow">
            <div class="p30">
                <div class="tit">
                    <a onclick="clicke();" class="green"><span class="g2">654</span> 条分类信息</a>
                </div>
                <p class="g2">今日新增<a onclick="clicke();">145</a>个商家，总计商家 <a onclick="clicke();">15</a> 个</p>
                <p class="g2">今日上新<a onclick="clicke();">1585</a>个，待审核 <a onclick="clicke();">56</a> 个</p>
                <p class="g2">总订单<a onclick="clicke();">185</a>单，今日订单 <a onclick="clicke();"> 56</a> 单</p>
                <p class="g2">退款申请<a onclick="clicke();">145</a>条，待审核 <a onclick="clicke();">15</a>条</p>
                <p class="g2">总点评<a onclick="clicke();">145</a>条</p>
            </div>
            <img class="bt" src="{{loadEdition('/admin/images/icon17.png')}}" >
        </div>

    </div>
    <!-- 会员统计 end -->
    <!-- 销售量统计 -->
    <div class="member-user clearfix ">
        <div class="statistics-left fl box-shadow">
            <span class="company">销售量统计</span>
            <div id="container" style="width: 100%;height: 100%;"></div>
            <div class="choose-tiem">
                <div class="time tim">
                    <span>一周</span>
                    <i></i>
                </div>
                <ul class="hidden t">
                    <li>一周</li>
                    <li>一个月</li>
                </ul>
            </div>
        </div>
        <div class="statistics-right fr box-shadow">
            <span class="company">单位:(元)</span>
            <div class="choose-tiem">
                <div class="time times">
                    <span>12月</span>
                    <i></i>
                </div>
                <ul class="hidden t">
                    <li>12月</li>
                    <li>11月</li>
                </ul>
            </div>
            <iframe id="round-frame" name="mainFrame" frameborder="0" src="{{route('index.round')}}" scrolling="no"></iframe>
        </div>
    </div>
    <!-- 销售量统计 end-->

</div>
<script src="{{loadEdition('/js/jquery.min.js')}}"></script>
<script src="{{loadEdition('/admin/js/bootstrap.min.js')}}"></script>
<script src="{{loadEdition('/admin/js/echarts.common.min.js')}}"></script>
<script type="text/javascript">
    function clicke(){
        layer.alert("该模块正在开发中,敬请期待...",{icon:5});
    }
    $(function(){
        var tim = $(".time");
        var timSb = tim.siblings().find('li');
        function xios(s_b){
            if(s_b.hasClass("hidden")){
                s_b.addClass('show').removeClass("hidden");
            }else{
                s_b.addClass("hidden")
            }
        }
        tim.on('click',function(){
            var that = $(this);
            var s_b = that.siblings();
            xios(s_b);
        })
        function chooseTime(that){
            var time = that.text();
            that.parents('.t').addClass("hidden");
            that.parent().siblings().find("span").html(time);
        }
        timSb.click(function(){
            var that = $(this);
            chooseTime(that);
        })
    })

    // 统计数据
    var dom = document.getElementById("container");
    var myChart = echarts.init(dom);
    var app = {};
    option = null;

    setTimeout(function () {
        option = {
            legend: {},
            tooltip: {
                showContent: true,
            },
            color:['#ffa45a', '#4baef9','#14bdac','#7d83fe','#65d4db','#f8a67d','#fec66e'],
            dataset: {
                source: [
                    ['product', '12-06', '212-07', '12-08', '12-09', '12-10', '12-11','12-12'],
                    ['商城', 100,200,400,500,200,1000,2001],
                    ['酒店', 300,1100,500,800,100,550,1856],
                    ['饭店', 200,1200,100,500,892,457,2222],
                    ['外卖', 1001,200,300,580,20,1300,2081],
                    ['农家乐', 30,100,1200,300,1100,1550,156],
                    ['民宿', 2020,120,1000,1500,890,457,222],
                    ['旅游', 200,1200,1000,1500,1892,1457,2522],
                ]
            },
            xAxis: {type: 'category'},
            yAxis: {gridIndex: 0},
            grid: {top: '8%'},
            series: [
                {type: 'line', smooth: true, seriesLayoutBy: 'row'},
                {type: 'line', smooth: true, seriesLayoutBy: 'row'},
                {type: 'line', smooth: true, seriesLayoutBy: 'row'},
                {type: 'line', smooth: true, seriesLayoutBy: 'row'},
                {type: 'line', smooth: true, seriesLayoutBy: 'row'},
                {type: 'line', smooth: true, seriesLayoutBy: 'row'},
                {type: 'line', smooth: true, seriesLayoutBy: 'row'},
                {
                    type: 'pie',
                    id: 'pie',
                    radius: '',
                    center: ['50%', '25%'],
                    label: {
                        formatter: '{b}: {c} ({d}%)'
                    },
                    encode: {
                        itemName: 'product',
                        value: '2019',
                        tooltip: '2019'
                    }
                }
            ]
        };
        myChart.setOption(option);


    });
    if (option && typeof option === "object") {
        myChart.setOption(option, true);
    }
</script>

