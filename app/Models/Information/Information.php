<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/3/1
 * Time: 14:28
 */

namespace App\Models\Information;

use App\Models\BaseModel;

class Information extends BaseModel {

    protected $table = 'information';
    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }

    /**
     * @param int $user_id
     * @param int $target_id
     *
     * @return bool
     */
    public function deleteInfo($user_id, $target_id) {
        return $this
            ->where('user_id', $user_id)
            ->where('target_id', $target_id)
            ->update(['status' => -1]) === false ? false : true;
    }

    /**
     * @param int $user_id
     * @param int $level
     * @param int $type_id
     *
     * @return array|\Illuminate\Support\Collection|null
     */
    public function getInfoCount($user_id, $level = 1, $type_id = null) {
        $infoType = InformationType::getInstance();
        if ($level == 1) {
            $data = [];
            $types = $infoType->where('parent_id', 0)->get(['id', 'type_name']);
            foreach ($types as $type) {
                $subTypeIds = $infoType->where('parent_id', $type->id)->pluck('id');
                if ($subTypeIds->count()) {
                    $unread = $this
                        ->where('user_id', $user_id)
                        ->where('read', 0)
                        ->whereIn('type_id', $subTypeIds)
                        ->count();
                } else {
                    $unread = 0;
                }
                $data[] = [
                    'id' => $type->id,
                    'type_name' => $type->type_name,
                    'unread' => $unread
                ];
            }
            return $data;
        }

        if ($type_id) {
            return $infoType
                ->leftJoin('information AS i', 'i.type_id', 'information_type.id')
                ->where('parent_id', $type_id)
                ->select(['information_type.id', 'type_name'])
                ->selectRaw('COUNT(i.read = ? OR NULL) AS unread', [0])
                ->groupBy(['information_type.id'])
                ->get();
        }

        return null;
    }

    /**
     * @param int $user_id
     * @param int $type_id
     * @param int $page
     *
     * @return array|null
     */
    public function getInfoList($user_id, $type_id, $page = 1) {
        $type = InformationType::getInstance()->find($type_id);
        switch ($type->parent_id) {
            case 1:
                break;
            case 2:

                switch ($type_id) {
                    case 3:
                        $joinMsg = '赞了您的帖子';
                        // 点赞
                        $list = $this
                            ->join('tieba_post_vote AS tpv', 'tpv.id', 'target_id')
                            ->join('tieba_post AS tp', 'tp.id', 'tpv.post_id')
                            ->join('users AS u', 'u.id', 'tpv.user_id')
                            ->where('information.user_id', $user_id)
                            ->where('information.type_id', $type_id)
                            ->where('information.status', 1)
                            ->selectRaw("information.id AS info_id, tp.id AS post_id,
                            CONCAT('{$this->domain}', u.avator) AS avator, u.name AS from_user, tp.title, read,
                            DATE_FORMAT(tpv.created_at, '?') AS created_at", ['%Y-%m-%d %H:%i'])
                            ->orderByDesc('information.created_at')
                            ->forPage($page, 10)
                            ->get();
                        break;
                    case 4:
                        // 评论
                        $joinMsg = '评论了您的帖子';
                        $list = $this
                            ->join('tieba_post AS tp', 'tp.id', 'target_id')
                            ->join('users AS u', 'u.id', 'tp.user_id')
                            ->where('information.user_id', $user_id)
                            ->where('information.type_id', $type_id)
                            ->where('information.status', 1)
                            ->selectRaw("information.id AS info_id, tp.id AS post_id,
                            CONCAT('{$this->domain}', u.avator) AS avator, u.name AS from_user, tp.title, read,
                            DATE_FORMAT(tp.created_at, '?') AS created_at", ['%Y-%m-%d %H:%i'])
                            ->forPage($page, 10)
                            ->get();
                        break;
                    case 5:
                        // 回复
                        $joinMsg = '回复了您';
                        $list = $this
                            ->join('tieba_post_comment AS tpc', 'tpc.id', 'target_id')
                            ->join('tieba_post AS tp', 'tp.id', 'tpc.post_id')
                            ->join('users AS u', 'u.id', 'tpc.user_id')
                            ->where('information.user_id', $user_id)
                            ->where('information.type_id', $type_id)
                            ->where('information.status', 1)
                            ->selectRaw("information.id AS info_id, tp.id AS post_id,
                            CONCAT('{$this->domain}', u.avator) AS avator, u.name AS from_user, tp.title, read,
                            DATE_FORMAT(tpc.created_at, '?') AS created_at", ['%Y-%m-%d %H:%i'])
                            ->forPage($page, 10)
                            ->get();
                        break;
                    default:
                        return null;
                }
                return [
                    'join_msg' => $joinMsg,
                    'list' => $list
                ];
        }
    }

    public function getInfo($user_id, $id) {
        return $this->where('user_id', $user_id)->find($id);
    }
}
