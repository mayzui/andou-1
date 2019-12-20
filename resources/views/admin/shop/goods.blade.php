@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>商品管理</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <a href="{{route('shop.create')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                        <i class="fa fa-plus-circle"></i> 新增商品</button>
                </a>
                <form method="post" action="{{route('shop.index')}}" name="form">
                    <style>
                        th ,td{
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th width="100">ID</th>
                            <th>名称</th>
                            <th>分类</th>
                            <th>图片</th>
                            <th>描述</th>
                            <th>热门</th>
                            <th>推荐</th>
                            <th>上架</th>
                            <th>特价</th>
                            <th>邮费</th>
                            <th>点击量</th>
                            <th>创建时间</th>
                            <th>更新时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->merchant_name}}</td>
                                <td>{{$item->goods_cate_id}}</td>
                                <td><img src="{{ env('IMAGE_PATH_PREFIX')}}{{$item->img}}" alt="" style="width: 50px;height: 50px;"></td>
                                <td>{{$item->desc}}</td>
                                <td>
                                    @if ($item->is_hot == 1)
                                        <span class="text-info">热卖</span>
                                    @else
                                        <span class="text-danger">普通</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->is_recommend == 1)
                                        <span class="text-info">推荐</span>
                                    @else
                                        <span class="text-danger">普通</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->is_sale == 1)
                                        <span class="text-info">上架</span>
                                    @else
                                        <span class="text-danger">未上架</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->is_bargain == 1)
                                        <span class="text-info">特价</span>
                                    @else
                                        <span class="text-danger">非特价</span>
                                    @endif
                                </td>
                                <td>{{$item->dilivery}}</td>
                                <td>{{$item->pv}}</td>
                                <td>{{$item->created_at}}</td>
                                <td>{{$item->updated_at}}</td>
                                <td class="text-center">
                                    <div class="btn-group">

                                        @if($item->is_hot == 0)
                                            <a href="{{route('shop.setStatus',['field'=>'is_hot','status'=>1,'id'=>$item->id])}}"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-warning"></i>热卖品</button></a>
                                        @else
                                            <a href="{{route('shop.setStatus',['field'=>'is_hot','status'=>0,'id'=>$item->id])}}"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-warning"></i>非热卖</button></a>
                                        @endif

                                        @if($item->is_recommend == 0)
                                            <a href="{{route('shop.setStatus',['field'=>'is_recommend','is_recommend'=>1,'id'=>$item->id])}}"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-warning"></i> 推荐</button></a>
                                        @else
                                            <a href="{{route('shop.setStatus',['field'=>'is_recommend','is_recommend'=>0,'id'=>$item->id])}}"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-warning"></i> 不推荐</button></a>
                                        @endif

                                        @if($item->is_sale == 0)
                                            <a href="{{route('shop.setStatus',['field'=>'is_sale','is_sale'=>1,'id'=>$item->id])}}"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-warning"></i> 上架</button></a>
                                        @else
                                            <a href="{{route('shop.setStatus',['field'=>'is_sale','is_sale'=>0,'id'=>$item->id])}}"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-warning"></i>下架</button></a>
                                        @endif
                                        @if($item->is_bargain == 0)
                                            <a href="{{route('shop.setStatus',['field'=>'is_bargain','is_bargain'=>1,'id'=>$item->id])}}"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-warning"></i> 特价</button></a>
                                        @else
                                            <a href="{{route('shop.setStatus',['field'=>'is_bargain','is_bargain'=>0,'id'=>$item->id])}}"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-warning"></i>非特价</button></a>
                                        @endif

                                        <a href="{{route('shop.update')}}?id={{$item->id}}">
                                            <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button>
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
        </div>
        <div class="clearfix"></div>
    </div>
    <script type="text/javascript">
        function del(e) {
            var id = e;
            layer.alert("是否删除该数据？",{icon:3},function (index) {
                location.href="{{route('shop.goodsDel')}}?id="+id;
                layer.close(index);
            });
        }
    </script>
@endsection