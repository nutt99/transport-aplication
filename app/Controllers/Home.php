<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('frontend/index', [
            'transportApi'   => rtrim(base_url('app-api'), '/'),
            'wisataProxyApi' => rtrim(base_url('app-api/wisata'), '/'),
        ]);
    }
}