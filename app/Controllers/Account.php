<?php

namespace App\Controllers;

class Account extends BaseController
{
    public function index()
    {
        $customerId = $this->currentUser->id;
        $orders = model('PenjualanModel')->getOrdersByCustomer($customerId);

        $this->data['orders']      = $orders;
        $this->data['recentOrder'] = count($orders) > 0 ? $orders[0] : null;
        $this->data['title']       = 'Dashboard Akun';
        return view('themes/indomarket/account/dashboard', $this->data);
    }

    public function orders()
    {
        $customerId = $this->currentUser->id;
        $this->data['orders'] = model('PenjualanModel')->getOrdersByCustomer($customerId);
        $this->data['title']  = 'Riwayat Pesanan';
        return view('themes/indomarket/account/orders', $this->data);
    }

    public function orderDetail(string $invoice)
    {
        $customerId = $this->currentUser->id;
        $order = model('PenjualanModel')->getOrderByInvoice($invoice);
        if (!$order || $order->customer_id != $customerId) {
            return redirect()->to('account/orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        $this->data['order']   = $order;
        $this->data['details'] = model('PenjualanDetailModel')->getDetailByPenjualan($order['id']);
        $this->data['title']   = 'Detail Pesanan ' . $invoice;
        return view('themes/indomarket/account/order_detail', $this->data);
    }

    public function profile()
    {
        $this->data['title'] = 'Profil Saya';
        $this->data['user']  = $this->currentUser;
        return view('themes/indomarket/account/profile', $this->data);
    }

    public function updateProfile()
    {
        $customerId = $this->currentUser->id;
        $data   = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'phone'      => $this->request->getPost('phone'),
            'address'     => $this->request->getPost('address'),
            'city'       => $this->request->getPost('city'),
            'province'   => $this->request->getPost('province'),
            'postal_code'   => $this->request->getPost('postal_code'),
        ];

        // Upload avatar
        $avatar = $this->request->getFile('avatar');
        if ($avatar && $avatar->isValid()) {
            $avatarName = $avatar->getRandomName();
            $avatar->move(FCPATH . 'uploads/avatars', $avatarName);
            $data['avatar'] = 'uploads/avatars/' . $avatarName;
        }

        \Config\Database::connect()->table('users')->update($data, ['id' => $customerId]);

        return redirect()->to('account/profile')->with('success', 'Profil berhasil diperbarui!');
    }

    public function trackOrder(string $invoice)
    {
        $customerId = $this->currentUser->id;
        $order = model('PenjualanModel')->getOrderByInvoice($invoice);
        if (!$order || $order->customer_id != $customerId) {
            return redirect()->to('account/orders');
        }

        $this->data['order']   = $order;
        $this->data['details'] = model('PenjualanDetailModel')->getDetailByPenjualan($order['id']);
        $this->data['title']   = 'Lacak Pesanan ' . $invoice;
        return view('themes/indomarket/account/order_detail', $this->data);
    }
}