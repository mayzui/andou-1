@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>菜品信息</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>

                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{route('foods.set_meal_informationChange')}}" method="post" accept-charset="UTF-8">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{ $meal->id or '' }}" />
                    <input type="hidden" name="merchant_id" value="{{ $meal->merchant_id or '' }}" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label">套餐id：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="set_meal_id" value="{{$meal->id or ''}}" readonly required placeholder="套餐id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">套餐名称：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="set_meal_name" value="{{$meal->name or ''}}" readonly="readonly" required placeholder="套餐名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">菜品名称：</label>
                        <div class="input-group col-sm-2 checkbox">
                            @foreach($information as $v)
                                <label><input type="checkbox" name="information_id[]" @if(in_array($v->id,$information_id)) checked="checked" @endif value="{{ $v->id }}" />&nbsp;{{ $v->name }}</label><br/>
                            @endforeach
                        </div>
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
