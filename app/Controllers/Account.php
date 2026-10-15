<?php

namespace App\Controllers;

use App\Models\PenjualanModel;
use App\Models\ShippingAreaModel;

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
            if (in_array($status, ['pending'])) {
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

        $db = \Config\Database::connect();
        // Ambil data terbaru langsung dari DB (bukan dari session Ion Auth)
        $user = $db
            ->table('users')
            ->where('id', $this->currentUser->id)
            ->get()
            ->getRow(); // getRow() → object

             // Ambil area pengiriman yang aktif
        $shippingAreas = model(ShippingAreaModel::class)
            ->where('is_active', 1)
            ->orderBy('area_name', 'ASC')
            ->findAll();

        $this->data['user']  = $user ?? $this->currentUser;
        $this->data['shippingAreas'] = $shippingAreas;
        $this->data['title'] = 'Profil Saya';

         // Pilihan wilayah untuk formulir Batterbit
        $this->data['provinceOptions'] = [
            'Jawa Tengah',
        ];

        $this->data['cityOptions'] = [
            'Batang', 'Pekalongan'
        ];

        // Sesuaikan daftar ini dengan kode pos yang sudah kamu verifikasi
        $this->data['postalCodeOptions'] = [
            '51274','51273', '51271'
        ];

        return view('themes/indomarket/account/profile', $this->data);
    }

    // ---------------------------------------------------------------
    // UPDATE PROFIL
    // ---------------------------------------------------------------
    public function updateProfile()
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to('auth/login');
        }

        $userId = $this->currentUser->id;

        $provinceOptions = ['Jawa Tengah'];
        $cityOptions = ['Batang', 'Pekalongan'];
        $postalCodeOptions = ['51274','51273', '51271'];
        // Validasi minimal
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name'  => 'permit_empty|max_length[50]',
            'phone'      => 'permit_empty|max_length[20]',
            'alamat'     => 'permit_empty|max_length[255]',
            'provinsi'   => 'required|in_list[' . implode(',', $provinceOptions) . ']',
            'kota'       => 'required|in_list[' . implode(',', $cityOptions) . ']',
            'kode_pos'   => 'required|in_list[' . implode(',', $postalCodeOptions) . ']',
            'shipping_area_id' => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

            $db = \Config\Database::connect();

        // Pastikan area yang dipilih benar-benar ada dan aktif.
        // Validasi ini penting karena dropdown bisa dimanipulasi.
        $areaId = (int) $this->request->getPost('shipping_area_id');

        $area = $db->table('m_shipping_area')
            ->where('id', $areaId)
            ->where('is_active', 1)
            ->get()
            ->getRow();

        if (!$area) {
            return redirect()->back()
                ->withInput()
                ->with('errors', [
                    'Area pengiriman tidak valid atau sudah tidak aktif.',
                ]);
        }

        // FIX #1: Peta nama field form → nama kolom DB aktual
        $updateData = [
            'first_name'  => $this->request->getPost('first_name'),
            'last_name'   => $this->request->getPost('last_name') ?? '',
            'phone'       => $this->request->getPost('phone') ?? '',
            'address'     => $this->request->getPost('alamat') ?? '',
            'shipping_area_id' => $areaId,
            'gmaps'       => $this->request->getPost('gmaps_link') ?? '', // form: gmaps_link → DB: gmaps
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