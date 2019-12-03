<?php

namespace App\Repositories;


use App\Models\Goods;

class ShopRepository
{
    /**
     * 添加权限
     * @param array $params
     * @return mixed
     */
    public function create(array $params)
    {
        return Goods::create($params);
    }

    /**
     * 根据id获取权限的详细信息
     * @param $id
     * @return mixed
     */
    public function ById($id)
    {
        return Goods::find($id);
    }

    /**
     * 根据路由名称获取路由的详细信息
     * @param $route
     * @return mixed
     */
    public function ByRoute($route)
    {
        return Goods::where('route', $route)->first();
    }

    /**
     * 获取全部权限只限显示的数据
     * @return mixed
     */
    public function getRulesAndPublic()
    {
        return Goods::orderBy('sort', 'asc')->public()->get();
    }

    /**
     * 获取全部权限
     * @return mixed
     */
    public function getRules()
    {
        return Goods::orderBy('sort', 'asc')->get();
    }
}