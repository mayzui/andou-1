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
            </div>
            <div class="ibox-content">
                <div class="col-sm-3" style="padding-left: 0px; width: 100%;">
                    <div class="input-group">
                    请输入搜索编号<input type="text" placeholder="请输入商品名称" id="names" required >
                <button class="btn btn-primary " type="button"><i class="fa fa-search" id="pse" >搜索</i></button>
                        
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
                            <th>团购编号</th>
                            <th>团购商品</th>
                            <th>团长</th>
                            <th>团购人数</th>
                            <th>还差人数</th>
                            <th>拼团时间</th>
                            <th>拼团状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        @foreach($data as $k => $item)
                            <tr>
                                <td>{{$item->group_code}}</td>
                                <td>{{$item->name}}</td>
                                <td>{{$item->nickname}}</td>
                                <td>{{$item->total_member}}</td>
                                <td>{{$item->total_member - $item->member_num}}</td>
                                <td>
                                {{$item->begin_time}}
                                    <br>
                                {{$item->finish_time}}
                                </td>
                                <td>
                                    @if($item->status == 1)
                                    拼团中
                                    @elseif($item->status == 2)
                                    拼团完成
                                    @elseif($item->status == 3)
                                    拼团失败(未退款)
                                    @elseif($item->status == 4)
                                    拼团失败(已退款)
                                    @endif
                                </td>
                                <td>
                                    @if($item->status == 1)
                                    <a href='{{url("/admin/shop/display?id=$item->id")}}'>
                                        <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i>查看</button>
                                    </a>
                                    @elseif($item->status == 2)
                                    <a href='{{url("/admin/shop/display?id=$item->id")}}'>
                                        <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i>查看</button>
                                    </a>
                                    @elseif($item->status == 3)
                                    <a href='{{url("/admin/shop/display?id=$item->id")}}'>
                                        <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i>查看</button>
                                    </a>
                                    <a href="{{url("")}}">
                                        <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i>退款</button>
                                    </a>
                                    @elseif($item->status == 4)
                                    <a href='{{url("/admin/shop/display?id=$item->id")}}'>
                                        <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i>查看</button>
                                    </a>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                        <tbody>
                    </table>

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
                location.href="{{route('shop.show')}}?keyword="+keyword +"&sta="+"1"+ "&uname="+keyword+"&pho="+keyword;
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