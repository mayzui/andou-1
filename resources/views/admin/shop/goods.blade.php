@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#home" data-toggle="tab">产品列表</a></li>
                <li><a href="#profile" data-toggle="tab">产品分类</a></li>
            </ul>
            <div class="tab-content" style="z-index: auto">
                {{--产品列表--}}
                <div class="ibox-content tab-pane active a" id="home">
                    <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                    <button type="button" class="btn btn-danger btn-sm mdels" title="批量删除" ><i class="fa fa-trash-o"></i> 批量删除</button>
                    <a href="{{route('shop.create')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            <i class="fa fa-plus-circle"></i> 新增商品</button>
                    </a>
                    <form method="post" action="{{route('shop.index')}}" name="form">
                        <style>
                            th{
                                text-align: center;
                            }
                            #home td{
                                text-align: center;
                            }
                        </style>
                        <table class="table table-striped table-bordered table-hover m-t-md">
                            <thead>
                            <tr>
                                <th><input type="checkbox" id="checkall" /></th>
                                <th style="width: 250px">产品名称</th>
                                <th width="200px">产品类目</th>
                                <th>产品图片</th>
                                <th>是否上架</th>
                                <th>库存</th>
                                <th>销量</th>
                                <th>基础价格</th>
                                <th width="200px">上架时间</th>
                                {{--<th>更新时间</th>--}}
                                <th width="250px">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list as $k => $item)
                                <tr>
                                    <td><input type="checkbox" name="ids" value="{{$item->id}}" /></td>
                                    <td><p style="width: 250px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">{{$item->goods_name}}</p></td>
                                    <td>{{$item->goods_cate_id}}</td>
                                    <td><img src="{{ env('IMAGE_PATH_PREFIX')}}{{$item->img}}" alt="" style="width: 55px;height: 55px;"></td>
                                    <td>
                                        @if ($item->is_sale == 1)
                                            <span class="text-info">是</span>
                                        @else
                                            <span class="text-danger">否</span>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($goods_sku as $v)
                                            @if(in_array($item -> id,$v))
                                                {{ $v['total'] }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>{{$item->volume}}</td>
                                    <td>{{$item->price}}</td>
                                    <td>{{$item->created_at}}</td>
                                    {{--<td>{{$item->updated_at}}</td>--}}
                                    <td class="text-center">
                                        <div class="btn-group">
                                            @if($item->is_sale == 0)
                                                <a href="{{route('shop.setStatus',['field'=>'is_sale','is_sale'=>1,'id'=>$item->id])}}"><button class="btn btn-outline btn-info btn-xs" type="button"><i class="fa fa-warning"></i> 上架</button></a>
                                            @else
                                                <a href="{{route('shop.setStatus',['field'=>'is_sale','is_sale'=>0,'id'=>$item->id])}}"><button class="btn btn-outline btn-warning btn-xs" type="button"><i class="fa fa-warning"></i>下架</button></a>
                                            @endif
                                            <a href="{{route('shop.update')}}?id={{$item->id}}">
                                                <button class="btn btn-outline btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 编辑</button>
                                            </a>
                                            <a onclick="del({{$item->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{$list}}
                    </form>
                </div>
                {{--产品分类--}}
                <div class="ibox-content tab-pane" id="profile">
                    <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                    <button type="button" class="btn btn-danger btn-sm mdels" title="批量删除" ><i class="fa fa-trash-o"></i> 批量删除</button>
                    <a href="{{route('shop.merchants_goods_typeChange')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                            <i class="fa fa-plus-circle"></i> 新增分类</button>
                    </a>

                    <form method="post" action="{{route('shop.express')}}" name="form">
                        <table class="table table-striped table-bordered table-hover m-t-md">
                            <thead>
                            <tr>
                                <th width="50px"><input type="checkbox" id="checkall" /></th>
                                <th width="150px">分类ID</th>
                                <th width="250px">商家名称</th>
                                <th>分类名称</th>
                                <th width="200px">产品数</th>
                                <th width="200px">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($data) > 0)
                                @foreach($data as $k => $item)
                                    <tr>
                                        <td><input type="checkbox" name="ids" value="{{$item['id']}}" /></td>
                                        <td>{{$item['id']}}</td>
                                        <td>{{$item['merchants_name']}}</td>
                                        <td>{{$item['_name']}}</td>
                                        <td>{{$item['num']}}</td>
                                        <td>
                                            <a href="{{route('shop.merchants_goods_typeChange')}}?id={{$item['id']}}">
                                                <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 编辑</button>
                                            </a>
                                            <a onclick="del({{$item['id']}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <th colspan="4">暂时没有查询到数据</th>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <script type="text/javascript">
        function del(e) {
            var id = e;
            layer.alert("是否删除该数据？",{icon:3},function (index) {
                location.href="{{route('shop.goodsDel')}}?id="+id;
                layer.close(index);
            });
        }
        //执行批量删除
        $(".mdels").click(function () {
            var obj = document.getElementsByName("ids");
            var check_val = [];
            for(k in obj){
                if(obj[k].checked)
                    check_val.push(obj[k].value);
            }
            if(check_val==""){
                layer.alert("请选择你需要删除的选项",{icon:2});
            }else {
                layer.confirm("是否删除这 "+check_val.length+" 项数据？", {icon: 3}, function (index) {
                    $.post("{{route('shop.deleteAll')}}", {ids: check_val, _token: "{{csrf_token()}}"}, function (data) {
                        if (data = 1) {
                            layer.alert("删除成功", {icon: 1}, function (index) {
                                window.location.href = "{{route('shop.goods')}}";
                            });
                        }
                    })

                })
            }
        })
        // 实现全选
        $("#checkall").click(function () {
            if(this.checked){
                $("[name=ids]:checkbox").prop("checked",true);
            }else{
                $("[name=ids]:checkbox").prop("checked",false);
            }
        })
    </script>
@endsection