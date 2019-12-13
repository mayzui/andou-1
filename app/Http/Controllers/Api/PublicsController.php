<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PublicsController extends Controller
{
    /**
     * @api {POST} http://aaa.com/index/Index/add_needs 添加用户需求
     * @apiVersion 1.0.0
     * @apiGroup NEED
     *
     *  @apiHeader {String} access-key Users unique access-key.
     * @apiParam {String} need_name 需求者名称-非空
     * @apiParam {String} e_mail 用户邮箱-非空邮箱格式
     * @apiParam  {String} phone 用户电话-非空
     * @apiParam {String} company_name 需求公司名称-非空
     * @apiParam  {String} needs_desc 需求描述-非空
     *
     * @apiSuccess {Object} code 返回码
     * @apiSuccess {Object} reason  中文解释
     * @apiSuccess {String[]} data  返回数据
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *          "code":0,
     *          "reason":"需求已经提交了，我们的工作人员会在2个工作日内和您取得联系!",
     *          "data":[]
     *      }
     */

    public function getUserById ($id)
    {

    }

    /**
     * @api {POST} http://aaa.com/api/public/index 添加用户需求
     * @apiVersion 1.0.0
     * @apiGroup NEED
     *
     * @apiParam {String} need_name 需求者名称-非空
     * @apiParam {String} e_mail 用户邮箱-非空邮箱格式
     * @apiParam  {String} phone 用户电话-非空
     * @apiParam {String} company_name 需求公司名称-非空
     * @apiParam  {String} needs_desc 需求描述-非空
     *
     * @apiSuccess {Object} code 返回码
     * @apiSuccess {Object} reason  中文解释
     * @apiSuccess {String[]} data  返回数据
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *          "code":0,
     *          "reason":"需求已经提交了，我们的工作人员会在2个工作日内和您取得联系!",
     *          "data":[]
     *      }
     */

    public function index ()
    {

    }


    /**
     * @api {post} /Api/Admin/Article/add 新增文章
     * @apiName add
     * @apiGroup Article
     * @apiHeader {String} t-host 用户主机编码
     * @apiHeader {String} t-token 用户登录授权编码
     * @Param(name="cat_id", alias="分类id", required="",notEmpty="",numeric="")
     * @Param(name="title", alias="文章标题", required="",notEmpty="")
     * @Param(name="keyword", alias="关键词" )
     * @Param(name="description", alias="描述摘要" )
     * @Param(name="pic", alias="文章封面图片" )
     * @Param(name="content", alias="文章内容" )
     * @apiParam {int} cat_id 文章分类id
     * @apiParam {string} title 文章标题
     * @apiParam {string} keyword  关键词
     * @apiParam {string} description  描述摘要
     * @apiParam {string} pic  文章封面图片
     * @apiParam {string} pics 图片
     * @apiParam {string} content  文章内容
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} result
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code":200,"result":{},"msg":"success"}
     */
    public function  test ()
    {}

}
