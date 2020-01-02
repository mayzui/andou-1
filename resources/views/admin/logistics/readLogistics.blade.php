@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/css/base.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/layui.css')}}" rel="stylesheet">
<link href="{{loadEdition('/admin/css/style.css')}}" rel="stylesheet">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
<style type="text/css">
    .logistics{
        position: relative;
        padding: 0 30px;
        /* border: 1px solid #EEEEEE; */
    }
    .logistics-list {
        padding-left: 30px;
    }
    .logistics-list .items {
        position: relative;
        padding: 10px 0 10px 20px;
        font-size: 14px;
        color: #646464;
    }
    .logistics-list .item:first-child, .logistics-list .item:first-child .text-grey {
        color: #1eb1f3;
    }
    .logistics-list .items::before {
        position: absolute;
        left: -22px;
        bottom: -18px;
        content: '';
        width: 1px;
        height: 40px;
        background-color: #efefef;
    }
    .logistics-list .items:first-child, .logistics-list .items:first-child .text-grey {
        color: #1eb1f3;
    }
    .mt-10 {
        margin-top: 10px;
    }
    .text-grey {
        color: #999;
    }
    .logistics-list .items:first-child::after {
        width: 12px;
        height: 12px;
        background: #1eb1f3;
        border: 3px solid #d7efeb;
    }
    .logistics-list .items::after {
        position: absolute;
        top: 50%;
        left: -28px;
        content: '';
        margin-top: -8px;
        width: 12px;
        height: 12px;
        background-color: #d8d8d8;
        border-radius: 50%;
    }
    .logistics-list .items:last-child::before {
        width: 0;
    }
</style>
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>查看物流信息</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{route('logistics.goGoods')}}" method="post" accept-charset="UTF-8">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{ $id or '' }}" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label">物流公司：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="name" value="{{$data['name'] or ''}}" readonly style="border: 1px solid #e5e6e7;"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">快递单号：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="courier_num" value="{{$data['courier_num'] or ''}}" readonly style="border: 1px solid #e5e6e7;"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">订单状态：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="state" value="{{$state or ''}}" readonly style="border: 1px solid #e5e6e7;" />
                        </div>
                    </div>
                    <div class="form-group" style="position: relative;">
                        <label class="col-sm-2 control-label" style="position: absolute;top: 50%;left: 0;transform: translateY(-50%)">物流信息：</label>
                        <div class="logistics col-sm-10" style="border: 1px solid #e5e6e7;float: right;">
                            <ul class="logistics-list">
                                @foreach($data['wuliu_msg']['data'] as $v)
                                    <div class="form-group">
                                        <li class="items">
                                            <div>{{$v['context'] or ''}}</div>
                                            <div class="mt-10 text-grey">{{$v['time'] or ''}}</div>
                                        </li>
                                    </div>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection
