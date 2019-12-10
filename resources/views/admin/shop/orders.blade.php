@extends('admin.layouts.layout')
<style>
    th ,td{
        text-align: center;
    }
</style>

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>订单管理</h5>
            </div>
            <div class="ibox-content">
                <div class="col-sm-3" style="padding-left: 0px;">
                    <div class="input-group">
                        <input type="text" class="form-control" v-model="key" placeholder="输入需查询的关键字" />
                        <span class="input-group-btn">
                           <a type="button" class="btn btn-primary" @click="search"><i class="fa fa-search"></i> 搜索</a>
                    </span>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <form method="post" action="{{route('shop.index')}}" name="form">
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th width="100">ID</th>
                            <th>订单号</th>
                            <th>下单人</th>
                            <th>支付方式</th>
                            <th>支付金额</th>
                            <th>总计金额</th>
                            <th>抵扣金额</th>
                            <th>订单状态</th>
                            <th>邮费</th>
                            <th>订单备注</th>
                            <th>发票信息</th>
                            <th>支付时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>

                    </table>

                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
@endsection