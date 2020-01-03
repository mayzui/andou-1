@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>

<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <title>入住需知</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="/umeditor1_2_3-utf8-php/utf8-php/themes/default/css/umeditor.css">
    <script type="text/javascript" src="/umeditor1_2_3-utf8-php/utf8-php/third-party/jquery.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="/umeditor1_2_3-utf8-php/utf8-php/umeditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/umeditor1_2_3-utf8-php/utf8-php/umeditor.min.js"></script>
    <script type="text/javascript" src="/umeditor1_2_3-utf8-php/utf8-php/lang/zh-cn/zh-cn.js"></script>
</head>
<body>
<h1 style="margin-left: 60px;">入住需知</h1>

<!--style给定宽度可以影响编辑器的最终宽度-->

<div style="margin-top: 300px;margin-left: 400px;">

    <script type="text/plain" id="myEditor" style="width:600px;height:200px;">
    <p>{{ $data->need_content or '尊敬的客户您好:' }}</p>

</script>


</div>
<div class="clear"></div>
<div id="btns">
    <table>
        <tr>
            <td>
                <button class="btn btn-primary" onclick="getContentTxt()"   style="margin-left: 670px;margin-top: 30px;" ><i class="fa fa-check"></i>&nbsp;保 存</button>　
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
        var content  = arr.join("\n");
        $.ajax({
            url:'{{url('admin/know/www')}}',
            type:"post",
            data:{
                '_token':'{{csrf_token()}}',
                'content':content
            },
            success:function (e) {
                 var code = JSON.parse(e)
                if(code.code == 0){
                    alert(code.msg)
                }
                if(code.code == 1){
                    alert(code.msg)
                }
                if(code.code == 2){
                    alert(code.msg)
                }
            }
        })
    }
</script>
</body>
</html>