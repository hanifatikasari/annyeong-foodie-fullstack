<?php

namespace App\Controllers;

class TestMidtrans extends BaseController
{
    public function index()
{
    require_once ROOTPATH . 'vendor/autoload.php';

    echo '<pre>';

    var_dump(class_exists('\Midtrans\Config'));

    echo '</pre>';
}
}