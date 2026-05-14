<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Orders extends BaseController
{
    public function index()
    {
        $status  = $this->request->getGet('status') ?? '';
        $model   = model('PenjualanModel');

        $query = $model;
        if ($status) {
            $query->where('order_status', $status);
        }

         // Ambil data orders
        $orders = $query->orderBy('id', 'DESC')->paginate(15, 'bootstrap');

        // Load UserModel
        $userModel = new \IonAuth\Models\UserModel();

        // Tambahkan customer_name ke setiap order
        foreach ($orders as &$order) {
            $user = $userModel->find($order['customer_id']);

            $order['customer_name'] = $user
                ? trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))
                : '-';
        }


        $data = [
            'title'               => 'Kelola Pesanan Online',
            'currentAdminMenu'    => 'sales',
            'currentAdminSubMenu' => 'order',
            'orders'              => $orders,
            'pager'               => $model->pager,
            'status'              => $status,
        ];

        return view('admin/orders/index', $data);
    }

    public function detail(int $id)
    {
        $order   = model('PenjualanModel')->find($id);
        if (!$order) return redirect()->to('admin/orders')->with('error', 'Tidak ditemukan.');

        $details = model('PenjualanDetailModel')->getDetailByPenjualan($id);

        $data = [
            'title'               => 'Detail Pesanan #' . $order->invoice_no,
            'currentAdminMenu'    => 'sales',
            'currentAdminSubMenu' => 'order',
            'order'               => $order,
            'details'             => $details,
        ];

        return view('admin/orders/detail', $data);
    }

    public function verify(int $id)
    {
        $order = model('PenjualanModel')->find($id);
        if (!$order) return redirect()->back()->with('error', 'Order tidak ditemukan.');

        model('PenjualanModel')->update($id, [
            'payment_status' => 'lunas',
            'verified_at'    => date('Y-m-d H:i:s'),
            'verified_by'    => $this->currentUser->id,
        ]);

        return redirect()->to('admin/orders/' . $id)->with('success', 'Pembayaran berhasil diverifikasi!');
    }

    public function updateStatus(int $id)
    {
        $status = $this->request->getPost('order_status');
        $allowed = ['pending', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];

        if (!in_array($status, $allowed)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

       model('PenjualanModel')->update($id, [
            'order_status' => $status
        ]);
        return redirect()->to('admin/orders/' . $id)->with('success', 'Status pesanan diperbarui!');
    }

    public function viewBukti(string $filename)
    {
        $path = FCPATH . 'uploads/payment_proof/' . $filename;
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }
        return $this->response->setHeader('Content-Type', mime_content_type($path))
                              ->setBody(file_get_contents($path));
    }
}