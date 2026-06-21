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

        model('PenjualanModel')
            ->updateOrderStatus($id, 'verified');

        return redirect()->to('admin/orders/' . $id)
            ->with('success', 'Pembayaran berhasil diverifikasi! Status diubah ke Verified.');
    }

    // ---------------------------------------------------------------
    // UPDATE STATUS — Update status pesanan dan restore stok jika dibatalkan
    // ---------------------------------------------------------------
    public function updateStatus(int $id)
    {
        $newStatus = $this->request->getPost('order_status');

        $allowedStatuses = [
            'pending',
            'processing',
            'shipped',
            'completed',
            'cancelled'
        ];

        if (!in_array($newStatus, $allowedStatuses)) {
            return redirect()->back()
                ->with('error', 'Status tidak valid.');
        }

        $order = model('PenjualanModel')->find($id);

        if (!$order) {
            return redirect()->back()
                ->with('error', 'Pesanan tidak ditemukan.');
        }

        $currentStatus = $order->order_status;

        /*
        |--------------------------------------------------------------------------
        | Allowed Status Transition
        |--------------------------------------------------------------------------
        | pending    -> processing, cancelled
        | processing -> shipped, completed, cancelled
        | shipped    -> completed
        | completed  -> final
        | cancelled  -> final
        |--------------------------------------------------------------------------
        */

        $allowedTransitions = [
            'pending' => [
                'processing',
                'cancelled'
            ],

            'processing' => [
                'shipped',
                'completed',
                'cancelled'
            ],

            'shipped' => [
                'completed'
            ],

            'completed' => [],

            'cancelled' => [],
        ];

        if (
            !isset($allowedTransitions[$currentStatus]) ||
            !in_array($newStatus, $allowedTransitions[$currentStatus])
        ) {

            return redirect()->back()
                ->with(
                    'error',
                    "Status tidak dapat diubah dari '{$currentStatus}' ke '{$newStatus}'."
                );
        }

        // ============================================================
        // Restore stok HANYA saat pertama kali masuk ke cancelled
        // ============================================================
        if ($newStatus === 'cancelled') {

            $details = model('PenjualanDetailModel')
                ->where('penjualan_id', $id)
                ->findAll();

            $inventoryModel = model('ProductInventoryModel');

            foreach ($details as $item) {

                $inventory = $inventoryModel
                    ->where('product_id', $item->product_id)
                    ->first();

                if ($inventory) {

                    $inventoryModel->update(
                        $inventory->id,
                        [
                            'qty' => $inventory->qty + $item->qty
                        ]
                    );
                }
            }
        }

        model('PenjualanModel')
            ->updateOrderStatus($id, $newStatus);

        return redirect()
            ->to('admin/orders/detail/' . $id)
            ->with(
                'success',
                'Status pesanan berhasil diperbarui!'
            );
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