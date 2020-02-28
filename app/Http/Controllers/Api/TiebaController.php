<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/2/27
 * Time: 10:42
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tieba\{Post, PostComment, PostType, PostVote};
use Carbon\Carbon;
use Exception;
use Illuminate\Http\{JsonResponse, Request, UploadedFile};
use Illuminate\Support\Facades\Log;

class TiebaController extends Controller {

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @api {get} /api/tieba/list 帖文列表
     * @apiName list
     * @apiGroup tieba
     * @apiParam {Number} [uid] type=mine 时必传
     * @apiParam {String=public,mine} [type=public] 类型，public - 广场；mine - 我的
     * @apiParam {Number} [page=1] 页码
     * @apiSuccessExample {json} Success-Response:
     * {}
     *
     */
    public function list(Request $request) {
        $data = $this->validate($request, [
            'uid' => 'required_if:type,mine|numeric|exists:users,id',
            'type' => 'field|string|in:public,mine',
            'page' => 'nullable|numeric|min:1'
        ]);

        $page = 1;
        $user_id = null;

        if (isset($data['page']) && $data['page'] > 1) {
            $page = $data['page'];
        }

        if (isset($data['type']) && $data['type'] === 'mine') {
            $user_id = $data['uid'];
        }

        return $this->responseJson(200, 'OK', Post::getInstance()->getPostList($page, $user_id));
    }

    /**
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @api {get} /api/tieba/detail 帖文详情
     * @apiName detail
     * @apiGroup tieba
     * @apiParam {Number} post_id 帖文 ID
     * @apiParam {Number} [page=1] 页码
     * @apiSuccessExample {json} Success-Response:
     * {}
     *
     */
    public function detail(Request $request) {
        $data = $this->validate($request, [
            'post_id' => 'required|numeric|exists:tieba_post,id',
            'page' => 'nullable|numeric|min:1'
        ]);

        if (isset($data['page']) && $data['page'] > 1) {
            return $this->responseJson(
                200,
                'OK',
                PostComment::getInstance()->getComment($data['post_id'], $data['page'])
            );
        }

        return $this->responseJson(200, 'OK', Post::getInstance()->getDetail($data['post_id']));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @api {post} /api/tieba/upvote 帖文点赞
     * @apiName upvote
     * @apiGroup tieba
     * @apiParam {Number} uid
     * @apiParam {Number} post_id 帖文 ID
     * @apiParam {Number=1,0} [vote=1] 默认点赞，0 为取消
     * @apiSuccessExample {json} Success-Response:
     * {}
     *
     */
    public function upvote(Request $request) {
        $data = $this->validate($request, [
            'uid' => 'required|numeric|exists:users,id',
            'post_id' => 'required|numeric|exists:tieba_post,id',
            'vote' => 'nullable|numeric|in:1,0'
        ]);

        $vote = isset($data['vote']) ? (bool)$data['vote'] : true;

        if ($vote && PostVote::getInstance()->isVoted($data['uid'], $data['post_id'])) {
            return $this->responseJson(201, '请勿重复点赞');
        }

        try {
            if (PostVote::getInstance()->vote($data['uid'], $data['post_id'], $vote)) {
                return $this->responseJson(200, '操作成功');
            }
        } catch (Exception $e) {
            Log::error('贴吧点赞异常，错误消息：' . $e->getMessage(), $e->getTrace());
        }

        return $this->responseJson(201, '操作失败');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @api {post} /api/tieba/comment 帖文评论/回复
     * @apiName comment
     * @apiGroup tieba
     * @apiParam {Number} uid
     * @apiParam {Number} post_id 帖文 ID
     * @apiParam {String} content 评论内容
     * @apiParam {Number} [comment_id] 评论 ID，回复用户时必传
     * @apiSuccessExample {json} Success-Response:
     * {}
     *
     */
    public function comment(Request $request) {
        $data = $this->validate($request, [
            'uid' => 'required|numeric|exists:users,id',
            'post_id' => 'required|numeric|exists:tieba_post,id',
            'content' => 'required|string|max:60',
            'comment_id' => 'filled|numeric|exists:tieba_post_comment,id'
        ]);

        if (isset($data['comment_id']) && $data['comment_id']) {
            if (!PostComment::getInstance()->isCommentFromPost($data['post_id'], $data['comment_id'])) {
                return $this->responseJson(201, '回复目标不存在');
            }
        } else {
            $data['comment_id'] = 0;
        }

        $ret = PostComment::getInstance()
            ->addComment($data['uid'], $data['post_id'], $data['content'], $data['comment_id']);

        if ($ret) {
            return $this->responseJson(200, '回复成功', $ret);
        }
        return $this->responseJson(201, '回复失败');
    }

    /**
     * @param Request $request
     *
     * @api {get} /api/tieba/share 帖文分享
     * @apiName share
     * @apiGroup tieba
     * @apiSuccessExample {json} Success-Response:
     * {}
     *
     */
    public function share(Request $request) {
        // TODO:
    }

    /**
     * @return JsonResponse
     *
     * @api {get} /api/tieba/types 帖文类型
     * @apiName types
     * @apiGroup tieba
     * @apiSuccessExample {json} Success-Response:
     * {}
     *
     */
    public function types() {
        return $this->responseJson(200, 'OK', PostType::getInstance()->getType());
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @api {post} /api/tieba/post 发布帖文
     * @apiName post
     * @apiGroup tieba
     * @apiParam {Number} uid
     * @apiParam {String} title 标题
     * @apiParam {String} content 内容
     * @apiParam {Object[]} [images] 图片
     * @apiParam {Number} type_id 类型 ID，通过 types 接口获取
     * @apiParam {String} contact_info 联系方式
     * @apiParam {Boolean} top_post 是否置顶
     * @apiSuccessExample {json} Success-Response:
     * {}
     *
     */
    public function post(Request $request) {
        $data = $this->validate($request, [
            'uid' => 'required|numeric|exists:users,id',
            'title' => 'required|string|max:16',
            'content' => 'required|string|max:160',
            'images.*' => 'nullable|image',
            'type_id' => 'required|numeric|exists:tieba_post_type,id',
            'contact_info' => 'required|string|max:64',
            'top_post' => 'required|boolean'
        ]);

        if (isset($data['images'])) {
            $now = Carbon::now();
            $basePath = "post/{$now->format('Ym')}";
            $storagePath = public_path($basePath);

            if (!is_writeable($storagePath) && !mkdir($storagePath, 0777, true)) {
                Log::error('贴吧图片上传目录权限不足');
                return $this->responseJson(201, '上传图片异常');
            }

            $successes = [];

            /** @var UploadedFile $image */
            foreach ($data['images'] as $image) {
                if (stripos($image->getMimeType(), 'image') === false) {
                    continue;
                }

                $fileName = Carbon::now()->format('YmdHis') . ex_mt_rand(5) . '.' .
                    $image->getClientOriginalExtension();

                if ($image->move($storagePath, $fileName)) {
                    $successes[] = "/{$basePath}/{$fileName}";
                }
            }
        }

        try {
            $ret = Post::getInstance()->addPost([
                'user_id' => $data['uid'],
                'title' => $data['title'],
                'content' => $data['content'],
                'type_id' => $data['type_id'],
                'contact_info' => $data['contact_info'],
                'top_post' => (int)$data['top_post']
            ], isset($successes) ? $successes : []);
        } catch (Exception $e) {
            Log::error('贴吧发帖异常，错误消息：' . $e->getMessage(), $e->getTrace());
            return $this->responseJson(201, '发帖异常，请重试');
        }

        if ($ret) {
            if ($data['top_post']) {
                // TODO: 付费流程
            }
            return $this->responseJson(200, '发帖成功');
        }

        return $this->responseJson(201, '发帖失败，请重试');
    }
}
