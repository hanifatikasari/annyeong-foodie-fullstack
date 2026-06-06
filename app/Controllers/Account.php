<?php

namespace App\Controllers;

use App\Models\PenjualanModel;

class Account extends BaseController
{
    // ---------------------------------------------------------------
    // DASHBOARD
    // FIX ISSUE #7: gunakan getOrdersByCustomer() secara konsisten
    // ---------------------------------------------------------------
    public function index()
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to('auth/login');
        }

        $userId = $this->currentUser->id ?? 0;
        $orders = model('PenjualanModel')->getOrdersByCustomer($userId);
        $recentOrder = model('PenjualanModel')->getLatestOrderByCustomer($userId);

        $pendingCount = 0;
        $completedCount = 0;
        foreach ($orders as $o) {
            $status = $o->order_status ?? '';
            if (in_array($status, ['pending_payment', 'pending_verification'])) {
                $pendingCount++;
            }
            if ($status === 'completed') {
                $completedCount++;
            }
        }

        $this->data['orders']         = $orders;
        $this->data['recentOrder']    = $recentOrder;
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
        if (!$this->auth->loggedIn()) {
            return redirect()->to('auth/login');
        }

        $userId = $this->currentUser->id ?? 0;
        $this->data['orders'] = model('PenjualanModel')->getOrdersByCustomer($userId);
        $this->data['title']  = 'Riwayat Pesanan';

        return view('themes/indomarket/account/orders', $this->data);
    }

    // ---------------------------------------------------------------
    // DETAIL PESANAN
    // ---------------------------------------------------------------
    public function orderDetail($id)
    {
        // Pastikan user sudah login
        if (!$this->auth->loggedIn()) {
            return redirect()->to('auth/login');
        }

        // Ambil order + data customer berdasarkan ID
        $order = model('PenjualanModel')->getOrderWithCustomerById((int)$id);


        // Jika order tidak ditemukan
        if (!$order) {
            return redirect()->to('account/orders')
                ->with('error', 'Pesanan tidak ditemukan.');
        }

        // Validasi: pastikan order milik user yang sedang login
        // Kolom yang digunakan adalah customer_id
        if ((int) ($order->customer_id ?? 0) !== (int) $this->currentUser->id) {
            return redirect()->to('account/orders')
                ->with('error', 'Akses ditolak.');
        }

        // Ambil detail item pesanan
        $details = model('PenjualanDetailModel')->getDetailByPenjualan($order->id);

        // Kirim data ke view
        $this->data['order']   = $order;
        $this->data['details'] = $details;
        $this->data['title']   = 'Detail Pesanan ' . $order->invoice_no;

        // Tampilkan halaman detail pesanan
        return view('themes/indomarket/account/order_detail', $this->data);
    }

    // ---------------------------------------------------------------
    // PROFIL
    // FIX ISSUE #8: ambil data terbaru dari DB, null-safe
    // ---------------------------------------------------------------
    public function profile()
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to('auth/login');
        }

        // Ambil data terbaru langsung dari DB (bukan dari session Ion Auth)
        $user = \Config\Database::connect()
            ->table('users')
            ->where('id', $this->currentUser->id)
            ->get()
            ->getRow(); // getRow() → object

        $this->data['user']  = $user ?? $this->currentUser;
        $this->data['title'] = 'Profil Saya';

        return view('themes/indomarket/account/profile', $this->data);
    }

    // ---------------------------------------------------------------
    // UPDATE PROFIL
    // FIX ISSUE #1: Pemetaan field form → kolom DB yang benar
    //
    // Form menggunakan nama Indonesia:
    //   alamat   → address
    //   kota     → city
    //   provinsi → province
    //   kode_pos → postal_code
    //
    // Kolom DB aktual (dari prompt):
    //   address, city, province, postal_code
    // ---------------------------------------------------------------
    public function updateProfile()
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to('auth/login');
        }

        $userId = $this->currentUser->id;

        // Validasi minimal
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name'  => 'permit_empty|max_length[50]',
            'phone'      => 'permit_empty|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // FIX #1: Peta nama field form → nama kolom DB aktual
        $updateData = [
            'first_name'  => $this->request->getPost('first_name'),
            'last_name'   => $this->request->getPost('last_name') ?? '',
            'phone'       => $this->request->getPost('phone') ?? '',
            'address'     => $this->request->getPost('alamat') ?? '',      // form: alamat  → DB: address
            'city'        => $this->request->getPost('kota') ?? '',        // form: kota    → DB: city
            'province'    => $this->request->getPost('provinsi') ?? '',    // form: provinsi → DB: province
            'postal_code' => $this->request->getPost('kode_pos') ?? '',   // form: kode_pos → DB: postal_code
        ];

        // Upload avatar (opsional)
        $avatarFile = $this->request->getFile('avatar');
        if ($avatarFile && $avatarFile->isValid() && !$avatarFile->hasMoved()) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

            if (in_array($avatarFile->getMimeType(), $allowedTypes)
                && $avatarFile->getSize() <= 2 * 1024 * 1024) {

                $uploadDir = FCPATH . 'uploads/avatars';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $avatarName = $avatarFile->getRandomName();
                $avatarFile->move($uploadDir, $avatarName);
                $updateData['avatar'] = 'uploads/avatars/' . $avatarName;
            }
        }

        // FIX #1: Gunakan QueryBuilder raw — lebih aman dan langsung
        $db     = \Config\Database::connect();
        $result = $db->table('users')
                     ->where('id', $userId)
                     ->update($updateData);

        if ($result === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan profil. Silakan coba lagi.');
        }

        return redirect()->to('account/profile')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    // ---------------------------------------------------------------
    // TRACK ORDER (dari halaman akun)
    // ---------------------------------------------------------------
    public function trackOrder(string $invoice)
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to('auth/login');
        }

        $order = model('PenjualanModel')->getOrderByInvoice($invoice);

        if (!$order || (int) ($order->customer_id ?? 0) !== (int) $this->currentUser->id) {
            return redirect()->to('account/orders');
        }

        $this->data['order']   = $order;
        $this->data['details'] = model('PenjualanDetailModel')->getDetailByPenjualan($order->id);
        $this->data['title']   = 'Lacak Pesanan ' . $invoice;

        return view('themes/indomarket/account/order_detail', $this->data);
    }
}