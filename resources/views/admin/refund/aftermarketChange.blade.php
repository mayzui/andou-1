@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>订单详情</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{ route('refund.indexChange') }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{ $data->id or '' }}" />
                    @foreach($goodsdata as $v)
                        <div class="form-group">
                            <label class="col-sm-2 control-label">商品名称：</label>
                            <div class="input-group col-sm-2">
                                <input type="text" class="form-control" name="goods_name" value="{{ $v -> goods_name or '' }}" readonly required data-msg-required="商品名称">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">商品价格：</label>
                            <div class="input-group col-sm-2">
                                <input type="text" class="form-control" name="goods_price" value="{{ $v -> goods_price or '' }}" readonly required data-msg-required="商品价格">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">购买数量：</label>
                            <div class="input-group col-sm-2">
                                <input type="text" class="form-control" name="num" value="{{ $v -> num or '' }}" readonly required data-msg-required="购买数量">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">商品规格：</label>
                            <div class="input-group col-sm-2">
                                <input type="text" class="form-control" name="attr_value" value="{{ $v -> attr_value or '' }}" readonly required data-msg-required="商品规格">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">商品图片：</label>
                            <div class="layui-upload-list">
                                    <img class="layui-upload-img" id="goods_img" src="{{ $v->goods_img or '' }}" style="width: 80px;height: 80px">
                                <p id="demoText"></p>
                            </div>
                        </div>
                    @endforeach
                    <div class="form-group">
                        <label class="col-sm-2 control-label">订单号：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="order_id" value="{{ $order_money -> order_id or '' }}" readonly required data-msg-required="订单号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">订单总金额：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="order_money" value="{{ $order_money -> pay_money or '' }}" readonly required data-msg-required="订单总金额">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">收货人姓名：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="consignee_realname" value="{{ $orderdata -> consignee_realname or '' }}" readonly required data-msg-required="收货人姓名">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">收货人电话：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="consignee_telphone" value="{{ $orderdata -> consignee_telphone or '' }}" readonly required data-msg-required="收货人电话">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">收货人地址：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="consignee_address" value="{{ $orderdata -> consignee_address or '' }}" readonly required data-msg-required="收货人地址">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">物流公司：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="express_company" value="{{ $orderdata -> express_company or '' }}" readonly required data-msg-required="物流公司">
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>

@endsection
