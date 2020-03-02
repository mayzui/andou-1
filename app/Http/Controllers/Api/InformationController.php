<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/2/27
 * Time: 12:09
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Information\Information;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InformationController extends Controller {

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @api {get} /api/info/list 信息列表
     * @apiName list
     * @apiGroup info
     * @apiParam {Number} uid
     * @apiParam {Number=1,2,3} [level=1] 列表级数
     * @apiParam {Number} [type_id] 信息类型 ID，level 为 2 或 3 时必传
     * @apiParam {Number} [page=1]
     * @apiSuccessExample {json} Success-Response:
     * {}
     *
     */
    public function list(Request $request) {
        $data = $this->validate($request, [
            'uid' => 'required|numeric|exists:users,id',
            'level' => 'filled|numeric|in:1,2,3',
            'type_id' => 'required_if:level,2,3|numeric|exists:information_type,id',
            'page' => 'nullable|numeric|min:1'
        ]);

        if (!isset($data['level'])) {
            $data['level'] = 1;
            $data['type_id'] = null;
        }

        if ($data['level'] != 3) {
            $count = Information::getInstance()->getInfoCount($data['uid'], $data['level'], $data['type_id']);
            return $this->responseJson(200, 'OK', ['count' => $count]);
        }

        $data['page'] = isset($data['page']) && $data['page'] > 1 ? $data['page'] : 1;

        $list = Information::getInstance()->getInfoList($data['uid'], $data['type_id'], $data['page']);
        return $this->responseJson(200, 'OK', ['list' => $list]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @api {post} /api/info/read 更新已读
     * @apiName read
     * @apiGroup info
     * @apiParam {Number} uid
     * @apiParam {Number} info_id
     * @apiSuccessExample {json} Success-Response:
     * {}
     */
    public function read(Request $request) {
        $data = $this->validate($request, [
            'uid' => 'required|numeric|exists:users,id',
            'info_id' => 'required|numeric|exists,information,id'
        ]);

        $info = Information::getInstance()->getInfo($data['uid'], $data['info_id']);
        if (!$info || $info->update([
                'read' => 1,
                'read_at' => Carbon::now()->toDateTimeString()
            ])) {
            Log::error("更新消息已读状态失败，用户 ID：{$data['uid']}，消息 ID：{$data['info_id']}");

        }

        return $this->responseJson(200, 'OK');
    }
}
