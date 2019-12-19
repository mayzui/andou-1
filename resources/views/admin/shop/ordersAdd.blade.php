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
                <form class="form-horizontal m-t-md" action="{{url('/admin/shop/ordersAdds')}}" method="POST">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">下单人：</label>
                        <div class="col-sm-3">
                            <input type="text" name="user_id" value="{{old('name')}}" class="form-control" required data-msg-required="">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">支付金额：</label>
                        <div class="col-sm-3">
                            <input type="text" name="pay_money" value="{{old('name')}}" class="form-control" required data-msg-required="请输入分类名称">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">总计金额：</label>
                        <div class="col-sm-3">
                            <input type="text" name="order_money" value="{{old('name')}}" class="form-control" required data-msg-required="请输入分类名称">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">抵扣金额：</label>
                        <div class="col-sm-3">
                            <input type="text" name="pay_discount" value="{{old('name')}}" class="form-control" required data-msg-required="请输入分类名称">
                        </div>
                    </div>

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
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">邮费：</label>
                        <div class="col-sm-3">
                            <input type="text" name="shipping_free" value="{{old('name')}}" class="form-control" required data-msg-required="请输入分类名称">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">订单备注：</label>
                        <div class="col-sm-3">
                            <input type="text" name="remark" value="{{old('name')}}" class="form-control" required data-msg-required="请输入分类名称">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">发票信息：</label>
                        <div class="col-sm-2">
                            <select name="auto_receipt" class="form-control">
                                <option value="3" @if(old('status') == 3) selected="selected" @endif>客户发票</option>
                                <option value="1" @if(old('status') == 1) selected="selected" @endif>自动发票</option>
                                <option value="0" @if(old('status') == 0) selected="selected" @endif>不开发票</option>
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">支付方式：</label>
                        <div class="col-sm-2">
                            <select name="pay_way" class="form-control">
                                <option value="1" @if(old('status') == 1) selected="selected" @endif>支付宝</option>
                                <option value="0" @if(old('status') == 0) selected="selected" @endif>微信</option>
                                <option value="2" @if(old('status') == 2) selected="selected" @endif>余额</option>
                                <option value="3" @if(old('status') == 3) selected="selected" @endif>其他</option>
                            </select>
                        </div>
                    </div>
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
