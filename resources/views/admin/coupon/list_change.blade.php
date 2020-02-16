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
                <h5>编辑优惠券</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{route('coupon.list_change')}}" method="POST">
                    <input type="hidden" name="id" value="{{ $data -> id or '' }}" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label">优惠券名称：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" name="coupon_name" value="{{$data -> coupon_name or ''}}" class="form-control" required data-msg-required="">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">优惠券分类：</label>
                        <select style="height: 25px;width: 273px;" name="coupon_type_id" id="coupon_type_id">
                            @if(empty($data))
                            <option value="0" >——请选择优惠券分类——</option>
                            <option value="1" >平台优惠券</option>
                            <option value="2" disabled >商户优惠券</option>
                                @else
                                <option value="0"  >——请选择优惠券分类——</option>
                                <option value="1" @if($data -> coupon_type_id == 1) selected @endif >平台优惠券</option>
                                <option value="2" @if($data -> coupon_type_id == 2) selected @endif disabled >商户优惠券</option>
                            @endif
                        </select>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">开始时间：</label>
                        <div class="input-group col-sm-2">
                            <input type="datetime-local" class="form-control" class="one_time" value="{{ $data -> start_at or '' }}" name="start_at" placeholder="请选择时间">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">结束时间：</label>
                        <div class="input-group col-sm-2">
                            <input type="datetime-local" class="form-control" class="end_at" value="{{ $data -> end_at or '' }}" name="end_at" placeholder="请选择时间">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">优惠券总数：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" name="max_mun" value="{{$data -> max_mun or ''}}" class="form-control" required data-msg-required="">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">价值金额：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" name="money" value="{{$data -> money or ''}}" class="form-control" required data-msg-required="">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <div class="col-sm-12 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>
                            <button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    {{csrf_field()}}

                </form>
            </div>
        </div>
    </div>
    <div id="functions" style="display: none;">
        @include('admin.rules.fonticon')
    </div>
@section('footer-js')
    <script>

        function showicon(){
            layer.open({
                type: 1,
                title:'点击选择图标',
                area: ['800px', '80%'], //宽高
                anim: 2,
                shadeClose: true, //开启遮罩关闭
                content: $('#functions')
            });
        }

        $('.fontawesome-icon-list .fa-hover').find('a').click(function(){
            var str=$(this).text();
            $('#fonts').val( $.trim(str));
            layer.closeAll();
        })
    </script>
@endsection

