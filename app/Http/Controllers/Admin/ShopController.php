<?php

namespace App\Http\Controllers\Admin;
use Auth;

use App\Http\Requests\Admin\ShopRequest;
use App\Services\ActionLogsService;
use Illuminate\Http\Request;
use App\Services\ShopService;
use App\Repositories\ShopRepository;


class ShopController extends BaseController
{

    protected $shopService;

    /**
     * ActionLogsController constructor.
     * @param $actionLogsService
     */
    public function __construct(ShopService $shopService)
    {
        $this->shopService = $shopService;
    }

    public function index ()
    {

        $data = Auth::guard('admin')->user()->getMenus();

        dd($data);
        return $this->view(null);
    }

    public function goods(Auth $auth)
    {
        $data = $auth::guard('admin')->user()->getMenus();
        dd($data);

        return $this->view(null);
    }

    public function goodsCate ()
    {
        dd(123);

        return $this->view(null);
    }

    public function orders ()
    {
        dd(123);

        return $this->view(null);
    }

    public function goodsBrand ()
    {
        dd(123);

        return $this->view(null);
    }
}
