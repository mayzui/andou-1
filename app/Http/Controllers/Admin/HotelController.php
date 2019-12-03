<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\HotelRequest;
use App\Services\ActionLogsService;
use Illuminate\Http\Request;
use App\Services\HotelService;
use App\Repositories\HotelRepository;


class HotelController extends BaseController
{

    protected $hotelService;

    /**
     * ActionLogsController constructor.
     * @param $actionLogsService
     */
    public function __construct(HotelService $hotelService)
    {
        $this->hotelService = $hotelService;
    }

    public function index ()
    {
        dd(123);
        return $this->view(null);
    }

    public function goods()
    {
        dd(123);

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
