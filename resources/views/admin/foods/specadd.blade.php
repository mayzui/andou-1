@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>新增菜品规格</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>

                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{route('foods.specadd')}}" method="post" accept-charset="UTF-8">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{ $id or '' }}" />
                    <input type="hidden" name="spec_id" value="{{$id}}" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label">规格名称：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="name" value="{{$data->name or ''}}" autocomplete="off" required placeholder="请输入规格名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">规格报价：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="price" value="{{$data->price or ''}}" autocomplete="off" required placeholder="请输入规格报价">
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
