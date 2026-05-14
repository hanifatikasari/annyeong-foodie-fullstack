<?php

namespace App\Controllers;

class Account extends BaseController
{
    // ---------------------------------------------------------------
    // DASHBOARD
    // FIX ISSUE #6: method getOrdersByUser() — pastikan ada di model
    // FIX ISSUE #8: null guard pada $currentUser
    // ---------------------------------------------------------------
    public function index()
    {
        $userId = $this->currentUser->id ?? 0;

        // Menggunakan method yang telah diperbaiki di PenjualanModel
        $orders = model('PenjualanModel')->getOrdersByCustomer($userId);

        $pendingCount   = count(array_filter($orders, fn($o) => in_array(
            is_object($o) ? $o->status_order : $o['status_order'],
            ['pending_payment', 'pending_verification']
        )));
        $completedCount = count(array_filter($orders, fn($o) =>
            (is_object($o) ? $o->status_order : $o['status_order']) === 'completed'
        ));

        $this->data['orders']         = $orders;
        $this->data['recentOrder']    = !empty($orders) ? $orders[0] : null;
        $this->data['pendingCount']   = $pendingCount;
        $this->data['completedCount'] = $completedCount;
        $this->data['title']          = 'Dashboard Akun';

        return view('themes/indomarket/account/dashboard', $this->data);
    }

    // ---------------------------------------------------------------
    // RIWAYAT PESANAN
    // ---------------------------------------------------------------
    public function orders()
    {
        $userId = $this->currentUser->id ?? 0;

        $this->data['orders'] = model('PenjualanModel')->getOrdersByCustomer($userId);
        $this->data['title']  = 'Riwayat Pesanan';

        return view('themes/indomarket/account/orders', $this->data);
    }

    // ---------------------------------------------------------------
    // DETAIL PESANAN
    // ---------------------------------------------------------------
    public function orderDetail(string $invoice)
    {
        $order = model('PenjualanModel')->getOrderByInvoice($invoice);

        // Validasi: pastikan order milik user yang sedang login
        $orderUserId = is_object($order) ? ($order->customer_id ?? null) : ($order['customer_id'] ?? null);

        if (!$order || (int) $orderUserId !== (int) $this->currentUser->id) {
            return redirect()->to('account/orders')
                ->with('error', 'Pesanan tidak ditemukan.');
        }

        $orderId = is_object($order) ? $order->id : $order['id'];
        $details = model('PenjualanDetailModel')->getDetailByPenjualan($orderId);

        $this->data['order']   = $order;
        $this->data['details'] = $details;
        $this->data['title']   = 'Detail Pesanan ' . $invoice;

        return view('themes/indomarket/account/order_detail', $this->data);
    }

    // ---------------------------------------------------------------
    // PROFIL
    // FIX ISSUE #8: null guard, pass $user terpisah + $currentUser
    // ---------------------------------------------------------------
    public function profile()
    {
        // Ambil data user terbaru langsung dari DB agar selalu up-to-date
        $user = \Config\Database::connect()
            ->table('users')
            ->where('id', $this->currentUser->id)
            ->get()
            ->getRow(); // getRow() mengembalikan object

        $this->data['user']  = $user ?? $this->currentUser;
        $this->data['title'] = 'Profil Saya';

        return view('themes/indomarket/account/profile', $this->data);
    }

    // ---------------------------------------------------------------
    // UPDATE PROFIL
    // FIX ISSUE #3:
    //   - Gunakan $db->table('users')->where()->update() dengan format benar
    //   - Pastikan hanya kolom yang ada di DB yang di-update
    //   - Tambahkan validasi minimal
    // ---------------------------------------------------------------
    public function updateProfile()
    {
        $builder = $db->table('users');

        $result = $builder->where('id', $userId)
                  ->update($updateData);

        if (!$result) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan profil. Coba lagi.');
        }

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[30]',
            'last_name'  => 'permit_empty|max_length[30]',
            'phone'      => 'permit_empty|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Data yang akan di-update — sesuaikan dengan kolom di tabel users
        $updateData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name'  => $this->request->getPost('last_name') ?? '',
            'phone'      => $this->request->getPost('phone') ?? '',
            'address'    => $this->request->getPost('alamat') ?? '',
            'city'       => $this->request->getPost('kota') ?? '',
            'province'   => $this->request->getPost('provinsi') ?? '',
            'postal_code' => $this->request->getPost('kode_pos') ?? '',
        ];

        // Upload avatar (opsional)
        $avatarFile = $this->request->getFile('avatar');
        if ($avatarFile && $avatarFile->isValid() && !$avatarFile->hasMoved()) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (in_array($avatarFile->getMimeType(), $allowedTypes)) {
                // Pastikan folder ada
                $uploadDir = FCPATH . 'uploads/avatars';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $avatarName = $avatarFile->getRandomName();
                $avatarFile->move($uploadDir, $avatarName);
                $updateData['avatar'] = 'uploads/avatars/' . $avatarName;
            }
        }

        // FIX: Gunakan QueryBuilder dengan benar
        $db = \Config\Database::connect();
        $db->table('users')
           ->where('id', $userId)
           ->update($updateData);

        // Cek apakah ada error
        if ($db->affectedRows() === false) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan profil. Coba lagi.');
        }

        return redirect()->to('account/profile')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    // ---------------------------------------------------------------
    // TRACK ORDER (dari akun)
    // ---------------------------------------------------------------
    public function trackOrder(string $invoice)
    {
        $order = model('PenjualanModel')->getOrderByInvoice($invoice);

        $orderUserId = is_object($order) ? ($order->customer_id ?? null) : ($order['customer_id'] ?? null);

        if (!$order || (int) $orderUserId !== (int) $this->currentUser->id) {
            return redirect()->to('account/orders');
        }

        $orderId = is_object($order) ? $order->id : $order['id'];

        $this->data['order']   = $order;
        $this->data['details'] = model('PenjualanDetailModel')->getDetailByPenjualan($orderId);
        $this->data['title']   = 'Lacak Pesanan ' . $invoice;

        return view('themes/indomarket/account/order_detail', $this->data);
    }
}