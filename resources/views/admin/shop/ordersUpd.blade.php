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
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
            </div>
            <div class="ibox-content">
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{url('/admin/shop/ordersUpds')}}" method="POST">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">支付金额：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="pay_money" id="name" value="{{$data['pay_money']}}" required placeholder="支付金额">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">总计金额：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name=""  value="{{$data['total']}}" required placeholder="总计金额">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">邮费：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="" value="{{$data['shipping_free']}}" required placeholder="邮费">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">支付时间：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control"  id="name" value="{{$data['pay_time']}}" required placeholder="支付时间">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">订单备注：</label>
                        <div class="input-group col-sm-2">
                            <textarea cols="36" rows="10" >{{ $data['remark'] or '' }}</textarea>
                        </div>
                    </div>

                    <input type="hidden" name="id" value="{{$id}}">
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <div class="col-sm-12 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>
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
@endsection
