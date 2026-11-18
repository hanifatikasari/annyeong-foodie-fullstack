<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Orders extends BaseController
{
    protected ActivityLogger $activityLogger;

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->activityLogger = new ActivityLogger();
    }

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
        $deliveryType = $this->request->getGet('delivery_type') ?? '';

        // getOnlineOrdersForAdmin() mengembalikan Builder,
        // sehingga pagination harus dilakukan di sini.
        $builder = $penjualanModel
            ->getOnlineOrdersForAdmin($status);

        if ($deliveryType !== '') {
            $builder->where('t_penjualan.delivery_type', $deliveryType);
        }

        $orders = $builder
            ->paginate($perPage, 'orders');

        $data = [
            'title'               => 'Kelola Pesanan Online',
            'currentAdminMenu'    => 'sales',
            'currentAdminSubMenu' => 'online_order',
            'orders'              => $orders,
            'pager'               => $penjualanModel->pager,
            'status'              => $status,
            'deliveryType'        => $deliveryType,
        ];

        return view('admin/orders/index', $data);
    }

    // ---------------------------------------------------------------
    // DETAIL — Detail satu pesanan
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

        $db = \Config\Database::connect();

        $couriers = $db->table('users u')
            ->select('u.id, u.first_name, u.last_name')
            ->join('users_groups ug', 'ug.user_id = u.id')
            ->join('groups g', 'g.id = ug.group_id')
            ->where('g.name', 'Kurir')
            ->orderBy('u.first_name', 'ASC')
            ->get()
            ->getResult();

        $data = [
            'title'               => 'Detail Pesanan #' . $order->invoice_no,
            'currentAdminMenu'    => 'sales',
            'currentAdminSubMenu' => 'online_order',
            'order'               => $order,
            'details'             => $details,
            'couriers'            => $couriers,
        ];

        return view('admin/orders/detail', $data);
    }

    // ---------------------------------------------------------------
    // SAVE KURIR
    // ---------------------------------------------------------------
    public function saveCourier($id)
    {
        $courierId = $this->request->getPost('delivery_courier_id');

        model('PenjualanModel')->update(
            $id,
            [
                'delivery_courier_id' => $courierId
            ]
        );

        // Catat ke Audit Trail
        $this->activityLogger->log(
            'update',
            'order',
            "Penugasan kurir (ID: {$courierId}) untuk pesanan ID #{$id}.",
            null,
            ['delivery_courier_id' => $courierId],
            'order',
            $id
        );

        return redirect()
            ->to('admin/orders/detail/' . $id)
            ->with('success', 'Kurir berhasil disimpan.');
    }

    // ---------------------------------------------------------------
    // VERIFY — Verifikasi pembayaran
    // ---------------------------------------------------------------
    public function verify(int $id)
    {
        $penjualanModel = model('PenjualanModel');
        $notificationModel = model('NotificationModel');
        $order = model('PenjualanModel')->find($id);

        if (!$order) {
            return redirect()->back()->with('error', 'Order tidak ditemukan.');
        }

        // Cegah verifikasi ulang
        if ($order->payment_status === 'paid') {
            return redirect()
                ->to('admin/orders/detail/' . $id)
                ->with('info', 'Pembayaran pesanan ini sudah terverifikasi.');
        }

        $updated = $penjualanModel->verifyPayment($id, session()->get('user_id'));

        if (!$updated) {
            return redirect()
                ->to('admin/orders/detail/' . $id)
                ->with('error', 'Gagal memverifikasi pembayaran.');
        }

        // Catat ke Audit Trail
        $this->activityLogger->log(
            'verify',
            'order',
            "Pembayaran pesanan {$order->invoice_no} (ID: {$id}) berhasil diverifikasi oleh admin.",
            ['payment_status' => $order->payment_status, 'order_status' => $order->order_status],
            ['payment_status' => 'paid', 'order_status' => 'processing'],
            'order',
            $id
        );

        model('NotificationModel')->insert([
            'user_id'      => $order->customer_id,
            'type'         => 'payment_verified',
            'reference_id' => (string) $order->id,
            'title'        => 'Pembayaran Terverifikasi',
            'message'      => 'Pembayaran pesanan ' . $order->invoice_no .
                            ' telah diterima. Pesanan sedang diproses.',
            'is_read'      => 0,
        ]);

        return redirect()->to('admin/orders/detail/' . $id)
            ->with('success', 'Pembayaran berhasil diverifikasi! Pesanan sedang diproses.');
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

        $penjualanModel = model('PenjualanModel');
        $notificationModel = model('NotificationModel');
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

            'shipped' => ['completed'],
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
        // Simpan perubahan status pesanan
        $updated = $penjualanModel->updateOrderStatus($id, $newStatus);
        if (!$updated) {
            return redirect()
                ->to('admin/orders/detail/' . $id)
                ->with('error', 'Gagal memperbarui status pesanan.');
        }

        // Catat ke Audit Trail
        $deskripsi = "Status pesanan {$order->invoice_no} diubah dari '{$currentStatus}' ke '{$newStatus}'.";
        if ($newStatus === 'cancelled') {
            $deskripsi .= ' Stok produk dikembalikan ke gudang.';
        }

        $this->activityLogger->log(
            'update',
            'order',
            $deskripsi,
            ['order_status' => $currentStatus],
            ['order_status' => $newStatus],
            'order',
            $id
        );

        $messages = [
            'processing' => [
                'title' => 'Pesanan Sedang Diproses',
                'message' => 'Pesanan ' . $order->invoice_no .
                            ' sedang dipersiapkan.'
            ],
            'shipped' => [
                'title' => 'Pesanan Sedang Dikirim',
                'message' => 'Pesanan ' . $order->invoice_no .
                            ' sedang dalam perjalanan.'
            ],
            'completed' => [
                'title' => 'Pesanan Selesai',
                'message' => 'Pesanan ' . $order->invoice_no .
                            ' telah selesai. Terima kasih!'
            ],
            'cancelled' => [
                'title' => 'Pesanan Dibatalkan',
                'message' => 'Pesanan ' . $order->invoice_no .
                            ' telah dibatalkan.'
            ],
        ];
        // Buat notifikasi untuk pemilik pesanan
        if (isset($messages[$newStatus])) {
            model('NotificationModel')->insert([
                'user_id'      => $order->customer_id,
                'type'         => 'order_' . $newStatus,
                'reference_id' => (string) $order->id,
                'title'        => $messages[$newStatus]['title'],
                'message'      => $messages[$newStatus]['message'],
                'is_read'      => 0,
            ]);
        }

        return redirect()
            ->to('admin/orders/detail/' . $id)
            ->with('success', 'Status pesanan berhasil diperbarui!');
    }

    // ---------------------------------------------------------------
    // VIEW BUKTI — Tampilkan file bukti pembayaran
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
