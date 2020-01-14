
@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>入住需知</h5>
            </div>
            <div class="ibox-content">
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
                <meta name="csrf-token" content="{{ csrf_token() }}" />
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                <script type="text/javascript" charset="utf-8" src="/ueditor1_4_3_3-utf8-php/utf8-php/ueditor.config.js"></script>
                <script type="text/javascript" charset="utf-8" src="/ueditor1_4_3_3-utf8-php/utf8-php/ueditor.all.min.js"> </script>
                <script type="text/javascript" charset="utf-8" src="/ueditor1_4_3_3-utf8-php/utf8-php/lang/zh-cn/zh-cn.js"></script>
                </head>
                <body>
                <div style="margin-top: 40px;margin-left: 500px;">
                    <script id="container" name="content" type="text/plain" style="width: 80%;height: 40%;">
<span>{{ $data->need_content or '尊敬的客户您好:'}}</span>
</script>
                    <script type="text/javascript" src="/ueditor1_4_3_3-utf8-php/utf8-php/ueditor.config.js"></script>
                    <script type="text/javascript" src="/ueditor1_4_3_3-utf8-php/utf8-php/ueditor.all.js"></script>
                </div>
                <div class="clear"></div>
                <div id="btns">
                    <table>
                        <tr>
                            <td>
                                <button class="btn btn-primary" onclick="getContentTxt()"   style="margin-left: 890px;margin-top: 30px;" ><i class="fa fa-check"></i>&nbsp;保 存</button>　
                            </td>
                        </tr>
                    </table>
                </div>
                <div>
                    <h3 id="focush2"></h3>
                </div>
                <script type="text/javascript">
                    //实例化编辑器
                    var um = UE.getEditor('container');
                    function getContentTxt() {
                        var arr = [];
                        arr.push(UE.getEditor('container').getContentTxt());
                        var content  = arr.join("\n");
                            location.href="{{route('know.add')}}?content="+content
                    }
                </script>
            </div>
        </div>
    </div>

@endsection
