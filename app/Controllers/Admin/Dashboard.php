<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        // Gunakan service atau instantiate langsung jika hanya dipakai di sini
        $ionAuth = new \IonAuth\Libraries\IonAuth();

        $data = [
            'title'               => 'Dashboard Annyeong Foodie',
            'currentAdminMenu'    => 'dashboard',
            'currentAdminSubMenu' => 'dashboard',
            // Contoh: Jika butuh data user yang login
            'user'                => $ionAuth->user()->row(),
        ];

        return view('admin/dashboard/index', $data);
    }
}