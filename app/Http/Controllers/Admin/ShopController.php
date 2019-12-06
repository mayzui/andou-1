<?php

namespace App\Http\Controllers\Admin;
use App\Handlers\Tree;
use App\Models\GoodBrands;
use App\Models\GoodsCate;
use Auth;

use App\Http\Requests\Admin\ShopRequest;
use App\Services\ActionLogsService;
use Illuminate\Http\Request;
use App\Services\ShopService;
use App\Repositories\ShopRepository;
use Illuminate\Support\Facades\Validator;


class ShopController extends BaseController
{

    protected $shopService;

    /**
     * ActionLogsController constructor.
     * @param $actionLogsService
     */
    public function __construct(ShopService $shopService)
    {
        $this->shopService = $shopService;
    }

    public function index ()
    {

        return $this->view(null);
    }

    public function goods(Auth $auth)
    {

        $admin = Auth::guard('admin')->user();

        dd($admin->id);


        return $this->view(null);
    }

    public function goodsCate (Request $request)
    {
        $list = GoodsCate::select('id','name','img','sort','pid')
            ->orderBy('sort','asc')
            ->orderBy('pid','asc')
            ->get();


        $list = Tree::tree($list->toArray(),'name','id','pid');
        return $this->view('goodsCate',['list'=>$list]);
    }

    public function cateAdd (Request $request)
    {
        $list = GoodsCate::select('id','name','sort','pid')
            ->where('level','<','3')
            ->orderBy('sort','asc')
            ->orderBy('pid','asc')
            ->get();
        $list = Tree::tree($list->toArray(),'name','id','pid');
        return $this->view('cateAdd',['list'=>$list]);
    }

    public function cateEdit (Request $request,$id)
    {
        $cate = GoodsCate::find($id);
        $list = GoodsCate::select('id','name','sort','pid')
            ->where('level','<','3')
            ->orderBy('sort','asc')
            ->orderBy('pid','asc')
            ->get();
        $list = Tree::tree($list->toArray(),'name','id','pid');
        return $this->view('cateEdit',['list'=>$list,'cate'=>$cate]);

    }

    public function cateDelete(Request $request ,$id)
    {
        $model = GoodsCate::find($id);
        if ($model->delete()){
            return redirect()->route('shop.goodsCate');
        }
        return viewError('已删除或者删除失败');
    }
    public function cateStore (Request $request)
    {
        $validate = Validator::make($request->all(),[
            'name' => 'required',
            'sort' => 'required|numeric',
            'img' => 'required',
            'pid'=>'required',
        ],[
            'name.required'=>'名称必须',
            'sort.numeric'=>'排序必须是数字',
            'img.required'=>'请上传图片',
            'pid.required'=>'缺少上级'
        ]);


        if ($validate->fails()) {
            flash($validate->errors()->first())->error()->important();
            return redirect()->route('shop.cateAdd');
        }

        $model = new GoodsCate();
        if ($request->input('id')) {
            $model = GoodsCate::find($request->input('id'));
            if ($model->pid != $request->input('pid')){
                flash('操作失败，不能更改分类的上下级关系')->error()->important();
                return redirect()->route('shop.goodsCate');
            }
        }

        $model->name = $request->input('name');
        $model->sort = $request->input('sort');

        // 等级判断
        $model->level = 1;
        $model->roots = 0;
        if  ($request->input('pid') > 0) {
            $pmodel = GoodsCate::find($request->input('pid'));
            $model->level = ++$pmodel->level;
            $model->roots = $pmodel->roots . ',' .$pmodel->id;
        }

        $model->pid = $request->input('pid');
        $model->img = $request->input('img');

        if ($model->save()) {
            return   redirect()->route('shop.goodsCate');
        }
        return  viewError('操作失败','shop.cateAdd');
    }



    public function orders ()
    {
        dd(123);

        return $this->view(null);
    }

    public function goodsBrand (Request $request)
    {
        $list = GoodBrands::orderBy('id','desc')->paginate($request->input('limit'));
        return $this->view('goodsBrand',['list'=>$list]);
    }

    public function brandAdd (Request $request)
    {
        return $this->view('brandAdd');
    }


    public function brandUpdate (Request $request,$id)
    {
        $brand = GoodBrands::find($id);
        return $this->view('brandUpdate',['brand'=>$brand]);
    }



    public function brandStore (Request $request)
    {
        $validate = Validator::make($request->all(),[
            'name' => 'required',
            'img' => 'required',
        ],[
            'name.required'=>'排序必须是数字',
            'img.required'=>'请上传图片',
        ]);


        if ($validate->fails()) {
            flash($validate->errors()->first())->error()->important();
            return redirect()->route('shop.brandAdd');
        }

        $model = new GoodBrands();
        if ($request->input('id')) {
            $model = GoodBrands::find($request->input('id'));
        }
        $model->name = $request->input('name');
        $model->img = $request->input('img');

        if ($model->save()) {
            return   redirect()->route('shop.goodsBrand');
        }
        return  viewError('操作失败','shop.brandAdd');
    }


    public function brandDelete (Request $request ,$id)
    {
        $model = GoodBrands::find($id);
        if ($model->delete()){
            return redirect()->route('shop.goodsBrand');
        }
        return viewError('已删除或者删除失败');
    }
}
