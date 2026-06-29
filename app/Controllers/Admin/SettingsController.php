<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class SettingsController extends BaseController
{
    protected $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
    }

    public function payment()
    {
        $data['title'] = 'Payment Settings';

        $data['currentAdminMenu']    = '';
        $data['currentAdminSubMenu'] = 'payment_setting';

        $data['settings'] = [
            'midtrans' => $this->settingModel->getValue(
                'payment_midtrans_enabled',
                0
            ),

            'transfer' => $this->settingModel->getValue(
                'payment_transfer_enabled',
                0
            ),

            'qris' => $this->settingModel->getValue(
                'payment_qris_enabled',
                0
            ),
            'cash' => $this->settingModel->getValue(
                'payment_cash_enabled',
                1
            ),
        ];

        return view(
            'admin/settings/payment',
            $data
        );
    }

    public function savePayment()
    {
        $this->settingModel->setValue(
            'payment_midtrans_enabled',
            $this->request->getPost('midtrans') ? 1 : 0
        );

        $this->settingModel->setValue(
            'payment_transfer_enabled',
            $this->request->getPost('transfer') ? 1 : 0
        );

        $this->settingModel->setValue(
            'payment_qris_enabled',
            $this->request->getPost('qris') ? 1 : 0
        );

        $this->settingModel->setValue(
            'payment_cash_enabled',
            $this->request->getPost('cash') ? 1 : 0
        );

        return redirect()
            ->back()
            ->with(
                'success',
                'Pengaturan pembayaran berhasil disimpan.'
            );
    }

    public function handlingFee()
    {
        $settingModel=model('SettingModel');

        return view('admin/settings/handling_fee',[
            'title'=>'Handling Fee',
            'currentAdminSubMenu'=>'handling_fee',
            'fee'=>$settingModel->getValue('handling_fee',5000)
        ]);
    }

    public function saveHandlingFee()
    {
        model('SettingModel')->setValue(
            'handling_fee',
            $this->request->getPost('fee')
        );

        return redirect()->back()->with(
            'success',
            'Handling fee berhasil disimpan.'
        );
    }
}