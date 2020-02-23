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
                <h5>邀请码管理</h5>
            </div>
            <div class="ibox-title">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>

                <input type="text" id="names" required >
                <button class="btn btn-primary " type="button"><i class="fa fa-search" id="pse" >搜索</i></button>
            </div>
            <div class="ibox-content">
                <style>
                    th ,td{
                        text-align: center;
                    }
                </style>
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th width="100">ID</th>
                        <th>用户名称</th>
                        <th>邀请码</th>
                        <th>生成日期</th>
                        <th>电话号码</th>
                        <th>下级数</th>
                    </tr>
                    </thead>
                    @if(count($list) > 0)
                        @foreach($list as $k=>$item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->usernames}}</td>
                            <td>{{$item->invite_code}}</td>
                            <td>{{$item->make_time}}</td>
                            <td>{{$item->phone}}</td>
                            <td>{{$item->collar}}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="11">没有查询到相关数据</td>
                        </tr>
                    @endif
                    <tbody>
                </table>
                 @if(empty($code))
                 {{$list}}
                 @else
                    {{ $list->appends(['code'=>$code]) }}
                @endif
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <script type="text/javascript">
        //搜索
        $("#pse").click(function () {
            var code = $("#names").val();
            location.href="{{route('invite.inlist')}}?code="+code
        })
    </script>
@endsection
