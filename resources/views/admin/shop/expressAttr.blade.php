@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>运费管理</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>

                <button type="button" class="btn btn-primary btn-outline pull-right" data-toggle="modal" data-target="#myModal">添加运费</button>

                <form method="post" action="{{route('shop.expressAttr')}}" name="form">
                    <style>
                        th ,td{
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th width="15">ID</th>
                            <th>名称</th>
                            <th>计费运费</th>
                            <th>基础运费</th>
                            <th>运费单价</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>


                        @foreach($list as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->city->name}}</td>
                                <td>{{$item->caculate_method}}</td>
                                <td>{{$item->basic_price}}</td>
                                <td>{{$item->unit_price}}</td>
                                <td>
                                    <a href="{{route('shop.deleteExpressAttr',$item->id)}}"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
@endsection

<div class="modal  fade" id="myModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title">添加运费</h3>
            </div>

            <form class="form-horizontal"  method="post" action="{{ route('shop.storeExpressAttrs')}}">
                <div class="ibox-content">
                    <div class="form-group">
                        {!! csrf_field() !!}
                        <input type="hidden" name="express_id" value="{{$data->id}}"  />
                        <label class="col-sm-3 control-label">选择城市</label>
                        <div class="col-sm-8">
                                @foreach($city as $c)
                                    <lable>
                                        <input type="checkbox" name="ids[]"  @if (in_array($c->id,$ids)) checked @endif value="{{$c->id}}"/> {{$c->name}}
                                    </lable>
                                @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">计费方式</label>
                        <div class="col-sm-8">
                            <select name="caculate_method" class="form-control">
                                <option value="1">重量</option>
                                <option value="2">件数</option>
                                <option value="3">里程</option>
                                <option value="4">固定运费(只计算基础运费)</option>
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">基础运费</label>
                        <div class="col-sm-8">
                            <input type="text" name="basic_price"  placeholder="基础运费" class="form-control"/>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">单价</label>
                        <div class="col-sm-8">
                            <input type="text" name="unit_price"  placeholder="基础运费" class="form-control"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> 保存</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> 关闭</button>
                </div>
            </form>
        </div>
    </div>
</div>