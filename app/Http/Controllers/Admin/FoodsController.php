<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Role;
use App\Repositories\RulesRepository;
use App\Handlers\Tree;
class FoodsController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    
    /*
     *      菜品详情
     * */
    //跳转菜品详情界面
    public function information(){
        $id = Auth::id();
        // 判断该用户，是否开店
        $i = DB::table('merchants') -> where("user_id",$id) -> first();
        if(!empty($i)){
            // 如果开店，则能够看到自己的菜品详情
            // 查询数据库数据
            $data = DB::table("foods_information") -> where('merchant_id',$id) -> paginate(10);
        }else{
            // 如果开店，则为超级管理员，能够看见所有的数据
            // 查询数据库数据
            $data = DB::table("foods_information") -> paginate(10);
            $id = "";
        }
        return $this -> view('',['data'=>$data,'id'=>$id]);
    }
    // 新增菜品详情
    public function informationadd(){
        if(\request()->isMethod("get")){
            $all = \request() -> all();
            // 判断跳转新增界面还是修改界面
            if(empty($all['id'])) {
                // 跳转新增界面
                // 链接数据库，查询菜品分类
                $type = DB::table("foods_classification") -> get();
                // 链接数据库，查询菜品规格
                $spec = DB::table("foods_spec") -> get();
                $data = (object)[
                    "classification_id" => 0
                ];
                // 定义一个传值用的数组
                $arr = [
                    'data' => $data,
                    'type' => $type,
                    'spec' => $spec
                ];
                return $this -> view('',$arr);
            }else{
                // 跳转修改界面
                // 根据获取的id 查询数据库中的值
                $data = DB::table("foods_information") -> where("id",$all['id']) -> first();
                // 链接数据库，查询菜品分类
                $type = DB::table("foods_classification") -> get();
                // 链接数据库，查询菜品规格
                $spec = DB::table("foods_spec") -> get();
                // 获得查询出来的菜品规格
                $data_spec =$data->specifications;
                // 定义一个传值用的数组
                $arr = [
                    'data' => $data,
                    'data_spec' => explode(",",$data_spec),
                    'type' => $type,
                    'spec' => $spec
                ];
                return $this -> view('',$arr);
            }

        }else{
            $all = \request() -> all();
            // 判断用户执行的是新增操作还是修改操作
            if(empty($all['id'])){
                // 执行新增操作
                $spec = $all['specifications'];
                // 将数组转换成字符串
                $specs = implode(",",$spec);
                // 定义一个数组用于接收需要上传数据库的值
                $data = [
                    "merchant_id" => Auth::id(),
                    "classification_id" => $all['classification_id'],
                    "name" => $all['name'],
                    "price" => $all['price'],
                    "image" => $all['img'],
                    "specifications" => $specs,
                    "remark" => $all['remark']
                ];
                // 链接数据库，新增数据
                $i = DB::table("foods_information") -> insert($data);
                if($i){
                    flash('新增成功') -> success();
                    return redirect()->route('foods.information');
                }else{
                    flash('新增失败') -> error();
                    return redirect()->route('foods.information');
                }
            }else{
                // 执行修改操作
                // 获取提交的值
                $spec = $all['specifications'];
                // 将数组转换成字符串
                $specs = implode(",",$spec);
                // 定义一个数组用于接收需要上传数据库的值
                $data = [
                    "merchant_id" => Auth::id(),
                    "classification_id" => $all['classification_id'],
                    "name" => $all['name'],
                    "price" => $all['price'],
                    "image" => $all['img'],
                    "specifications" => $specs,
                    "remark" => $all['remark'],
                    "quantitySold" => $all['quantitySold'],
                    "num" => $all['num']
                ];
                // 执行修改操作
                $i = DB::table("foods_information") -> where("id",$all['id']) -> update($data);
                if($i){
                    flash('修改成功') -> success();
                    return redirect()->route('foods.information');
                }else{
                    flash('修改失败') -> error();
                    return redirect()->route('foods.information');
                }
            }

        }

    }

    // 删除菜品信息
    public function informationdel(){
        // 获取传入的数据
        $all = \request()->all();
        // 根据id 删除表中数据
        $i = DB::table("foods_information") -> where("id",$all['id']) -> delete();
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('foods.information');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('foods.information');
        }
    }


    /*
     *      菜品规格
     * */
    // 跳转菜品规格
    public function spec(){
        $id = Auth::id();
        // 判断该用户，是否开店
        $i = DB::table('merchants') -> where("user_id",$id) -> first();
        if(!empty($i)){
            // 如果开店，则能够看到自己的菜品规格
            // 查询数据库数据
            $data = DB::table("foods_spec") -> where('merchant_id',$id) -> paginate(10);
        }else{
            // 如果开店，则为超级管理员，能够看见所有的数据
            // 查询数据库数据
            $data = DB::table("foods_spec") -> paginate(10);
            $id = "";
        }
        return $this -> view('',['data'=>$data,'id'=>$id]);
    }
    // 新增 and 修改，菜品规格
    public function specadd(){
        if(\request()->isMethod("get")){
            $all = \request() -> all();
            // 判断跳转新增界面还是修改界面
            if(empty($all['id'])){
                // 跳转新增界面
                return $this -> view('');
            }else{
                // 跳转修改界面
                // 根据当前获取的id，查询数据库中的值
                $data = DB::table("foods_spec") -> where("id",$all['id']) -> first();
                // 定义一个数组用于向前台传输数据
                $arr = [
                    'data'=>$data,
                    'id'=>$all['id']
                ];
                return $this -> view('',$arr);
            }

        }else{
            // 获取传入的值
            $all = \request()->all();
            // 判断用户执行的是新增操作还是修改操作
            if(empty($all['id'])){
                // 执行新增操作
                // 获取当前商户id
                $id = Auth::id();
                // 判断数据库是否存在同样的规格
                $m = DB::table("foods_spec") ->where('name',$all['name']) -> first();
                if(empty($m)){
                    // 数据库不存在该值,则进行新增操作
                    // 定义一个数组，用于向数据库添加数据
                    $data = [
                        'merchant_id' => $id,
                        'name' => $all['name']
                    ];
                    //链接数据库，执行新增操作
                    $i = DB::table("foods_spec") ->insert($data);
                    if($i){
                        flash('新增成功') -> success();
                        return redirect()->route('foods.spec');
                    }else{
                        flash('新增失败') -> error();
                        return redirect()->route('foods.spec');
                    }
                }else{
                    // 数据库存在该值，则提示商户，该规格已存在
                    flash('新增失败，该规格已存在！') -> error();
                    return redirect()->route('foods.spec');
                }

            }else{
                // 执行修改操作
                // 判断数据库是否存在同样的规格
                $m = DB::table("foods_spec") ->where('name',$all['name']) -> first();
                if(empty($m)){
                    // 数据库不存在该值,则进行修改操作
                    // 定义一个数组，用于向数据库添加数据
                    $data = [
                        'name' => $all['name']
                    ];
                    $i = DB::table("foods_spec") -> where("id",$all['id']) -> update($data);
                    if($i){
                        flash('修改成功') -> success();
                        return redirect()->route('foods.spec');
                    }else{
                        flash('修改失败') -> error();
                        return redirect()->route('foods.spec');
                    }
                }else{
                    // 数据库存在该值，则提示商户，该规格已存在
                    flash('修改失败，该规格已存在！') -> error();
                    return redirect()->route('foods.spec');
                }
            }
        }
    }
    // 删除菜品规格
    public function specdel(){
        // 获取传入的数据
        $all = \request()->all();
        // 根据id 删除表中数据
        $i = DB::table("foods_spec") -> where("id",$all['id']) -> delete();
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('foods.spec');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('foods.spec');
        }
    }



    /**
     *      商户菜品分类
     */
    // 跳转菜品分类模块
    public function index()
    {
        // 判断当前用户是否是酒店用户
        $id = Auth::id();
        // 判断该用户，是否开店
        $i = DB::table('merchants') -> where("user_id",$id) -> first();

        if(!empty($i)){
            // 如果是酒店用户，只能看见自己的菜品分类
            $data = DB::table("foods_classification")->where('merchants_id',$id)->paginate(10);
        }else{
            // 查询数据库中商户菜品分类表
            $data = DB::table("foods_classification")->paginate(10);
            $id = "";
        }
        return $this->view('',['data'=>$data,'id'=>$id]);
    }

    // 新增菜品分类
    public function add(){
        $all = \request()->all();
        if(\request()->isMethod("get")){
            // 判断是跳转新增界面，跳转修改界面
            if(!empty($all)){
                // 跳转修改界面
                // 获取传入的id
                $id = $all['id'];
                // 链接数据库，根据id查询数据
                $data = DB::table("foods_classification") -> where("id",$id) -> first();
                // 将获取的数据传入前台
                return $this->view('',['data'=>$data]);
            }else{
                // 跳转新增界面
                //获取当前商户id
                $id = Auth::id();
                //跳转新增菜品界面
                return $this -> view('');
            }
        }else{
            // 获取传入的值
            $all = \request()->all();
            $name = $_POST['name'];
            // 判断用户执行的是新增操作还是修改操作
            if(empty($all['id'])){
                // 新增操作
                // 判断数据库是否存在同样的菜品分类
                $m = DB::table("foods_classification") -> where("name",$name) -> first();
                if(empty($m)){
                    // 该分类不存在可以新增
                    // 定义一个数组存放获取的值
                    $data = [
                        "merchants_id" => Auth::id(),
                        "name" => $name
                    ];
                    // 链接数据库，新增数据
                    $i = DB::table("foods_classification") -> insert($data);
                    if($i){
                        flash('新增成功') -> success();
                        return redirect()->route('foods.index');
                    }else{
                        flash('新增失败') -> error();
                        return redirect()->route('foods.index');
                    }
                }else{
                    // 该分类已经存在，不能新增
                    flash('新增失败，该分类已经存在！') -> error();
                    return redirect()->route('foods.index');
                }

            }else{
                // 修改操作
                // 判断数据库是否存在同样的菜品分类
                $m = DB::table("foods_classification") -> where("name",$name) -> first();
                if(empty($m)){
                    // 定义一个数组存放获取的值
                    $data = [
                        "name" => $name
                    ];
                    // 链接数据库，执行修改操作
                    $i = DB::table("foods_classification") -> where("id",$all['id']) -> update($data);
                    if($i){
                        flash('修改成功') -> success();
                        return redirect()->route('foods.index');
                    }else{
                        flash('修改失败') -> error();
                        return redirect()->route('foods.index');
                    }
                }else{
                    flash('修改失败，该分类已经存在！') -> error();
                    return redirect()->route('foods.index');
                }

            }

        }
    }

    // 删除菜品分类
    public function del(){
        // 获取传入的id
        $id = $_GET['id'];
        // 根据id 删除表中数据
        $i = DB::table("foods_classification")->where('id',$id)->delete();
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('foods.index');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('foods.index');
        }
    }

}