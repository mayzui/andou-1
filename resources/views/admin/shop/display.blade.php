@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('css')
    <style>
        .animated{-webkit-animation-fill-mode: none;}
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h3>拼团详情</h3>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="" method="POST">
                   <h5>拼团信息</h5>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <tr>
                    <td>团购编号:</td>
                    <td>{{$res->group_code}}}</td>
                    </tr>
                    <br>
                    <tr>
                    <td>团购状态:</td>
                    @if($res->status == 1)
                    <td>拼团中</td>
                    @elseif($res->status == 2)
                    <td>拼团完成</td>
                    @elseif($res->status == 3)
                    <td>拼团失败(未退款)</td>
                    @elseif($res->status == 4)
                    <td>拼团失败(已退款)</td>
                    @endif
                    </tr>
                    <br>
                    <tr>
                    <td>购买用户:</td>
                    <td>{{$res->name}}</td>
                    </tr>
                    <br>
                    <tr>
                    <td>开团时间:</td>
                    <td>{{$res->begin_time}}</td>
                    </tr>
                    <br>
                    <tr>
                    <td>结束时间:</td>
                    <td>{{$res->finish_time}}</td>
                    </tr>
                    <br>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                        <h5>商品信息</h5>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <tr>
                    <td>商品名称:</td>
                    <td>{{$arr->name}}:</td>
                    <td>{{$arr->desc}}</td>
                    </tr>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <h5>参团信息</h5>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th>用户头像</th>
                            <th>用户昵称</th>
                            <th>参团时间</th>
                            <th>订单号码</th>
                        </tr>
                        </thead>
                        @foreach($data as $k => $item)
                        <tr>
                            <td><img src="{{$item->avator}}" style="height:50px;width:50px;" alt=""></td>
                            <td>{{$item->name}}</td>
                            <td>{{$item->part_time}}</td>
                            <td>{{$item->order_sn}}</td>
                        </tr>
                        @endforeach
                        <tbody>
                    </table>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>                
                </form>
            </div>
        </div>
    </div>
    <div id="functions" style="display: none;">
        @include('admin.rules.fonticon')
    </div>
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
@section('footer-js')
  
@endsection


