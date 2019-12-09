<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Libraires\ApiResponse;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    use ApiResponse;
    /**
     * @SWG\Post(path="/api/common/uploadImage",
     *   tags={"客户端-公共方法"},
     *   summary="上传图片",
     *   operationId="uploadImage",
     *   produces={"application/json"},
     *   @SWG\Parameter(in="formData",name="image[]",type="file",description="图片",required=true),
     *   @SWG\Response(response="default", description="操作成功")
     * )
     */
    public function up123 (Request $request)
    {
        if ($request->isMethod('post')) {
            try {


                $path = Storage::putFile('uploads', $request->file);
                return $this->status('ok',['path'=>$path,'showPath'=>"http://".$request->header('host') . DIRECTORY_SEPARATOR . $path],200);
            } catch (\Exception $e) {
                return $this->failed($e->getMessage());
            }
        }
    }


    public function uploadImage(Request $request)
    {
        $file = $request->file('file');
        //如果是有效的上传文件
        if($file->isValid()) {
            //            获取原文件的文件类型
            $ext = $file->getClientOriginalExtension();    //文件拓展名
            //        生成新文件名
            $newfile = md5(date('YmdHis').rand(1000,9999).uniqid()).'.'.$ext;

            try {
                $file->move('./uploads',$newfile);
                return $this->status('ok',['path'=>"/uploads/".$newfile , 'showUrl'=>$request->header('host') ."/uploads/".$newfile],200);
            } catch (\Exception $e) {
                return $this->failed($e->getMessage());

            }
        }
    }


    /**
     * 验证文件是否合法
     */
    public function upload($file, $disk='public') {
        // 1.是否上传成功
        if (! $file->isValid()) {
            return false;
        }

        // 2.是否符合文件类型 getClientOriginalExtension 获得文件后缀名
        $fileExtension = $file->getClientOriginalExtension();
        if(! in_array($fileExtension, ['png', 'jpg', 'gif'])) {
            return false;
        }

        // 3.判断大小是否符合 2M
        $tmpFile = $file->getRealPath();
        if (filesize($tmpFile) >= 2048000) {
            return false;
        }

        // 4.是否是通过http请求表单提交的文件
        if (! is_uploaded_file($tmpFile)) {
            return false;
        }

        // 5.每天一个文件夹,分开存储, 生成一个随机文件名
        $fileName = date('Y_m_d').'/'.md5(time()) .mt_rand(0,9999).'.'. $fileExtension;
        if (Storage::disk($disk)->put($fileName, file_get_contents($tmpFile)) ){
            return Storage::url($fileName);
        }
    }
}
