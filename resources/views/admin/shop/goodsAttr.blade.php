@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('content')
    <style>
        th,td{
            text-align: center;
        }
    </style>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>属性列表</h5>
            </div>
            <div class="ibox-content">
                <button type="button" class="btn btn-primary btn-xl" data-toggle="modal" id="addmuban">新增模板</button>
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th width="10%">模板id</th>
                        <th width="30%">模板名称</th>
                        <th class="text-center" width="250">管理</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($list as $k=>$item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->name}}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline btn-primary btn-xs type_info" data-id="{{$item->id}}" data-toggle="modal" data-target="#addMyModal"><i class="fa fa-paste"></i>编辑</button>
                                <a href="{{route('shop.attrDelete',$item->id)}}"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    </div>
    </div>
@endsection
{{--新增模态框--}}
<div class="modal inmodal fade" id="addMyModal" tabindex="-1" role="dialog"  aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3>编辑模板</h3>
            </div>
            <form class="form-horizontal m-t-md" action="{{ route('shop.attrStore') }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                {!! csrf_field() !!}
            <div class="modal-body">
                    <table class="table table-striped table-bordered table-hover m-t-md" style="font-size: 14px">
                        <thead>
                        <tr>
                            <td>
                                <input type="hidden" id="flag_id" name="id" value="">
                                <span><em style="margin-right:5px;vertical-align: middle;color: #fe0000;">*</em>模板名称：<input type="text" name="specNmae" id="specNmae" required /></span>
                            </td>
                            <td>

                            </td>
                            <td>
                                <div class="btn btn-outline btn-primary add" title="新增规格" id="add_spec"><span><i class="fa fa-plus"></i> 新增规格</span></div>
                            </td>
                        </tr>
                        <tr>
                            <th width="100">规格名称</th>
                            <th width="450">规格值</th>
                            <th width="100">操作</th>
                        </tr>
                        </thead>
                        <tbody id="addspec">

                        </tbody>
                    </table>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>
                <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script src="{{loadEdition('/js/jquery.min.js')}}"></script>
<script>
    $(document).on('click','#addmuban',function () {
        // 清空addspec中的内容
        $('#addspec').empty();
        document.getElementById("specNmae").value="";
        $('#addMyModal').modal("show");
    })
    //添加规格
    $(document).on('click', '#add_spec', function () {
        var spec_list = $('#addspec');
        var spec_length = spec_list.find('tr').length;
        if(spec_length >= 4){
            layer.open({icon: 2, content: '规格最多可添加3个'});
            return;
        }
        var spec_item_div = '<tr data-index='+spec_length+'> ' +
            '<td> <div style="width: 100px;"><input type="text" class="w80" name="spec['+spec_length+'][name]" value=""></div> </td> ' +
            '<td> <div style=""> ' +
            '<input type="text" maxlength="25" placeholder="点击添加保存" autocomplete="off" class="spec_item_name" autocomplete="off" style="display: block;float: left;">' +
            '<a href="javascript:void(0);" class="add_spec_item" style="display: block;float: right">添加</a> </div> </td> <td class="handle-s"> <div style="text-align: center; width: 60px;">' +
            '<a href="javascript:void(0);" class="btn red delete_spec" ><i class="fa fa-trash-o"></i>删除</a></div> </td></tr>';
        spec_list.append(spec_item_div);
    });
    //删除规格
    $(document).on('click', '.delete_spec', function () {
        var obj = $(this);
        if (obj.data('id') > 0) {
            layer.open({
                content: '确认删除已存在的规格吗？'
                ,btn: ['确定', '取消']
                ,yes: function(index, layero){
                    layer.close(index);
                    $.ajax({
                        type: "POST",
                        url: '/index.php?m=Admin&c=Goods&a=deleteSpe',
                        data: {id: obj.data('id')},
                        dataType: "json",
                        success: function (data) {
                            if (data.status == 1) {
                                obj.parent().parent().parent().remove();
                            } else {
                                layer.open({icon: 2, content: data.msg});
                            }
                        }
                    });
                }
                ,btn2: function(index, layero){
                    layer.close(index);
                }
                ,cancel: function(){
                    //右上角关闭回调
                    layer.close();
                }
            });
        } else {
            obj.parent().parent().parent().remove();
        }
    });
    //添加规格值
    $(document).on('click', '.add_spec_item', function () {
        layer.msg("添加成功");
        var spec_item_name = $(this).parent().find(".spec_item_name").val();
        if ($.trim(spec_item_name) == ''){
            layer.open({icon: 2, content: '规格值名称不能为空'});
            return;
        }
        var spec_item_length = $(this).parent().find('.spec_item_button_div').length;
        if(spec_item_length >= 30){
            layer.open({icon: 2, content: '规格值最多可添加30个'});
            return;
        }
        var spec_index = $(this).parents('tr').data('index');
        var html = '<div class="spec_item_button_div" style="float: left"> ' +
            '<input type="text" class="w70" name="spec['+spec_index+'][item]['+spec_item_length+'][item]" value="'+spec_item_name+'" style="display: block;float: left;"> ' +
            '<span class="ys-btn-close delete_spec_item" style="cursor: pointer">×</span> ' +
            '</div>';
        $(this).parent().find(".spec_item_name").before(html).val('');
    });
    //删除规格值
    $(document).on('click', '.delete_spec_item', function () {
        var obj = $(this);
        if (obj.data('id') > 0) {
            layer.open({
                content: '确认删除已存在的规格值吗？'
                ,btn: ['确定', '取消']
                ,yes: function(index, layero){
                    layer.close(index);
                    $.ajax({
                        type: "POST",
                        url: '/index.php?m=Admin&c=Goods&a=deleteSpeItem',
                        data: {id: obj.data('id')},
                        dataType: "json",
                        success: function (data) {
                            if (data.status == 1) {
                                obj.parent().remove();
                            } else {
                                layer.open({icon: 2, content: data.msg});
                            }
                        }
                    });
                }
                ,btn2: function(index, layero){
                    layer.close(index);
                }
                ,cancel: function(){
                    //右上角关闭回调
                    layer.close();
                }
            });
        } else {
            obj.parent().remove();
        }
    });

    // 获取当前id
    $(document).on('click', '.type_info', function () {
        var type_id = $(this).data('id');
        add_edit_type(type_id);
    });
    function add_edit_type(type_id) {
        // 通过异步传输id
        $.post("{{route('shop.getAttr')}}",{id:type_id,_token:'{{ csrf_token() }}'},function (data) {
            // 将获取的字符串转换成对象
            var strToObj = JSON.parse(data);
            if(strToObj.code == 200){
                // 清空addspec中的内容
                $('#addspec').empty();
                // 获取模板名称
                var spec_name = strToObj.data.name;
                var spec_id = strToObj.data.id;
                // 向模板名称添加数据
                document.getElementById("specNmae").value=spec_name;
                document.getElementById("flag_id").value=spec_id;
                var goods_attr_value_data = strToObj.goods_attr_value_data;
                console.log(strToObj);
                // 判断商品规格属性是否存在
                if(strToObj.goods_attr_value_data.length){
                    // 存在规格属性
                    // js 新增规格
                    for (var i = 0 ;i<goods_attr_value_data.length;i++){
                        var spec_list = $('#addspec');
                        var spec_length = spec_list.find('tr').length;
                        if(spec_length >= 4){
                            layer.open({icon: 2, content: '规格最多可添加3个'});
                            return;
                        }
                        var attr = goods_attr_value_data[i].spec_value;
                        var attr_value = JSON.parse(attr);
                        console.log(attr_value);
                        var str = ""
                        for(var j=0;j<attr_value.length;j++){
                            str += '<div class="spec_item_button_div" style="float: left">'+
                                '<input type="text" class="w70" name="spec['+i+'][item]['+j+'][item]" value="'+attr_value[j]+'" style="display: block;float: left;"> ' +
                                '<span class="ys-btn-close delete_spec_item" style="cursor: pointer">×</span> </div>';
                        }

                        var spec_item_div = '<tr data-index='+spec_length+'>' +
                            '<td> <div style="width: 100px;"><input type="text" class="w80" name="spec['+spec_length+'][name]" value="'+goods_attr_value_data[i].spec+'"></div> </td> ' +
                            '<td> <div style=""> ' +str+
                            '<input type="text" maxlength="25" placeholder="点击添加保存" autocomplete="off" class="spec_item_name" autocomplete="off" style="display: block;float: left;">' +
                            '<a href="javascript:void(0);" class="add_spec_item" style="display: block;float: right">添加</a> </div> </td> <td class="handle-s"> <div style="text-align: center; width: 60px;">' +
                            '<a href="javascript:void(0);" class="btn red delete_spec" ><i class="fa fa-trash-o"></i>删除</a></div> </td></tr>';
                        spec_list.append(spec_item_div);
                    }
                }
            }else{
                console.log("no");
            }
        });
    }


</script>