<?php

namespace App\Controllers;

class TestMidtrans extends BaseController
{
    public function index()
    {
        return env('MIDTRANS_SERVER_KEY');
    }
}