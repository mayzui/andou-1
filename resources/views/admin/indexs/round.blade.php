@extends('admin.layouts.layout')

@section('title', '首页')
<link href="{{loadEdition('/admin/css/base.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/layui.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/style.css')}}" rel="stylesheet">
<div class="round">
    <div id="container" style="height: 100%"></div>
    <div class="round-info clearfix">
        <div class="info fl">
            <span class="icon info1 "></span>
            <span class="">商城</span>
        </div>
        <div class="info fl">
            <span class="icon info2 "></span>
            <span class="">酒店</span>
        </div>
        <div class="info fl">
            <span class="icon info3 "></span>
            <span class="">饭店</span>
        </div>
        <div class="info fl">
            <span class="icon info4 "></span>
            <span class="">外卖</span>
        </div>
        <div class="info fl">
            <span class="icon info5 "></span>
            <span class="">农家乐</span>
        </div>
        <div class="info fl">
            <span class="icon info6 "></span>
            <span class="">民宿</span>
        </div>
        <div class="info fl">
            <span class="icon info7 "></span>
            <span class="">旅游</span>
        </div>
    </div>
</div>
<script src="{{loadEdition('/js/jquery.min.js')}}"></script>
<script src="{{loadEdition('/admin/js/bootstrap.min.js')}}"></script>
<script src="{{loadEdition('/admin/js/echarts.common.min.js')}}"></script>
<script type="text/javascript">
    var dom = document.getElementById("container");
    var myChart = echarts.init(dom);
    var app = {};
    option = null;
    option = {
        title : {
            text: '',
            subtext: '',
            x:'center'
        },
        color:['#ffa45a', '#4baef9','#14bdac','#7d83fe','#65d4db','#f8a67d','#fec66e'],

        tooltip : {
            trigger: 'item',
            showContent: false,
            formatter: "{b} : {c} ({d}%)"
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: ['', '', '']
        },
        series : [
            {
                name: '',
                type: 'pie',
                radius : ['30%', '50%'],
                center: ['50%', '30%'],
                data:[
                    {value:152400, name:'152400'},
                    {value:35465, name:'35465'},
                    {value:92584, name:'92584'},
                    {value:152400, name:'15240'},
                    {value:35465, name:'30546'},
                    {value:92584, name:'52584'},
                    {value:92584, name:'41584'},
                ],
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ]
    };
    ;
    if (option && typeof option === "object") {
        myChart.setOption(option, true);
    }
</script>
