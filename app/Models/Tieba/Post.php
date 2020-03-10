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
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class Post extends BaseModel {

    protected $table = 'tieba_post';
    protected $fillable = ['is_show', 'top_day', 'status'];
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
            ->select(['tieba_post_comment.id', 'content', 'comment_id', 'u.name'])
            ->selectRaw("CONCAT('{$this->domain}', u.avator) AS avator")
            ->limit($limit);
    }

    public function getPostList($page = 1, $type = 'public', $user_id = null) {
        $condition = $this
            ->leftJoin('tieba_post_vote AS tpv', function (JoinClause $join) use ($user_id) {
                $join->on('tpv.post_id', 'tieba_post.id')->whereRaw('tpv.user_id = ?', [$user_id ?: 0]);
            })
            ->join('tieba_post_type AS tpt', 'tpt.id', 'tieba_post.type_ic')
            ->where('tieba_post.is_show', 1)
            ->where('tieba_post.status', 1)
            ->where(function (Builder $query) use ($user_id, $type) {
                if ($type === 'mine') {
                    $query->where('tieba_post.user_id', $user_id);
                }
            })
            ->select(['tieba_post.id', 'tieba_post.user_id', 'tieba_post.title', 'tpt.type_name', 'tieba_post.vote',
                'tieba_post.share', 'tieba_post.top_post'])
            ->selectRaw("IF(NOW() <= DATE_ADD(tieba_post.paid_at, INTERVAL top_day DAY), ?, ?) AS top_post,
                IF(tpv.id IS NULL, 0, 1) AS is_vote, LEFT(tieba_post.content, ?) AS content,
                DATE_FORMAT(tieba_post.created_at, ?) AS created_at",
                [1, 0, 64, '%Y-%m-%d %H:%i']);

        if ($type === 'mine') {
            $condition = $condition->orderByDesc('tieba_post.created_at');
        } else {
            $condition = $condition->orderByDesc('tieba_post.top_post')->orderByDesc('tieba_post.created_at');
        }

        $posts = $condition->forPage($page, 10)->get();

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

    public function getDetail($post_id, $user_id = null) {
        $detail = $this
            ->leftJoin('tieba_post_vote AS tpv', function (JoinClause $join) use ($user_id) {
                $join->on('tpv.post_id', 'tieba_post.id')->whereRaw('tpv.user_id = ?', [$user_id]);
            })
            ->select(['tieba_post.id', 'tieba_post.user_id', 'tieba_post.title', 'tieba_post.content', 'tieba_post.vote',
                'tieba_post.share', 'tieba_post.top_post'])
            ->selectRaw('IF(tpv.id IS NULL, 0, 1) AS is_vote, DATE_FORMAT(tieba_post.created_at, ?) AS created_at',
                ['%Y-%m-%d %H:%i'])
            ->find($post_id);

        $user = $detail->user();
        $detail->setAttribute('name', $user->value('name'));
        $detail->setAttribute('avator', $user->value('avator'));

        $detail->setAttribute('images', $detail->images()->pluck('image_url'));

        $detail->setAttribute('comment_count', $detail->commentCount()->count());

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
     * @return bool|int
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
                return $postId;
            }
        }

        DB::rollBack();
        return false;
    }
}
