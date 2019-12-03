<?php
namespace App\Services;

use App\Handlers\Tree;
use App\Repositories\ShopRepository;

class ShopService
{
    protected $shopRepository;

    /**
     * RulesService constructor.
     * @param RulesRepository $rulesRepository
     * @param Tree $tree
     */
    public function __construct(ShopRepository $shopRepository ,Tree $tree)
    {
        $this->shopRepository = $shopRepository;
    }

    /**
     * 创建权限数据
     * @param array $params
     * @return mixed
     */
    public function create(array $params)
    {

    }

    /**
     * 根据id获取权限的详细信息
     * @param $id
     * @return mixed
     */
    public function ById($id)
    {
        return $this->shopRepository->ById($id);
    }

    /**
     * 获取树形结构权限列表
     * @return array
     */
    public function getRulesTree()
    {

    }
}