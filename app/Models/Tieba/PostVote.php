<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/2/27
 * Time: 15:48
 */

namespace App\Models\Tieba;

use App\Models\BaseModel;
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

        if ($vote) {
            if ($this->insert(['user_id' => $user_id, 'post_id' => $post_id])
                && Post::find($post_id)->increment('vote')) {
                DB::commit();;
                return true;
            }
        } else if ($this->where('user_id', $user_id)->where('post_id', $post_id)->delete()
            && Post::find($post_id)->decrement('vote')) {
            DB::commit();
            return true;
        }

        DB::rollBack();
        return false;
    }

    public function isVoted($user_id, $post_id) {
        return $this->where('user_id', $user_id)->where('post_id', $post_id)->exists();
    }
}
