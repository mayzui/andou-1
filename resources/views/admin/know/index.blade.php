{{--@extends('admin.layouts.layout')--}}
{{--<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">--}}
{{--<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>--}}
{{--<link rel="stylesheet" href="/a">--}}
{{--<script src=""></script>--}}
{{--@section('content')--}}
{{--    <div class="row">--}}
{{--        <div class="col-sm-12">--}}
{{--            <div class="ibox-title">--}}
{{--                <h5>入住需知</h5>--}}
{{--            </div>--}}
{{--            <div class="ibox-content">--}}
{{--                <div class="hr-line-dashed m-t-sm m-b-sm"></div>--}}
{{--                <form class="form-horizontal m-t-md" action="{{route('know.add')}}" method="post" accept-charset="UTF-8">--}}
{{--                    {!! csrf_field() !!}--}}
{{--                    <input type="hidden" name="id" value="{{ $data -> id or '' }}" />--}}
{{--                    <div class="form-group">--}}
{{--                        <label class="col-sm-2 control-label">标题：</label>--}}
{{--                        <div class="input-group col-sm-2">--}}
{{--                            <input type="text" class="form-control" name="title" value="{{$data -> title or ''}}" required placeholder="请输入标题">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-group">--}}
{{--                        <label class="col-sm-2 control-label">内容：</label>--}}
{{--                        <div class="input-group col-sm-2">--}}
{{--                            <textarea cols="36" rows="10" name="content">{{ $data -> content or '' }}</textarea>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>--}}
{{--                    <div class="form-group">--}}
{{--                        <div class="col-sm-12 col-sm-offset-2">--}}
{{--                            <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>　<button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="clearfix"></div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--@endsection--}}

<link rel="stylesheet" href="/admin/">