@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
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



<!DOCTYPE HTML>
<html>
<head>

    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <title>UMEDITOR 完整demo</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="/umeditor1_2_3-utf8-php/utf8-php/themes/default/css/umeditor.css">
    <script type="text/javascript" src="/umeditor1_2_3-utf8-php/utf8-php/third-party/jquery.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="/umeditor1_2_3-utf8-php/utf8-php/umeditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/umeditor1_2_3-utf8-php/utf8-php/umeditor.min.js"></script>
    <script type="text/javascript" src="/umeditor1_2_3-utf8-php/utf8-php/lang/zh-cn/zh-cn.js"></script>
</head>
<body>
<h1>UMEDITOR 完整demo</h1>

<!--style给定宽度可以影响编辑器的最终宽度-->

<div style="margin-top: 300px;margin-left: 300px;">

    <script type="text/plain" id="myEditor" style="width:600px;height:200px;">
    <p>请输入内容</p>
</script>

</div>
<div class="clear"></div>
<div id="btns">
    <table>
        <tr>
            <td>
                <button class="btn" onclick="getContentTxt()" ><i class="fa fa-check btn " onclick="getContentTxt()"></i>保存</button>&nbsp;
                <button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
            </td>
        </tr>
    </table>
</div>
<div>
    <h3 id="focush2"></h3>
</div>
<script type="text/javascript">
    //实例化编辑器
    var um = UM.getEditor('myEditor');
    function getContentTxt() {
        var arr = [];
        arr.push(UM.getEditor('myEditor').getContentTxt());
        alert(arr.join("\n"));
    }
</script>
<div class="col-sm-12 col-sm-offset-2">
    <button class="btn btn-primary" type="submit"><i class="fa fa-check btn " onclick="getContentTxt()"></i>&nbsp;保 存</button>　<button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
</div>
</body>
</html>