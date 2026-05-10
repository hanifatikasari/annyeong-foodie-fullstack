<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Penjualan extends BaseController
{
    public function __construct()
    {
        helper(['url', 'form']);
    }

    public function index()
    {
        $keyword = $this->request->getGet('table_search');
        $perPage = $this->request->getGet('perPage') ?? 10;

        $model = model('PenjualanModel');
        $model->select('t_penjualan.*, users.first_name, users.last_name')
              ->join('users', 'users.id = t_penjualan.kasir_id', 'left');

        if (!empty($keyword)) {
            $model->like('t_penjualan.invoice_no', $keyword);
        }

        $data = [
            'title'               => 'Data Penjualan',
            'currentAdminMenu'    => 'sales',
            'currentAdminSubMenu' => 'order',
            'penjualan'           => $model->orderBy('t_penjualan.id', 'DESC')->paginate($perPage, 'bootstrap'),
            'pager'               => $model->pager,
            'keyword'             => $keyword,
            'perPage'             => $perPage,
        ];

        return view('admin/penjualan/index', $data);
    }

    public function create()
    {
        // Hanya produk yang punya stok > 0
        $products = model('ProductModel')
            ->select('products.id, products.name, products.sku, products.price, products.hpp_total, product_inventories.qty')
            ->join('product_inventories', 'product_inventories.product_id = products.id')
            ->where('products.type', 'simple')
            ->where('products.parent_id IS NULL')
            ->where('products.deleted_at IS NULL')
            ->where('product_inventories.qty >', 0)
            ->orderBy('products.name')
            ->findAll();

        $data = [
            'title'               => 'Transaksi Penjualan Baru',
            'currentAdminMenu'    => 'sales',
            'currentAdminSubMenu' => 'order',
            'products'            => $products,
        ];

        return view('admin/penjualan/create', $data);
    }

    /**
     * Simpan transaksi penjualan:
     * 1. Simpan header t_penjualan
     * 2. Simpan detail (snapshot harga)
     * 3. Kurangi stok product_inventories
     */
    public function simpan()
    {
        $items = $this->request->getPost('items'); // array [{product_id, qty}]

        if (empty($items)) {
            return redirect()->back()->with('error', 'Keranjang belanja kosong!');
        }

        $pembayaran   = $this->request->getPost('pembayaran');
        $uangDiterima = (int) $this->request->getPost('uang_diterima');
        $diskon       = (int) ($this->request->getPost('diskon') ?? 0);
        $kasirId      = session()->get('user_id');

        // Hitung total dari item
        $totalHarga = 0;
        $orderItems = [];

        foreach ($items as $item) {
            $product = model('ProductModel')
                ->select('products.id, products.name, products.price, products.hpp_total, product_inventories.qty')
                ->join('product_inventories', 'product_inventories.product_id = products.id', 'left')
                ->find($item['product_id']);

            if (!$product) continue;

            $qty      = (int) $item['qty'];
            $stok     = (int) ($product->qty ?? 0);

            if ($qty <= 0) continue;
            if ($qty > $stok) {
                return redirect()->back()->with('error',
                    "Stok {$product->name} tidak cukup! Stok: $stok, dipesan: $qty");
            }

            $subtotal    = $product->price * $qty;
            $totalHarga += $subtotal;

            $orderItems[] = [
                'product'  => $product,
                'qty'      => $qty,
                'subtotal' => $subtotal,
            ];
        }

        if (empty($orderItems)) {
            return redirect()->back()->with('error', 'Tidak ada item valid.');
        }

        $totalBayar = $totalHarga - $diskon;

        if ($pembayaran === 'Cash' && $uangDiterima < $totalBayar) {
            return redirect()->back()->with('error',
                'Uang yang diterima kurang! Total: Rp ' . number_format($totalBayar));
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $penjualanModel = model('PenjualanModel');
            $detailModel    = model('PenjualanDetailModel');
            $inventoryModel = model('ProductInventoryModel');

            // 1. Simpan header
            $invoiceNo = $penjualanModel->generateInvoice();
            $penjualanModel->save([
                'invoice_no'    => $invoiceNo,
                'total_harga'   => $totalHarga,
                'diskon'        => $diskon,
                'total_bayar'   => $totalBayar,
                'pembayaran'    => $pembayaran,
                'uang_diterima' => $uangDiterima,
                'kasir_id'      => $kasirId,
            ]);
            $penjualanId = $db->insertID();

            // 2. Simpan detail & kurangi stok
            foreach ($orderItems as $oi) {
                $detailModel->save([
                    'penjualan_id'  => $penjualanId,
                    'product_id'    => $oi['product']->id,
                    'qty'           => $oi['qty'],
                    'hpp_price'     => $oi['product']->hpp_total,   // snapshot HPP
                    'selling_price' => $oi['product']->price,        // snapshot harga jual
                    'subtotal'      => $oi['subtotal'],
                ]);

                // Kurangi stok
                $inv = $inventoryModel->where('product_id', $oi['product']->id)->first();
                if ($inv) {
                    $inventoryModel->update($inv->id, ['qty' => $inv->qty - $oi['qty']]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi gagal.');
            }

            $kembalian = max(0, $uangDiterima - $totalBayar);

            return redirect()->to('admin/penjualan/show/' . $penjualanId)
                ->with('success', "Transaksi $invoiceNo berhasil! " .
                    ($kembalian > 0 ? "Kembalian: Rp " . number_format($kembalian) : ''));

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $penjualan = model('PenjualanModel')
            ->select('t_penjualan.*, users.first_name, users.last_name')
            ->join('users', 'users.id = t_penjualan.kasir_id', 'left')
            ->find($id);

        if (!$penjualan) {
            return redirect()->to('admin/penjualan')->with('error', 'Data tidak ditemukan.');
        }

        $details = model('PenjualanDetailModel')
            ->select('t_penjualan_detail.*, products.name as nama_produk, products.sku')
            ->join('products', 'products.id = t_penjualan_detail.product_id')
            ->where('penjualan_id', $id)
            ->findAll();

        $data = [
            'title'               => 'Detail Transaksi: ' . $penjualan->invoice_no,
            'currentAdminMenu'    => 'sales',
            'currentAdminSubMenu' => 'order',
            'penjualan'           => $penjualan,
            'details'             => $details,
        ];

        return view('admin/penjualan/show', $data);
    }
}