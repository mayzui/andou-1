
@extends('admin.layouts.layout')

@section('title', '首页')
<link href="{{loadEdition('/admin/css/base.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/layui.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/style.css')}}" rel="stylesheet">
<div class="round">
    <div id="container" style="height: 100%"></div>
    <div class="round-info">
        <div class="info clearfix clear">
            <span class="icon info1 "></span>
            <span class="fl">商城商家</span>
            <span class="fr font12"><span class="yollew font16">152400</span> 元</span>
        </div>
        <div class="info clearfix clear">
            <span class="icon info2 "></span>
            <span class="fl">酒店商家</span>
            <span class="fr font12"><span class="blue font16">35465</span> 元</span>
        </div>
        <div class="info clearfix clear">
            <span class="icon info3 "></span>
            <span class="fl">饭店商家</span>
            <span class="fr font12"><span class="green font16">92584</span> 元</span>
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
        color:['#ffa45a', '#4baef9','#14bdac'],

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
