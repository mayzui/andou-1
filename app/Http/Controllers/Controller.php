<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /**
     * 返回json数据
     */
    public function rejson($code = 0, $msg = '', $data = '')
    {
        $response = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
        exit(json_encode($response));
    }
}
