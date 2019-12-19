@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>菜品购物车</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>

                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{route('foods.orderschange')}}" method="post" accept-charset="UTF-8">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{ $order -> id or '' }}" />
                    @if(!empty($information))
                        @foreach($information as $v)
                            <input type="hidden" name="ids[]" value="{{ $v ->id }}">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">菜品名称：</label>
                                <div class="input-group col-sm-2">
                                    <input type="text" class="form-control" name="name[]" value="{{$v -> name or ''}}" readonly required placeholder="请输入菜品名称">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">菜品单价：</label>
                                <div class="input-group col-sm-2">
                                    <input type="text" class="form-control" name="price[]" onblur="calculation()" value="{{$v -> price or ''}}" readonly required placeholder="请输入菜品单价">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">菜品数量：</label>
                                <div class="input-group col-sm-2">
                                    @if(!empty($particulars))
                                        @foreach($particulars as $m)
                                            @if($v ->id == $m ->foods_id)
                                                <input type="text" class="form-control" name="num[]" onblur="calculation()" value="{{ $m ->num or '' }}" placeholder="请输入菜品数量">
                                            @endif
                                        @endforeach
                                        @else
                                        <input type="text" class="form-control" name="num[]" onblur="calculation()" value="" placeholder="请输入菜品数量">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <div>
                        <hr/>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">客户名称：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="user_name" value="{{$order -> user_name or ''}}" required placeholder="请输入联系电话">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">客户电话：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="phone" value="{{$order -> phone or ''}}" required placeholder="请输入联系电话">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">用餐人数：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="people" value="{{$order->people or ''}}" required placeholder="请输入用餐人数">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">用餐时间：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control layui-input" name="dinnertime" value="{{ $order -> dinnertime or '' }}" id="test1">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">备注：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="remark" value="{{$order->remark or ''}}" required placeholder="请输入备注">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">下单总金额：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="prices" id="prices" value="{{$order->prices or ''}}" readonly required placeholder="请输入下单总金额">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">支付方式：</label>
                        <select  style="height: 25px;width: 273px;" name="method" >
                            <option value="1" @if($order -> method == 1) selected @endif >微信</option>
                            <option value="2" @if($order -> method == 2) selected @endif >支付宝</option>
                            <option value="3" @if($order -> method == 3) selected @endif >银联</option>
                            <option value="4" @if($order -> method == 4) selected @endif >余额</option>
                        </select>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    @if(empty($order -> id))
                        <div class="form-group">
                            <div class="col-sm-12 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>　
                                <button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                            </div>
                        </div>
                    @endif
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        // 用js计算菜品的总金额
        var p = document.getElementsByName('price[]');
        var num = document.getElementsByName('num[]');
        var c = 0;
        for (var i = 0;i<p.length;i++){
            c = c + (p[i].value * num[i].value);
        }
        document.getElementById('prices').value=c;
        // 当更改菜品数量是，重新计算菜品总金额
        function calculation() {
            var p = document.getElementsByName('price[]');
            var num = document.getElementsByName('num[]');
            var c = 0;
            for (var i = 0;i<p.length;i++){
                c = c + (p[i].value * num[i].value);
            }
            document.getElementById('prices').value=c;
        }

        layui.use('laydate', function(){
            var laydate = layui.laydate;
            //执行一个laydate实例
            laydate.render({
                elem: '#test1' //指定元素
                ,type: 'datetime'
            });
        });


    </script>
@endsection
