<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/2/27
 * Time: 15:48
 */

namespace App\Models\Tieba;

use App\Models\BaseModel;
use App\Models\Information\Information;
use Exception;
use Illuminate\Support\Facades\DB;

class PostVote extends BaseModel {

    protected $table = 'tieba_post_vote';
    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }

    /**
     * @param int  $user_id
     * @param int  $post_id
     * @param bool $vote
     *
     * @return bool
     * @throws Exception
     */
    public function vote($user_id, $post_id, $vote = true) {
        DB::beginTransaction();
        $post = Post::find($post_id);
        if ($vote) {
            $voteId = $this->insertGetId(['user_id' => $user_id, 'post_id' => $post_id]);
            if ($voteId && $post->increment('vote')) {
                if ($user_id != $post->user_id) {
                    // 写通知信息
                    if (Information::getInstance()->insert([
                        'type_id' => 3,
                        'user_id' => $post->user_id,
                        'target_id' => $voteId
                    ])) {
                        DB::commit();;
                        return true;
                    }
                } else {
                    DB::commit();;
                    return true;
                }
            }
        } else if ($this->where('user_id', $user_id)->where('post_id', $post_id)->delete()
            && $post->decrement('vote')) {

            if ($user_id != $post->user_id) {
                // 移除通知信息
                if (Information::getInstance()->deleteInfo($post->user_id, $post_id)) {
                    DB::commit();
                    return true;
                }
            } else {
                DB::commit();
                return true;
            }
        }

        DB::rollBack();
        return false;
    }

    public function isVoted($user_id, $post_id) {
        return $this->where('user_id', $user_id)->where('post_id', $post_id)->exists();
    }
}
