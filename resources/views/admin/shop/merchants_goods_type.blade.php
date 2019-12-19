@extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>商品分类</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>

                <a href="{{route('shop.merchants_goods_typeChange')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button">
                        <i class="fa fa-plus-circle"></i>新增分类</button>
                </a>

                <form method="post" action="{{route('shop.express')}}" name="form">
                    <style>
                        th ,td{
                            text-align: center;
                        }
                    </style>
                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th>分类ID</th>
                            <th>商家名称</th>
                            <th>分类名称</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($data) > 0)
                        @foreach($data as $k => $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->merchants_name}}</td>
                                <td>{{$item->name}}</td>
                                <td>
                                    <a href="{{route('shop.merchants_goods_typeChange')}}?id={{$item->id}}">
                                        <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button>
                                    </a>
                                    <a onclick="del({{$item->id}})"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
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
                    {{$data}}
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <script type="text/javascript">
        function del(e) {
            var id = e;
            layer.alert("是否删除该数据？",{icon:3},function (index) {
                location.href="{{route('shop.merchants_goods_typeDel')}}?id="+id;
                layer.close(index);
            });
        }
    </script>
@endsection