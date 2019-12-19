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
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">订单状态：</label>
                        <div class="col-sm-2">
                            <select name="status" class="form-control">
                                <option value="10" @if(old('status') == 10) selected="selected" @endif>未支付</option>
                                <option value="20" @if(old('status') == 20) selected="selected" @endif>已支付</option>
                                <option value="40" @if(old('status') == 40) selected="selected" @endif>已发货</option>
                                <option value="50" @if(old('status') == 50) selected="selected" @endif>交易成功</option>
                                <option value="60" @if(old('status') == 60) selected="selected" @endif>交易关闭</option>
                                <option value="0" @if(old('status') == 0) selected="selected" @endif>已取消</option>
                            </select>
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
