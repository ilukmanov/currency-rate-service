<?php

namespace app\controller;

use support\Request;

class IndexController
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request The incoming request.
     * @return mixed The response to the request.
     */
    public function index(Request $request)
    {
        $data = [
            'microservice' => config('server.name'),
        ];
        return success($data);
    }
}
