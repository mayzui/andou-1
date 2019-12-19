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
                <h5>参数详情列表</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <form class="form-horizontal m-t-md" action="{{ route('shop.storeComplateAttrs') }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input type="hidden" name="goods_id" value="{{ $goods_id or ''}}" />
                    <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th>ID</th>
                        @foreach($dataname as $v)
                            <th><input type="text" value="{{ $v }}" name="attr name[]" readonly style="border: 0px;width: 90px;"  /></th>
                        @endforeach
                        <th style="width: 300px">库存</th>
                        <th style="width: 300px">价格</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $k=>$item)
                        <tr class="remove">
                            <td>{{ $k+1 }}</td>
                            @foreach($item as $v)
                                <td><input type="text" value="{{ $v }}" name="value_{{ $k+1 }}[]" readonly style="border: 0px;width: 90px;"  /></td>
                            @endforeach
                            <td><input type="text" name="num[]" class="form-control" required style="border: 1px solid gray" /></td>
                            <td><input type="text" name="price[]" class="form-control" required style="border: 1px solid gray" /></td>
                            <td>
                                <a class="del" ><button class="btn btn-danger btn-xs" type="button" onclick="del(this)"><i class="fa fa-trash-o"></i> 删除</button></a>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="{{ count($dataname)+1 }}">
                            <div class="form-group">
                                <div class="col-sm-12 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit" ><i class="fa fa-check"></i>&nbsp;保 存</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>

                </table>
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    </div>
    </div>
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <script type="text/javascript">
        function del(obj) {
            var table=obj.parentNode.parentNode.parentNode.parentNode;
            table.removeChild(obj.parentNode.parentNode.parentNode);
        }
    </script>
@endsection
