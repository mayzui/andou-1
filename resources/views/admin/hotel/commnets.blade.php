@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>评论管理</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>

                {{--<a href="{{route('hotel.commnetsAdd')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">--}}
                        {{--<i class="fa fa-plus-circle"></i>新增评论</button>--}}
                {{--</a>--}}

                <form method="post" action="{{route('shop.express')}}" name="form">
                    <style>
                        th ,td{
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th>评论ID</th>
                            <th>商品名称</th>
                            <th>用户名称</th>
                            <th>星级</th>
                            <th style="width: 300px">评论内容</th>
                            <th>评论时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($data) > 0)
                        @foreach($data as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->goodsname}}</td>
                                <td>{{$item->username}}</td>
                                <td>{{$item->stars}}★</td>
                                <td><p style="width: 300px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">{{$item->content}}</p></td>
                                <td>{{$item->created_at}}</td>
                                <td>
                                    <a onclick="del({{$item->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                                </td>
                            </tr>
                        @endforeach
                            @else
                            <tr>
                                <td colspan="7">对不起没有查询到相关内容</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    {{$data}}
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <script type="text/javascript">
        function del(e) {
            var id = e;
            layer.alert("是否删除该数据？",{icon:3},function (index) {
                location.href="{{route('hotel.commnetsDel')}}?id="+id;
                layer.close(index);
            });
        }
    </script>
@endsection