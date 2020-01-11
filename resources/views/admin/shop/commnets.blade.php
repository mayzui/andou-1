@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>评论管理</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>

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
                            <th width="200px">商品名称</th>
                            <th>用户名称</th>
                            <th width="100px">星级</th>
                            <th width="300px">评论内容</th>
                            <th width="200px">评论时间</th>
                            <th width="200px">回复内容</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td><p style="width: 200px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">{{$item->goodsname}}</p></td>
                                <td>{{$item->username}}</td>
                                <td>{{$item->stars}}<span style="color: green">★</span></td>
                                <td><p style="width: 300px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;margin:0px">{{$item->content}}</p></td>
                                <td>{{$item->created_at}}</td>
                                <td>
                                    @if(empty($item -> merchant_content))
                                        <span style="color: blue">未回复</span>
                                        @else
                                        {{ $item -> merchant_content }}
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-xs type_info" data-id="{{$item->id}}" data-content="{{ $item -> merchant_content }}" data-toggle="modal" data-target="#myModal"><i class="fa fa-comment-o"></i> 回复</button>
                                    <a onclick="del({{$item->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{$data}}
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    </div>
    </div>
    <div class="modal inmodal fade" id="myModal" tabindex="-1" role="dialog"  aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h3>评论回复</h3>
                </div>
                <form class="form-horizontal m-t-md" action="{{ route('shop.commnetReply') }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <div class="modal-body">
                        <table class="table table-striped table-bordered table-hover m-t-md" style="font-size: 14px"
                            <tbody>
                            <div class="form-group">
                                <input type="hidden" id="flag_id" name="id" value="">
                                <label class="col-sm-2 control-label">回复内容：</label>
                                <div class="input-group col-sm-8">
                                    <input type="text" class="form-control replyName" name="replyName" id="replyName" value="{{ $item -> merchant_content or '' }}" required data-msg-required="请输入商品名称" style="height: 40px">
                                </div>
                            </div>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>
                        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <script type="text/javascript">
        function del(e) {
            var id = e;
            layer.alert("是否删除该数据？",{icon:3},function (index) {
                location.href="{{route('shop.commnetsDel')}}?id="+id;
                layer.close(index);
            });
        }
        // 获取当前id
        $(document).on('click', '.type_info', function () {
            var type_id = $(this).data('id');
            var type_content = $(this).data('content');
            document.getElementById("flag_id").value=type_id;
            document.getElementById("replyName").value=type_content;
        });
    </script>
@endsection