@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>添加评论</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{ route('foods.commnetsAdd') }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品名称：</label>
                        <select style="height: 25px;width: 273px;" name="goods_id" id="goods_id">
                            <option value="0" >——请选择商品分类——</option>
                            @foreach($goodsData as $v)
                                <option value="{{ $v -> id }}" >{{ $v -> name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">评论内容：</label>
                        <div class="input-group col-sm-2">
                            <textarea cols="36" rows="10" name="content" id="content"></textarea>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">给个评分吧</label>
                        <select style="height: 25px;width: 273px;" name="stars" id="stars">
                            <option value="5" >5★</option>
                            <option value="4" >4★</option>
                            <option value="3" >3★</option>
                            <option value="2" >2★</option>
                            <option value="1" >1★</option>
                        </select>
                    </div>
                    <div class="hr-line-dashed"></div>
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
