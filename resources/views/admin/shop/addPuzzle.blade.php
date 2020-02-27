@extends('admin.layouts.layout')
<link href="{{loadEdition('/admin/plugins/layui/css/layui.css')}}">
<script src="{{loadEdition('/admin/plugins/layui/layui.all.js')}}"></script>
@section('css')
    <style>
        .animated{-webkit-animation-fill-mode: none;}
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>新增秒杀商品</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{route('shop.addPuzzleData')}}" method="POST">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品关键字</label>
                        <div class="input-group col-sm-2" style="float: left">
                            <input type="text" class="form-control" id="names">
                        </div><button style="float:left;margin-left: 20px;" class="btn btn-default" id="search" >搜索</button>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品名称</label>

                        <div class="input-group col-sm-2">
                            <select style="height: 25px;width: 273px;" name="goods_id" id="">
                                @if(empty($data))
                                    <option>请先搜索商品生成选项列表</option>
                                @else
                                  @if(empty($data[0]->name))
                                        <option>未搜索到商品</option>
                                      @else
                                        @foreach($data as $k=>$item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                      @endif
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品规格</label>
                        <div class="input-group col-sm-2">
                            <select style="height: 25px;width: 273px;" name="sku_id" id="skus">
                                @if(empty($sku))
                                    <option>请先搜索商品生成选项列表</option>
                                @else
                                    @foreach($sku[0] as $k=>$item)
                              <option value="<?php echo $item->id?>">
                                            @php
                          $sk_name  = $item->attr_value[0]->name;
                           $sk_val   = $item->attr_value[0]->value;
                                            @endphp
                                            @for($i=0;$i<count($sk_name);$i++)
                                                {{$sk_name[$i]}}
                                                {{$sk_val[$i]}}
                                            @endfor
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">活动开始时间：</label>
                        <div class="input-group col-sm-2">
                            <input type="datetime-local" name="begin_time" value="" >
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">活动结束时间：</label>
                        <div class="input-group col-sm-2">
                            <input type="datetime-local" name="finish_time" value="" >
                        </div>
                    </div>

                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">团购人数：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="top_member" value="" required data-msg-required="请输入商品邮费">
                        </div>
                    </div>

                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">单人团购数：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="single_limit" value="" required data-msg-required="请输入商品邮费">
                        </div>
                    </div>

                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">团购价格：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="price" value="" required data-msg-required="请输入商品邮费">
                        </div>
                    </div>

                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">团购库存：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="storage" value="" required data-msg-required="请输入商品邮费">
                        </div>
                    </div>

                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">团购规则：</label>
                        <div class="input-group col-sm-4">
                            <textarea type="text" rows="5" name="config" id="config" class="form-control" required data-msg-required="请输入跳转链接"></textarea>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <div class="col-sm-12 col-sm-offset-2">
                            @if(empty($data[0]->name))
                                <button class="btn btn-primary" disabled type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>
                                <button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                            @else
                                <button class="btn btn-primary"  type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>
                                <button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                            @endif
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
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
@section('footer-js')
    <script>
        function showicon(){
            layer.open({
                type: 1,
                title:'点击选择图标',
                area: ['800px', '80%'], //宽高
                anim: 2,
                shadeClose: true, //开启遮罩关闭
                content: $('#functions')
            });
        }

        $('.fontawesome-icon-list .fa-hover').find('a').click(function(){
            var str=$(this).text();
            $('#fonts').val( $.trim(str));
            layer.closeAll();
        })

        //搜索
        $("#search").click(function () {
            var search = $("#names").val();
            location.href="{{route('shop.addPuzzle')}}?name="+search
        })
    </script>
@endsection


