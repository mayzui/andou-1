<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Route;

class BaseController extends Controller {

    /**
     * 自动获取对应的模块名称和
     *
     * @param null  $view
     * @param array $data
     * @param array $mergeData
     *
     * @return Factory|View
     */
    public function view($view = null, $data = [], $mergeData = []) {
        $currentAction = $this->getCurrentAction();
        /**获取当前模块名称**/
        $module = $this->getModule();
        $controller = $currentAction['controller'];
        $action = $view ? $view : $currentAction['action'];
        $view_path = "{$module}.{$controller}.{$action}";

        return view($view_path, $data, $mergeData);
    }

    /**
     * 获取当前控制器名称
     *
     * @return mixed
     */
    public function getControllerName() {
        return $this->getCurrentAction()['controller'];
    }

    /**
     * 多图上传
     *
     * @return [type] [description]
     */
    public function uploads($files) {
        // $files=$all['imgs'];
        $count = count($files);
        $msg = [];
        // var_dump($files);exit;
        foreach ($files as $k => $v) {
            $type = $v->getClientOriginalExtension();
            $path = $v->getPathname();
            if ($type == "png" || $type == "jpg") {
                $newname = 'uploads/' . date("Ymdhis") . rand(0, 9999);
                $url = $newname . '.' . $type;
                $upload = move_uploaded_file($path, $url);
                $msg[] = $url;
            } else {
                return 0;
            }
        }
        return implode(',', $msg);
    }

    /**
     * 获取当前方法名称(小写)
     *
     * @return mixed
     */
    public function getActionName() {
        return $this->getCurrentAction()['action'];
    }

    /**
     * 获取当前控制器与方法(小写)
     *
     * @return array
     */
    public function getCurrentAction() {
        $action = Route::currentRouteName();
        [$controller, $action] = explode('.', $action);
        return ['controller' => $controller, 'action' => $action];
    }

    /**
     * 获取当前模块名称(小写)
     *
     * @return mixed
     */
    public function getModule() {
        $module = Request()->route()->action['prefix'];
        $module = explode('/', $module);
        return $module[1];
    }
}
