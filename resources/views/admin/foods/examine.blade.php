@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>饭店审核</h5>
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
                        <th width="100">ID</th>
                        <th>用户ID</th>
                        <th>商家名称</th>
                        <th style="width: 300px">商家简介</th>
                        <th>商家地址</th>
                        <th>商家类型名称</th>
                        <th>认证状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($data) > 0)
                        @foreach($data as $v)
                            <tr>
                                <th>{{ $v->id }}</th>
                                <th>{{ $v->user_id }}</th>
                                <th>{{ $v->name }}</th>
                                <th><p style="width: 300px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" >{{ $v->desc }}</p></th>
                                <th>{{ $v->address }}</th>
                                <th>{{ $v->type_name }}</th>
                                <th style="color: blue">{{ $v->is_reg == 0 ? "未认证" : "已通过" }} </th>
                                <td class="text-center">
                                    <div class="btn-group">
                                        @if($v->is_reg == 0)
                                            <a onclick="del({{$v->id}})"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-adn"></i> 认证通过</button></a>
                                            @else
                                            <a><button class="btn btn-primary btn-xs" type="button" disabled><i class="fa fa-adn"></i> 认证通过</button></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th colspan="7">暂时还没有数据</th>
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
            layer.alert("是否通过该商家的申请？",{icon:3},function (index) {
                location.href="{{route('foods.examinepass')}}?id="+id;
                layer.close(index);
            });
        }
    </script>

@endsection