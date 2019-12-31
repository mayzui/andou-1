@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>领取记录</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>

                <a  href="{{url('/admin/coupon/uselogAdd')}}" type="button" class="btn btn-primary"><i class="fa fa-plus-circle"></i>添加</a>

                <style>
                    th ,td{
                        text-align: center;
                    }
                </style>
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th width="100">ID</th>
                        <th>领取的用户</th>
                        <th>优惠券名称</th>
                        <th>优惠券类型</th>
                        <th>领取时间</th>
                        <th>结束时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $data as $k => $item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->name}}</td>
                            <td>{{$item->coupon_name}}</td>
                            <td>
                                @if($item->coupon_type_id == 0)
                                    平台优惠券
                                @elseif($item->coupon_type_id == 1)
                                    商家优惠券
                                @endif
                            </td>
                            <td>{{$item->start_at}}</td>
                            <td>{{$item->end_at}}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{url("/admin/coupon/useLogDel?id=$item->id")}}" onClick="delcfm()"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o" ></i> 删除</button></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{$data}}
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <script language="javascript">
        function delcfm() {
            if (!confirm("优惠券记录确认要删除吗？")) {
                window.event.returnValue = false;
            }
        }

    </script>
@endsection