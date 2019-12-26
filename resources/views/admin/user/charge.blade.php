@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>充值流水</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>&nbsp;
                    <style>
                        th ,td{
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th width="100">流水id</th>
                            <th>用户名称</th>
                            <th>流动金额</th>
                            <th style="width: 200px">流动描述</th>
                            <th>财务状况</th>
                            <th>流动时间</th>
                            <th>所属类型</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            @if(count($data) > 0)
                                @foreach($data as $v)
                                    <tr>
                                        <th>{{ $v -> id }}</th>
                                        <th>{{ $v -> name }}</th>
                                        <th>{{ $v -> price }}</th>
                                        <th><p style="width: 200px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">{{ $v -> describe }}</p></th>
                                        <th>
                                            @if($v -> state == 1)
                                                <p style="color: green">获得</p>
                                            @else
                                                <p style="color: red">消耗</p>
                                            @endif
                                        </th>
                                        <th>{{ $v -> create_time }}</th>
                                        <th>
                                            @if($v -> type_id == 1)
                                                感恩币流水
                                                @elseif($v -> type_id == 2)
                                                余额流水
                                                @elseif($v -> type_id == 3)
                                                提现流水
                                            @endif
                                        </th>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a onclick="del({{$v->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-ban"></i> 删除</button></a>
                                            </div>
                                        </td>
                                    </tr>

                                @endforeach
                                @else
                                <tr>
                                    <th colspan="8">暂时还没有数据</th>
                                </tr>
                            @endif
                        </tbody>
                    </table>

            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <script type="text/javascript">
        function del(e) {
            var id = e;
            layer.alert("是否删除该数据？",{icon:3},function (index) {
                location.href="{{route('user.cashLogsDel')}}?id="+id;
                layer.close(index);
            });
        }
    </script>

@endsection