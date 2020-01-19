@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>退货原因</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                @if(!empty($i))
                <a href="{{route('refund.indexChange')}}" link-url="javascript:void(0)">
                    <button class="btn btn-primary btn-sm" type="button">
                        <i class="fa fa-plus-circle"></i> 新增退货原因
                    </button>
                </a>
                @endif
                <form method="post" action="{{route('config.index')}}" name="form">
                    <style>
                        th ,td{
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th width="100">ID</th>
                            <th>商家名称</th>
                            <th>退款原因</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($data) > 0)
                        @foreach($data as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->merchants_name}}</td>
                                <td>{{$item->reason_name}}</td>
                                <td>@if($item->is_del==1)
                                        <p style="color: red">已删除</p>
                                    @else
                                        <p style="color: blue">运行中</p>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{route('refund.indexChange')}}?id={{$item->id}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>
                                        @if($item->is_del==1)
                                            <a href="{{route('refund.indexDel')}}?id={{$item->id}}&is_del=1"><button class="btn btn-group btn-xs" type="button"><i class="fa fa-adn"></i> 恢复</button></a>
                                        @else
                                            <a href="{{route('refund.indexDel')}}?id={{$item->id}}&is_del=0"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                            @else
                        <tr>
                            <td colspan="5">对不起未查询到相关内容</td>
                        </tr>
                        @endif
                        </tbody>
                    </table>
                    {{$data}}
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
@endsection