@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                @if(empty($activitydata->id))
                    <h5>新增活动</h5>
                @else
                    <h5>修改活动</h5>
                @endif
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{ route('shop.activityChange') }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{ $activitydata->id or '' }}" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label">活动名称：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="name" id="name" value="{{$activitydata->name or ''}}" required placeholder="请输入活动名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">请选择商品：</label>
                        <div class="input-group col-sm-2 checkbox">
                            @foreach($goodsdata as $k => $v)
                                <label><input type="checkbox" @if(in_array($v->id,$activityid)) checked="checked" @endif  name="goods[]" value="{{ $v->id }}" />{{ $v->name }}</label>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">开始时间：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control layui-input" name="create_time" value="{{ $activitydata -> create_time or '' }}" id="create_time">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">结束时间：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control layui-input" name="end_time" value="{{ $activitydata -> end_time or '' }}" id="end_time">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">是否开启：</label>
                        <select style="height: 25px;width: 273px;" name="status" id="status">
                            <option value="0" @if($activitydata-> status == 0) selected @endif >未开启</option>
                            <option value="1" @if($activitydata-> status == 1) selected @endif>开启</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 col-sm-offset-2">
                            <button class="btn btn-primary" type="button" id="saves" onclick="save()"><i class="fa fa-check"></i>&nbsp;保 存</button>　<button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
    <script>
        layui.use('laydate', function(){
            var laydate = layui.laydate;
            //执行一个laydate实例
            laydate.render({
                elem: '#create_time' //指定元素
                ,type: 'datetime'
            });
            //执行一个laydate实例
            laydate.render({
                elem: '#end_time' //指定元素
                ,type: 'datetime'
            });
        });
        function save(){
            var that = this;
            // 获取活动名称
            var name = document.getElementById('name').value;
            // 获取商品
            // 获取多选框中的数据
            var obj = document.getElementsByName('goods[]');
            var s = "";
            for (var i = 0; i < obj.length; i++) {
                if (obj[i].checked) s += obj[i].value + ',';
            }
            // 获取开始时间
            var create_time = document.getElementById('create_time').value;
            // 获取结束时间
            var end_time = document.getElementById('end_time').value;
            // 计算时间差
            var create_date = new Date(create_time).getTime();
            var end_date = new Date(end_time).getTime();

            // 判断
            if(!name.trim()){
                layer.alert("活动名称不能为空！",{icon:7},function (index) {
                    // document.getElementById('name').focus();
                    // 选中文本框
                    document.getElementById('name').select();
                    layer.close(index);
                });
            }else if(!s.trim()){
                layer.alert("请选择商品！",{icon:7});
            }else if(!create_time.trim()){
                layer.alert("请选择活动开始时间！",{icon:7},function (index) {
                    // 选中文本框
                    document.getElementById('create_time').select();
                    layer.close(index);
                });
            }else if(!end_time.trim()){
                layer.alert("请选择活动结束时间！",{icon:7},function (index) {
                    // 选中文本框
                    document.getElementById('end_time').select();
                    layer.close(index);
                });
            }else if(end_date - create_date <= 0){
                layer.alert("时间差错误！结束时间必须大于开始时间！",{icon:7});
            }else{
                document.getElementById('saves').setAttribute('type','submit');
            }
        }
    </script>
@endsection
