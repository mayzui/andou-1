@extends('admin.layouts.layout')
<style>
    th ,td{
        text-align: center;
        font-size: 13px;
    }
</style>

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>拼团管理</h5>
            </div>
            <div class="ibox-title">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <a href="{{url("/admin/shop/show?status=70")}}">
                            <button class="btn btn-primary " type="button"><i class="fa fa-paste">全部</i></button>
                        </a>
                        <a href="{{url("/admin/shop/show?status=1")}}">
                            <button class="btn btn-primary " type="button"><i class="fa fa-paste">拼团中</i></button>
                        </a>

                        <a href="{{url("/admin/shop/show?status=2")}}">
                            <button class="btn btn-primary " type="button"><i class="fa fa-paste">拼团成功</i></button>
                        </a>

                        <a href="{{url("/admin/shop/show?status=3")}}">
                            <button class="btn btn-primary " type="button"><i class="fa fa-paste">拼团失败(待退款)</i></button>
                        </a>
                        <a href="{{url("/admin/shop/show?status=4")}}">
                            <button class="btn btn-primary " type="button"><i class="fa fa-paste">拼团失败(已退款)</i></button>
                        </a>
            </div>
            <div class="ibox-content">
                <div class="col-sm-3" style="padding-left: 0px; width: 100%;">
                    <div class="input-group">
                        请输入搜索编号<input type="text" placeholder="请输入商品名称" id="names" required >
                <button class="btn btn-primary " type="button"><i class="fa fa-search" id="pse" >搜索</i></button>
                         <a href="{{route('shop.addPuzzle')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                                <i class="fa fa-plus-circle"></i> 新增团购</button>
                        </a>
                    </div>
                </div>
                <style>
                    th ,td{
                        text-align: center;
                    }
                </style>
                <div class="hr-line-dashed"></div>
                <form method="post" action="{{route('shop.index')}}" name="form">
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th>商品名称</th>
                            <th>商品图</th>
                            <th>库存数量</th>
                            <th>上架时间</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        @foreach($data as $k => $item)
                            <tr>
                                <td>{{$item->name}}</td>
                                <td><img src="{{$item->img}}" alt="" style="height:50px;width:50px;"></td>
                                <td>{{$item->storage}}</td>
                                <td>{{$item->created_at}}</td>
                                <td>
                                 @if($item->code == 0)
                                    @if($item->finish_time <= $times)
                                    已结束
                                    @elseif($item->begin_time <= $times)
                                    进行中
                                    @else
                                    未开始
                                    @endif
                                @else
                                未开始
                                @endif
                                </td>
                                <td>
                                @if($item->finish_time <= $times)
                                    <a href='{{url("/admin/shop/puzzleUpd?id=$item->id")}}'>
                                        <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i>修改</button>
                                    </a>
                                    @elseif($item->begin_time <= $times)
                                    <a href='{{url("/admin/shop/show?id=$item->id")}}'>
                                        <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i>查看</button>
                                    </a>
                                    @endif
                                
                                @if($item->code == 0)
                                <a href='{{url("/admin/shop/status?id=$item->id")}}'>
                                    <button class="btn btn-primary " type="button"><i class="fa fa-paste">下架</i></button>
                                </a>
                                @else
                                <a href='{{url("/admin/shop/status?id=$item->id")}}'>
                                    <button class="btn btn-primary " type="button"><i class="fa fa-paste">上架</i></button>
                                </a>
                                @endif

                                </td>
                            </tr>
                        @endforeach
                        <tbody>
                    </table>
                        {{$data}}
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <script type="text/javascript">
         //搜索订单编号
        function search() {
            var keyword = $("#sval").val()
            if (event.keyCode==13){
                // keyword 订单编号  uname 用户名  pho手机号
                location.href="{{route('shop.orders')}}?keyword="+keyword +"&sta="+"1"+ "&uname="+keyword+"&pho="+keyword;
            }
        }

        function time() {

            if (event.keyCode==13){
                var time = $(".time").val()
                location.href="{{route('shop.orders')}}?time="+time;
            }
        }
        //搜索

       $("#pse").click(function () {
            var search = $("#names").val();
            location.href="{{route('shop.show')}}?name="+search
        })


    </script>
@endsection