@extends('admin.layouts.layout')
@section('content')
    <style>
        th,td{
            text-align: center;
        }
    </style>
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-warning alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>属性列表</h5>
            </div>
            <div class="ibox-content">
                <a href="{{route('shop.addAttr')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"> 添加属性</button></a>
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th width="5%">属性id</th>
                        <th width="15%">属性名称</th>
                        <th  width="10%">是否是销售属性</th>
                        <th class="text-center" width="250">管理</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($list as $k=>$item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->name}}</td>
                            <td>
                                @if ($item->is_sale_attr)
                                    <span class="label label-success">是</span>
                                    @else
                                    <span class="label">否</span>
                                @endif

                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-primary btn-outline btn-xs" data-toggle="modal" onclick="setAttrId({{$item->id}})" data-target="#myModal">管理属性值</button>
                                <a href="{{route('shop.attrUpdate',$item->id)}}">
                                    <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button>
                                </a>
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

<div class="modal  fade" id="myModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title">属性管理</h3>
            </div>
            <form class="form-horizontal"  id="saveAttrValue" method="post" action="{{route('shop.saveAttrValue')}}">
                {!! csrf_field() !!}
                <div class="ibox-content">
                    <div class="hr-line-dashed"></div>
                    <input type="hidden" value="0" id="attrId" name="id">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">属性名称</label>
                        <div class="col-sm-8">
                            <input type="text" name="attrName" id="attrName"  class="form-control" value="" disabled/>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">值列表(双击删除) </label>
                        <span class="btn btn-info btn-sm" onclick="addinput()"><i class="fa fa-plus-circle"></i></span>
                        <div class="col-sm-8" id="attrValues">
{{--                            <input type="text" ondblclick="remevethis(this)" name="attr_value[]"  class="form-control value-num" value=""/>--}}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> 保存</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" ><i class="fa fa-close"></i> 关闭</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    // 设置id
    function  setAttrId (id)
    {
        document.getElementById('attrId').value = id;
        getAttrData(id)
    }

    function remevethis(obj) {
        return  $(obj).remove();
    }
    // 获取id对应的属性1数据
    function getAttrData (id) {
        $('#attrValues').html('');
        $.ajax({
            type: "POST",
            url: "/admin/shop/getAttr",
            data: {id : id },
            headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType:"JSON",
            success: function(res){
                if (res.code == 200) {
                    if (res.data && res.data.attr_value.length >0) {
                        console.log(res.data.attr_value);
                        for (i in  res.data.attr_value) {
                            var val = res.data.attr_value[i];
                            $('#attrValues').append("<input type=\"text\" ondblclick=\"remevethis(this)\" name=\"attr_value["+val.id+"]\"  class=\"form-control value-num\" value=\""+val.value+"\"/>");
                        }
                    } else {

                    }
                    $('#attrName').val(res.data.name)
                }
            }
        });
    }
    function addinput() {
        $('#attrValues').append("<input type=\"text\" ondblclick=\"remevethis(this)\" name=\"attr_value[]\"  class=\"form-control value-num\" value=\"\"/>");
    }

</script>