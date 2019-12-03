<?php
/**
 * YICMS
 * ============================================================================
 * 版权所有 2014-2017 YICMS，并保留所有权利。
 * 网站地址: http://www.yicms.vip
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Created by PhpStorm.
 * Author: kenuo
 * Date: 2017/11/12
 * Time: 下午6:03
 */

namespace App\Handlers;
use Illuminate\Support\Facades\Storage;

class ImageUploadHandler
{
    // 只允许以下后缀名的图片文件上传
    protected $allowed_ext = ["png", "jpg", "gif", 'jpeg'];

    /**
     * @param $file  文件名称
     * @param $folder 空间名称
     * @return array|bool
     */
    public function save($file, $folder)
    {



//        //fetch all files of specified bucket(see upond configuration)
//        Storage::files($directory);
//        Storage::allFiles($directory);
//        Storage::put('path/to/file/file.jpg', $contents); //first parameter is the target file path, second paramter is file content
//        Storage::putFile('path/to/file/file.jpg', 'local/path/to/local_file.jpg'); // upload file from local path
//        Storage::get('path/to/file/file.jpg'); // get the file object by path
//        Storage::exists('path/to/file/file.jpg'); // determine if a given file exists on the storage(OSS)
//        Storage::size('path/to/file/file.jpg'); // get the file size (Byte)
//        Storage::lastModified('path/to/file/file.jpg'); // get date of last modification
//        Storage::directories($directory); // Get all of the directories within a given directory
//        Storage::allDirectories($directory); // Get all (recursive) of the directories within a given directory
//        Storage::copy('old/file1.jpg', 'new/file1.jpg');
//        Storage::move('old/file1.jpg', 'new/file1.jpg');
//        Storage::rename('path/to/file1.jpg', 'path/to/file2.jpg');
//        Storage::prepend('file.log', 'Prepended Text'); // Prepend to a file.
//        Storage::append('file.log', 'Appended Text'); // Append to a file.
//        Storage::delete('file.jpg');
//        Storage::delete(['file1.jpg', 'file2.jpg']);
//        Storage::makeDirectory($directory); // Create a directory.
//        Storage::deleteDirectory($directory); // Recursively delete a directory.It will delete all files within a given directory, SO Use with caution please.
//        Storage::putRemoteFile('target/path/to/file/jacob.jpg', 'http://example.com/jacob.jpg'); //upload remote file to storage by remote url
//
//        Storage::url('path/to/img.jpg'); // get the file url



        // 构建存储的文件夹规则，值如：uploads/images/avatars/201709/21/
        // 文件夹切割能让查找效率更高。
        $folder_name = "uploads/images/$folder/" . date("Ym", time()) . '/'.date("d", time()).'/';

        // 文件具体存储的物理路径，`public_path()` 获取的是 `public` 文件夹的物理路径。
        // 值如：/home/vagrant/Code/larabbs/public/uploads/images/avatars/201709/21/
        $upload_path = public_path() . '/' . $folder_name;

        // 获取文件的后缀名，因图片从剪贴板里黏贴时后缀名为空，所以此处确保后缀一直存在
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

        // 拼接文件名，加前缀是为了增加辨析度，前缀可以是相关数据模型的 ID
        // 值如：1_1493521050_7BVc9v9ujP.png
        $filename = time() . '_' . str_random(10) . '.' . $extension;

        // 如果上传的不是图片将终止操作
        if ( ! in_array($extension, $this->allowed_ext)) {
            return false;
        }

        // 将图片移动到我们的目标存储路径中
        $file->move($upload_path, $filename);

        return [
            'path' => "/$folder_name/$filename"
        ];
    }
}