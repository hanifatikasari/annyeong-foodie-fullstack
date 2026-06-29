<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReportModel;
use Mpdf\Mpdf;
class Reports extends BaseController
{
    public function __construct()
    {
        helper(['url', 'form']);
    }

    public function index()
    {
        $data = [
            'title'               => 'Laporan',
            'currentAdminMenu'    => 'report',
            'currentAdminSubMenu' => 'report',
        ];
        return view('admin/reports/index', $data);
    }

    /**
     * Laporan Penjualan
     */
    public function penjualan()
    {
        $dari  = $this->request->getGet('dari')  ?? date('Y-m-01');
        $sampai = $this->request->getGet('sampai') ?? date('Y-m-d');

        $model = new ReportModel();

        $rows = $model->getLaporanPenjualan($dari, $sampai);
        $labaRows = $model->getLaporanLaba($dari, $sampai);
          
        // Index laba by tanggal
        $labaByTanggal = [];
        foreach ($labaRows as $l) {
            $labaByTanggal[$l->tanggal] = $l->laba;
        }

        // Merge laba ke rows
        foreach ($rows as &$r)
        {
            $r->laba  = $labaByTanggal[$r->tanggal] ?? 0;
            // sementara agar View lama tidak error
            $r->gross = $r->subtotal_produk;
            $r->nett  = $r->grand_total;
        }
        unset($r);

        $data = [
            'title'               => 'Laporan Penjualan',
            'currentAdminMenu'    => 'report',
            'currentAdminSubMenu' => 'report',
            'rows'                => $rows,
            'dari'                => $dari,
            'sampai'              => $sampai,
            'totalGross' => array_sum(array_column($rows, 'subtotal_produk')),
            'totalTransaksi'  => array_sum(array_column($rows, 'jumlah_transaksi')),
            'totalNett'  => array_sum(array_column($rows, 'grand_total')),
            'totalLaba'  => array_sum(array_column($rows, 'laba')),
            'totalDiskon'=> array_sum(array_column($rows, 'diskon')),
            'totalShipping'  => array_sum(array_column($rows,'shipping_cost')),
            'totalHandling'  => array_sum(array_column($rows,'handling_fee')),
        ];

        return view('admin/reports/penjualan', $data);
    }

    /**
     * Laporan Produksi
     */
    public function produksi()
    {
        $dari   = $this->request->getGet('dari')   ?? date('Y-m-01');
        $sampai = $this->request->getGet('sampai') ?? date('Y-m-d');

        $db = \Config\Database::connect();

        $rows = $db->table('t_produksi')
            ->select('t_produksi.*, products.name as nama_produk, products.hpp_total')
            ->join('products', 'products.id = t_produksi.product_id')
            ->where('tanggal_produksi >=', $dari)
            ->where('tanggal_produksi <=', $sampai)
            ->orderBy('tanggal_produksi', 'DESC')
            ->get()->getResult();

        $data = [
            'title'               => 'Laporan Produksi',
            'currentAdminMenu'    => 'report',
            'currentAdminSubMenu' => 'report',
            'rows'                => $rows,
            'dari'                => $dari,
            'sampai'              => $sampai,
            'totalQty'            => array_sum(array_column($rows, 'qty_hasil')),
            'totalHpp'            => array_sum(array_map(fn($r) => $r->qty_hasil * $r->hpp_total, $rows)),
        ];

        return view('admin/reports/produksi', $data);
    }

    /**
     * Laporan Stok Bahan Baku saat ini
     */
    public function stokBahan()
    {
        $bahan = model('BahanBakuModel')
            ->select('m_bahan_baku.*, categories.name as kategori')
            ->join('categories', 'categories.id = m_bahan_baku.category_id', 'left')
            ->where('m_bahan_baku.deleted_at IS NULL')
            ->orderBy('categories.name')
            ->orderBy('m_bahan_baku.nama_bahan')
            ->findAll();

        $data = [
            'title'               => 'Laporan Stok Bahan Baku',
            'currentAdminMenu'    => 'report',
            'currentAdminSubMenu' => 'report',
            'bahan'               => $bahan,
        ];

        return view('admin/reports/stok_bahan', $data);
    }

    /**
     * Laporan Metode Pembayaran
     */
    public function metode()
    {
        $dari   = $this->request->getGet('dari') ?? date('Y-m-01');
        $sampai = $this->request->getGet('sampai') ?? date('Y-m-d');
        $model = new ReportModel();
        $rows = $model->getLaporanMetodePembayaran($dari, $sampai);
        $data = [
            'title'               => 'Laporan Metode Pembayaran',
            'currentAdminMenu'    => 'report',
            'currentAdminSubMenu' => 'report',
            'rows'                => $rows,
            'dari'                => $dari,
            'sampai'              => $sampai,
            'totalOmzet' => array_sum(array_column($rows, 'total_omzet')),
            'totalTransaksi' => array_sum(array_column($rows, 'jumlah_transaksi')),
        ];
        return view('admin/reports/metode', $data);
    }

    public function penjualanPdf()
    {
        $dari   = $this->request->getGet('dari') ?? date('Y-m-01');
        $sampai = $this->request->getGet('sampai') ?? date('Y-m-d');
        $model = new \App\Models\ReportModel();

        $rows = $model->getLaporanPenjualan($dari, $sampai);
        $labaRows = $model->getLaporanLaba($dari, $sampai);

        $labaByTanggal = [];
        foreach ($labaRows as $l) {
            $labaByTanggal[$l->tanggal] = $l->laba;
        }
        foreach ($rows as &$r) {
            $r->laba = $labaByTanggal[$r->tanggal] ?? 0;
        }

        $session = session();
        $userId = $session->get('user_id');
        $admin = $this->db->table('users')
            ->select('users.first_name, users.last_name, groups.name')
            ->join('users_groups', 'users_groups.user_id = users.id')
            ->join('groups', 'groups.id = users_groups.group_id')
            ->where('users.id', $userId)
            ->get()
            ->getRow();
            
        $data = [
            'rows'         => $rows,
            'dari'         => $dari,
            'sampai'       => $sampai,
            'admin'        => $admin,
            'totalGross'   => array_sum(array_column($rows,'subtotal_produk')),
            'totalTransaksi'  => array_sum(array_column($rows, 'jumlah_transaksi')),
            'totalShipping'=> array_sum(array_column($rows,'shipping_cost')),
            'totalHandling'=> array_sum(array_column($rows,'handling_fee')),
            'totalDiskon'  => array_sum(array_column($rows,'diskon')),
            'totalNett'    => array_sum(array_column($rows,'grand_total')),
            'totalLaba'    => array_sum(array_column($rows,'laba')),
        ];

        $html = view('admin/reports/pdf_penjualan',$data);
        $mpdf = new Mpdf([
            'format' => 'A4-L'
        ]);

        

        $mpdf->SetTitle('Laporan Penjualan');
        $mpdf->SetFooter('Halaman {PAGENO}/{nbpg}');
        $mpdf->WriteHTML($html);

        return $this->response
            ->setHeader('Content-Type','application/pdf')
            ->setBody($mpdf->Output('laporan-penjualan.pdf','S'));
    }

    public function produksiPdf()
    {
        $dari   = $this->request->getGet('dari') ?? date('Y-m-01');
        $sampai = $this->request->getGet('sampai') ?? date('Y-m-d');

        $db = \Config\Database::connect();
        $rows = $db->table('t_produksi')
            ->select('t_produksi.*, products.name as nama_produk, products.hpp_total')
            ->join('products', 'products.id = t_produksi.product_id')
            ->where('tanggal_produksi >=', $dari)
            ->where('tanggal_produksi <=', $sampai)
            ->orderBy('tanggal_produksi', 'DESC')
            ->get()->getResult();

        $session = session();
        $userId = $session->get('user_id');
        $admin = $this->db->table('users')
            ->select('users.first_name, users.last_name, groups.name')
            ->join('users_groups', 'users_groups.user_id = users.id')
            ->join('groups', 'groups.id = users_groups.group_id')
            ->where('users.id', $userId)
            ->get()
            ->getRow();

        $data = [
            'rows'      => $rows,
            'dari'      => $dari,
            'sampai'    => $sampai,
            'admin'     => $admin,
            'totalQty'  => array_sum(array_column($rows, 'qty_hasil')),
            'totalHpp'  => array_sum(array_map(fn($r) => $r->qty_hasil * $r->hpp_total, $rows)),
        ];

        $html = view('admin/reports/pdf_produksi', $data);
        $mpdf = new Mpdf([
            'format' => 'A4-L'
        ]);

        $mpdf->SetTitle('Laporan Produksi');
        $mpdf->SetFooter('Halaman {PAGENO}/{nbpg}');
        $mpdf->WriteHTML($html);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setBody($mpdf->Output('laporan-produksi.pdf', 'S'));
    }

    public function stokBahanPdf()
    {
        $bahan = model('BahanBakuModel')
            ->select('m_bahan_baku.*, categories.name as kategori')
            ->join('categories', 'categories.id = m_bahan_baku.category_id', 'left')
            ->where('m_bahan_baku.deleted_at IS NULL')
            ->orderBy('categories.name')
            ->orderBy('m_bahan_baku.nama_bahan')
            ->findAll();

        $totalNilai = 0;
        foreach ($bahan as $item) {
            $totalNilai += $item->stok_sekarang * $item->harga_beli_satuan;
        }

        $session = session();
        $userId = $session->get('user_id');
        $admin = $this->db->table('users')
            ->select('users.first_name, users.last_name, groups.name')
            ->join('users_groups', 'users_groups.user_id = users.id')
            ->join('groups', 'groups.id = users_groups.group_id')
            ->where('users.id', $userId)
            ->get()
            ->getRow();

        $data = [
            'bahan'      => $bahan,
            'totalNilai' => $totalNilai,
            'admin'      => $admin,
        ];

        $html = view('admin/reports/pdf_stok_bahan', $data);
        $mpdf = new Mpdf([
            'format' => 'A4-L'
        ]);

        $mpdf->SetTitle('Laporan Stok Bahan Baku');
        $mpdf->SetFooter('Halaman {PAGENO}/{nbpg}');
        $mpdf->WriteHTML($html);

        $response = $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setBody($mpdf->Output('laporan-stok-bahan.pdf', 'S'));

        return $response;
    }

    public function metodePdf()
    {
        $dari   = $this->request->getGet('dari') ?? date('Y-m-01');
        $sampai = $this->request->getGet('sampai') ?? date('Y-m-d');
        $model = new \App\Models\ReportModel();

        $rows = $model->getLaporanMetodePembayaran($dari, $sampai);
        $totalOmzet = array_sum(array_column($rows, 'total_omzet'));
        $totalTransaksi = array_sum(array_column($rows, 'jumlah_transaksi'));

        $session = session();
        $userId = $session->get('user_id');
        $admin = $this->db->table('users')
            ->select('users.first_name, users.last_name, groups.name')
            ->join('users_groups', 'users_groups.user_id = users.id')
            ->join('groups', 'groups.id = users_groups.group_id')
            ->where('users.id', $userId)
            ->get()
            ->getRow();

        $data = [
            'rows'           => $rows,
            'dari'           => $dari,
            'sampai'         => $sampai,
            'admin'          => $admin,
            'totalOmzet'     => $totalOmzet,
            'totalTransaksi' => $totalTransaksi,
        ];

        $html = view('admin/reports/pdf_metode', $data);
        $mpdf = new Mpdf([
            'format' => 'A4-L'
        ]);

        $mpdf->SetTitle('Laporan Metode Pembayaran');
        $mpdf->SetFooter('Halaman {PAGENO}/{nbpg}');
        $mpdf->WriteHTML($html);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setBody($mpdf->Output('laporan-metode-pembayaran.pdf', 'S'));
    }
}