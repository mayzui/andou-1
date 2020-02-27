@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('css')
    <style>
        .animated {
            -webkit-animation-fill-mode: none;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>编辑秒杀</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{route('seckill.edit')}}" method="POST">
                    <input type="hidden" name="id" value="{{$data['id']}}"/>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品名称</label>
                        <div class="input-group col-sm-2">
                            <input type="text" value="{{$gname->name}}" class="form-control" required
                                   data-msg-required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品规格</label>
                        <div class="input-group col-sm-2">
                            <select style="height: 25px;width: 273px;" name="sku_id" id="skus">　
                                @if(empty($kid))
                                    <option>暂无规格</option>
                                @else
                                    @foreach($gdata as $k=>$item)
                                        <option value="{{e($item->id)}}"
                                                @if($skudata->id == $item->id) selected @endif >
                                            @php
                                                $attr  = json_decode($skudata->attr_value);
                                                $attr_value =json_decode($item->attr_value);

                                            @endphp
                                            @if($skudata->id == $item->id)
                                                @for ($i=0;$i<count($attr[0]->name);$i++)
                                                    {{$attr[0]->name[$i]}}
                                                    {{$attr[0]->value[$i]}}
                                                @endfor
                                            @else
                                                @for ($i=0;$i<count($attr_value[0]->name);$i++)
                                                    {{$attr_value[0]->name[$i]}}
                                                    {{$attr_value[0]->value[$i]}}
                                                @endfor
                                            @endif
                                            @endforeach

                                        </option>
                                        @endif
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">开始时间：</label>
                        <div class="input-group col-sm-2">
                            <input type="datetime-local" class="form-control" class="one_time"
                                   value="{{$start_time or ''}}" name="start_time" placeholder="请选择时间">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">结束时间：</label>
                        <div class="input-group col-sm-2">
                            <input type="datetime-local" class="form-control" class="end_at" value="{{$end_time or ''}}"
                                   name="end_time" placeholder="请选择时间">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">秒杀价格：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" name="kill_price" value="{{$data['kill_price']}}" class="form-control"
                                   required data-msg-required="">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">秒杀库存：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" name="num" value="{{$data['num']}}" class="form-control" required
                                   data-msg-required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">秒杀规则：</label>
                        <div class="input-group col-sm-2">
                            <textarea name="kill_rule" id="" cols="60" rows="6">{{$data['kill_rule'] or ''}}</textarea>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <div class="col-sm-12 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>
                            <button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    {{csrf_field()}}

                </form>
            </div>
        </div>
    </div>
    <div id="functions" style="display: none;">
        @include('admin.rules.fonticon')
    </div>
@section('footer-js')
    <script>

        function showicon() {
            layer.open({
                type: 1,
                title: '点击选择图标',
                area: ['800px', '80%'], //宽高
                anim: 2,
                shadeClose: true, //开启遮罩关闭
                content: $('#functions')
            });
        }

        $('.fontawesome-icon-list .fa-hover').find('a').click(function () {
            var str = $(this).text();
            $('#fonts').val($.trim(str));
            layer.closeAll();
        })
    </script>
@endsection


