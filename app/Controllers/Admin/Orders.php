<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Orders extends BaseController
{
    // ---------------------------------------------------------------
    // INDEX — Daftar semua pesanan online
    // FIX ISSUE #4: Hapus \IonAuth\Models\UserModel yang tidak ada.
    //               Gunakan DB query langsung ke tabel users.
    // FIX ISSUE #3: Gunakan customer_id, order_status, dll.
    // ---------------------------------------------------------------
    public function index()
    {
        $status  = $this->request->getGet('status') ?? '';
        $perPage = 15;

        $penjualanModel = model('PenjualanModel');

        // getOnlineOrdersForAdmin() mengembalikan Builder,
        // sehingga pagination harus dilakukan di sini.
        $orders = $penjualanModel
            ->getOnlineOrdersForAdmin($status)
            ->paginate($perPage, 'orders');

        $data = [
            'title'               => 'Kelola Pesanan Online',
            'currentAdminMenu'    => 'sales',
            'currentAdminSubMenu' => 'online_order',
            'orders'              => $orders,
            'pager'               => $penjualanModel->pager,
            'status'              => $status,
        ];

        return view('admin/orders/index', $data);
    }

    // ---------------------------------------------------------------
    // DETAIL — Detail satu pesanan
    // FIX #3: akses sebagai object ($order->invoice, $order->customer_id)
    // FIX #4: akses user dari DB langsung, bukan IonAuth model
    // ---------------------------------------------------------------
    public function detail(int $id)
    {
        $order = model('PenjualanModel')->getOrderWithCustomerById($id);

        if (!$order) {
            return redirect()->to('admin/orders')
                ->with('error', 'Pesanan tidak ditemukan.');
        }

        $details = model('PenjualanDetailModel')
            ->getDetailByPenjualan($id);

        $data = [
            'title'               => 'Detail Pesanan #' . $order->invoice_no,
            'currentAdminMenu'    => 'sales',
            'currentAdminSubMenu' => 'online_order',
            'order'               => $order,
            'details'             => $details,
        ];

        return view('admin/orders/detail', $data);
    }

    // ---------------------------------------------------------------
    // VERIFY — Verifikasi pembayaran
    // FIX #3: update 'order_status' (bukan status_order)
    // ---------------------------------------------------------------
    public function verify(int $id)
    {
        $order = model('PenjualanModel')->find($id);

        if (!$order) {
            return redirect()->back()->with('error', 'Order tidak ditemukan.');
        }

        model('PenjualanModel')->update($id, [
            'order_status'   => 'verified',
            'payment_status' => 'verified',
            'verified_at'    => date('Y-m-d H:i:s'),
            'verified_by'    => $this->currentUser->id,
        ]);

        return redirect()->to('admin/orders/' . $id)
            ->with('success', 'Pembayaran berhasil diverifikasi! Status diubah ke Verified.');
    }

    // ---------------------------------------------------------------
    // UPDATE STATUS
    // FIX #3: update 'order_status'
    // ---------------------------------------------------------------
    public function updateStatus(int $id)
    {
        $newStatus = $this->request->getPost('order_status');
        $allowed   = [
            'pending_payment',
            'pending_verification',
            'verified',
            'processing',
            'ready',
            'completed',
            'cancelled',
        ];

        if (!in_array($newStatus, $allowed)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        model('PenjualanModel')->update($id, ['order_status' => $newStatus]);

        return redirect()->to('admin/orders/detail/' . $id)
            ->with('success', 'Status pesanan berhasil diperbarui!');
    }

    // ---------------------------------------------------------------
    // VIEW BUKTI — Tampilkan file bukti pembayaran
    // FIX #3: field 'payment_proof'
    // ---------------------------------------------------------------
    public function viewBukti(string $filename)
    {
        $path = FCPATH . 'uploads/bukti_bayar/' . $filename;

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File bukti pembayaran tidak ditemukan.');
        }

        $mimeType = mime_content_type($path);
        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setBody(file_get_contents($path));
    }
}