@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                @if(empty($data->id))
                    <h5>新增用户流水</h5>
                    @else
                    <h5>修改用户流水</h5>
                @endif
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>

                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{route('user.cashLogsChange')}}" method="post" accept-charset="UTF-8">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{ $data->id or '' }}" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label">流动描述：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="describe" value="{{$data->describe or ''}}" required placeholder="请输入流动描述">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">感恩币状态：</label>
                        <select  style="height: 25px;width: 273px;" name="state" >
                                <option value="1" >获得</option>
                                <option value="0" >消耗</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">流动金额：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="price" value="{{$data->price or ''}}" required placeholder="请输入流动金额">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">流水类型：</label>
                        <select style="height: 25px;width: 273px;" name="type_id" id="type_id">
                            <option value="1" >积分流水</option>
                            <option value="2" >充值流水</option>
                            <option value="3" >提现流水</option>
                        </select>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <div class="col-sm-12 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>　<button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>

@endsection
