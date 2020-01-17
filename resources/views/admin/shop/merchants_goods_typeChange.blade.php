@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                @if(empty($data['id']))
                    <h5>新增商品分类</h5>
                @else
                    <h5>修改商品分类</h5>
                @endif
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{ route('shop.merchants_goods_typeChange') }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{ $data['id'] or '' }}" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label">分类名称：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="name" id="name" value="{{$data['name'] or ''}}" required placeholder="请输入分类名称">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">产品数：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="num" id="num" value="{{$data['num'] or ''}}" required placeholder="请输入产品数">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit" ><i class="fa fa-check"></i>&nbsp;保 存</button>　<button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
@endsection
