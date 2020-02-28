<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/2/27
 * Time: 15:47
 */

namespace App\Models\Tieba;

use App\Models\BaseModel;
use App\Models\Users;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Post extends BaseModel {

    protected $table = 'tieba_post';
    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }

    public function user() {
        return $this
            ->hasOne(Users::class, 'id', 'user_id')
            ->selectRaw("name, CONCAT('{$this->domain}', avator) AS avator");
    }

    public function images() {
        return $this
            ->hasMany(PostImage::class, 'post_id', 'id')
            ->where('status', 1)
            ->selectRaw("CONCAT('{$this->domain}', image_url) AS image_url");
    }

    public function commentCount() {
        return $this
            ->hasOne(PostComment::class, 'post_id', 'id')
            ->where('status', 1);
    }

    public function comments($limit = 3) {
        return $this
            ->hasMany(PostComment::class, 'post_id', 'id')
            ->join('users AS u', 'u.id', 'user_id')
            ->where('tieba_post_comment.status', 1)
            ->orderByDesc('tieba_post_comment.created_at')
            ->select(['tieba_post_comment.id', 'u.name', 'content', 'comment_id'])
            ->limit($limit);
    }

    public function getPostList($page = 1, $user_id = null, $page_size = 10) {
        $posts = $this
            ->where('is_show', 1)
            ->where('status', 1)
            ->where(function (Builder $query) use ($user_id) {
                if ($user_id) {
                    $query->where('user_id', $user_id);
                }
            })
            ->orderByDesc('top_post')
            ->orderByDesc('created_at')
            ->select(['id', 'user_id', 'title', 'vote'])
            ->selectRaw('LEFT(content, ?) AS content', [64])
            ->forPage($page, $page_size)
            ->get();

        /** @var Post $post */
        foreach ($posts as $post) {
            $user = $post->user()->first();
            $post->setAttribute('name', $user->name);
            $post->setAttribute('avator', $user->avator);

            $post->setAttribute('comment_count', $post->commentCount()->count());

            $post->setAttribute('images', $post->images()->pluck('image_url'));

            /** @var PostComment $comment */
            foreach ($post->comments as $key => $comment) {
                if ($comment->comment_id) {
                    $user = PostComment::getInstance()
                        ->join('users AS u', 'u.id', 'user_id')
                        ->find($comment->comment_id, ['u.name'])
                        ->name;
                    if ($user) {
                        $comment->setAttribute('to', $user);
                    }
                }
                unset($comment->comment_id);
            }
        }
        return $posts;
    }

    public function getDetail($post_id) {
        $detail = $this->find($post_id, ['id', 'user_id', 'title', 'content', 'vote']);

        $user = $detail->user();
        $detail->setAttribute('name', $user->value('name'));
        $detail->setAttribute('avator', $user->value('avator'));

        $detail->setAttribute('images', $detail->images()->pluck('image_url'));

        $comments = $detail->comments(10)->get();
        /** @var PostComment $comment */
        foreach ($comments as $key => $comment) {
            if ($comment->comment_id) {
                $user = PostComment::getInstance()
                    ->join('users AS u', 'u.id', 'user_id')
                    ->find($comment->comment_id, ['u.name'])
                    ->name;
                if ($user) {
                    $comments[$key]->setAttribute('to', $user);
                }
            }
            unset($comments[$key]->comment_id);
        }

        $detail->setAttribute('comments', $comments);

        return $detail;
    }

    /**
     * @param array $post_data
     * @param array $images
     *
     * @return bool
     * @throws Exception
     */
    public function addPost($post_data, $images = []) {
        DB::beginTransaction();

        if ($post_data['top_post']) {
            $post_data['is_show'] = 0;
        }

        $postId = $this->insertGetId($post_data);

        if ($postId) {
            $imageData = [];
            foreach ($images as $image) {
                $imageData[] = [
                    'post_id' => $postId,
                    'image_url' => $image
                ];
            }

            if (PostImage::getInstance()->insert($imageData)) {
                DB::commit();;
                return true;
            }
        }

        DB::rollBack();
        return false;
    }
}
