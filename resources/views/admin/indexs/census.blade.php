@extends('admin.layouts.layout')

@section('title', '首页')
<link href="{{loadEdition('/admin/css/base.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/layui.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/style.css')}}" rel="stylesheet">
<body>
	
	<div class="round">
		<div id="container" style="height: 100%"></div>
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
	    tooltip : {
	        trigger: 'axis',
	        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
	            type : 'cross'        // 默认为直线，可选为：'line' | 'shadow'
	        }
	    },
	    grid: {
	        left: '3%',
	        right: '4%',
	        bottom: '3%',
	        containLabel: true
	    },
	    xAxis : [
	        {
	            type : 'category',
	            data : ['会员充值', '商城', '酒店', '饭店', '外卖', '农家乐', '民宿', '旅游'],
	            axisTick: {
	                alignWithLabel: true
	            }
	        }
	    ],
	    yAxis : [
	        {
	            type : 'value'
	        }
	    ],
	    series : [
	        {	
				name:'会员充值',
	            type:'bar',
	            barWidth: '30%',
	            data:[
	                {value:152400, name:'152400'},
	                {value:35465, name:'35465'},
	                {value:92584, name:'92584'},
	            	{value:152400, name:'15240'},
	            	{value:35465, name:'30546'},
	            	{value:92584, name:'52584'},
	            	{value:92584, name:'41584'},
					{value:92584, name:'2552'},
	            ],
				itemStyle: {
					normal: {
　　　　　　　　　　　　//好，这里就是重头戏了，定义一个list，然后根据所以取得不同的值，这样就实现了，
						color: function(params) {
							var colorList = [
								'#ffa45a', '#4baef9','#14bdac','#7d83fe','#65d4db','#f8a67d','#fec66e','#fec66e'
							];
							return colorList[params.dataIndex]
						},
　　　　　　　　　　　　　//以下为是否显示，显示位置和显示格式的设置了
						label: {
							show: true,
							position: 'top',
						}
					}
				},
	        }
	    ],
		
	};
	
	if (option && typeof option === "object") {
	    myChart.setOption(option, true);
	}
</script>

 
</body>
</html>
