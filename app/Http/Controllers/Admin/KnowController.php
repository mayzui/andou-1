<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KnowController extends Controller
{
    /**
     * @descript 入住需知
     * @author  jsy
     */
    public function index()
    {
            return view('admin.know.index');
    }
    public function www(Request $request)
    {
       var_dump($request->post());
    }


}