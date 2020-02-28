<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/2/27
 * Time: 15:48
 */

namespace App\Models\Tieba;

use App\Models\BaseModel;
use App\Models\Users;

class PostComment extends BaseModel {

    protected $table = 'tieba_post_comment';
    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }

    public function getComment($post_id, $page) {
        $comments = $this
            ->join('users AS u', 'u.id', 'user_id')
            ->where('post_id', $post_id)
            ->where('tieba_post_comment.status', 1)
            ->forPage($page, 10)
            ->get(['tieba_post_comment.id', 'u.name', 'content', 'comment_id']);

        /** @var PostComment $comment */
        foreach ($comments as $key => $comment) {
            if ($comment->comment_id) {
                $user = $this
                    ->join('users AS u', 'u.id', 'user_id')
                    ->find($comment->comment_id, ['u.name'])
                    ->name;
                if ($user) {
                    $comments[$key]->to = $user;
                }
            }
            unset($comments[$key]->comment_id);
        }
        return $comments;
    }

    /**
     * @param int $post_id
     * @param int $comment_id
     *
     * @return bool
     */
    public function isCommentFromPost($post_id, $comment_id) {
        return $this
            ->where('post_id', $post_id)
            ->find($comment_id)
            ->exists();
    }

    /**
     * @param int $user_id
     * @param int $post_id
     * @param int $content
     * @param int $reply_comment_id
     *
     * @return array|bool
     */
    public function addComment($user_id, $post_id, $content, $reply_comment_id = 0) {
        $commentId = $this->insertGetId([
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment_id' => $reply_comment_id ?: 0,
            'content' => $content
        ]);

        if ($commentId) {
            $user_name = Users::find($user_id, ['name'])->name;

            $latestComment = [
                'id' => $commentId,
                'name' => $user_name,
                'content' => $content,
            ];

            if ($reply_comment_id) {
                $to = $this
                    ->join('users AS u', 'u.id', 'user_id')
                    ->find($reply_comment_id, ['name'])
                    ->name;
                $latestComment['to'] = $to;
            }

            return $latestComment;
        }
        return false;
    }
}
