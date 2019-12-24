@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>用户反馈意见</h5>
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
                        <th class="text-center" width="100">ID</th>
                        <th class="text-center" width="150">用户名</th>
                        <th class="text-center" style="width: 200px" >反馈内容</th>
                        <th class="text-center" width="100">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $v)
                        <tr>
                            <td>{{ $v -> id }}</td>
                            <td>{{ $v -> name }}</td>
                            <td><p>{{ $v -> content }}</p></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a onclick="del({{$v->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            {{--{{$data}}--}}
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function del(e) {
        var id = e;
        layer.alert("是否删除该数据？",{icon:3},function (index) {
            location.href="{{route('feedback.indexDel')}}?id="+id;
            layer.close(index);
        });
    }
</script>
@endsection