
@extends('admin.layouts.layout')

@section('title', '首页')
<link href="{{loadEdition('/admin/css/base.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/layui.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/style.css')}}" rel="stylesheet">
<div class="member">
  <!-- 会员统计 start -->
  <div class="title"><h2>会员统计</h2></div>
  <div class="member-user clearfix">
    <div class="item box-shadow">
      <span class="green">12452</span>
      <p>会员</p>
      <img src="{{loadEdition('/admin/images/icon8.png')}}" >
    </div>
    <div class="item box-shadow icon11">
      <span class="red">12452</span>
      <p>商场商家</p>
      <img src="{{loadEdition('/admin/images/icon11.png')}}" >
    </div>
    <div class="item box-shadow icon9">
      <span class="yollew">12452</span>
      <p>酒店商家</p>
      <img src="{{loadEdition('/admin/images/icon9.png')}}" >
    </div>
    <div class="item box-shadow icon10">
      <span class="blue">12452</span>
      <p>饭店商家</p>
      <img src="{{loadEdition('/admin/images/icon10.png')}}" >
    </div>
  </div>
  <!-- 会员统计 end -->
  <!-- 销售量统计 -->
  <div class="title"><h2>销售量统计</h2></div>
  <div class="member-user clearfix ">
    <div class="statistics-left fl box-shadow">
      <span class="company">单位:(元)</span>
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

    $(function(){
        var tim = $(".tim");
        var times = $(".times");
        var timSb = tim.siblings().find('li');
        var timesSb = times.siblings().find('li');

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
        times.on('click',function(){
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
        timesSb.click(function(){
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
            color:['#ffa45a', '#4baef9','#14bdac'],
            dataset: {
                source: [
                    ['product', '12-06', '212-07', '12-08', '12-09', '12-10', '12-11','12-12'],
                    ['商家商城', 100,200,400,500,200,1000,2001],
                    ['酒店商城', 300,1100,500,800,100,550,1856],
                    ['饭店商城', 2000,1200,100,500,892,457,2222],

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
