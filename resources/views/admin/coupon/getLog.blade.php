@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>发放记录</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>

                <style>
                    th ,td{
                        text-align: center;
                    }
                </style>
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th width="100">ID</th>
                        <th>发放人</th>
                        <th>发放优惠券名称</th>
                        <th>发放优惠券类型</th>
                        <th>发放时间</th>
                        <th>结束时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach( $data as $k => $item )
                    <tr>
                        <th>{{$item->id}}</th>
                        <th>{{$item->name}}</th>
                        <th>{{$item->coupon_name}}</th>
                        <th>
                            @if($item->coupon_type_id == 0)
                                平台优惠券
                            @elseif($item->coupon_type_id == 1)
                                商家优惠券
                            @endif
                            </th>
                        <th>{{$item->start_at}}</th>
                        <th>{{$item->end_at}}</th>
                        <th>
                            <div class="btn-group">
                                <a href="{{url("/admin/coupon/getLogDel?id=$item->id")}}" onClick="delcfm()"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o" ></i> 删除</button></a>
                            </div>
                        </th>
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