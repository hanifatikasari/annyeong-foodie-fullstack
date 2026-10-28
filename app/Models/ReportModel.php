<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportModel extends Model
{
    protected $DBGroup = 'default';

    /**
     * Query dasar transaksi yang dihitung sebagai omzet
     * POS + Online Completed
     */
    private function penjualanBase()
    {
        return $this->db->table('t_penjualan')
            ->where("
                (
                    invoice_no LIKE 'INV-%'
                    OR (
                        invoice_no LIKE 'BTB-%'
                        AND order_status = 'completed'
                    )
                )
            ", null, false);
    }

    /**
     * Laporan Penjualan
     */
    public function getLaporanPenjualan($dari, $sampai)
    {
        return $this->penjualanBase()
            ->select("
                DATE(created_at) as tanggal,
                COUNT(*) AS jumlah_transaksi,
                SUM(subtotal_produk) as subtotal_produk,
                SUM(shipping_cost) as shipping_cost,
                SUM(handling_fee) as handling_fee,
                SUM(diskon) as diskon,
                SUM(grand_total) as grand_total
            ")
            ->where('DATE(created_at) >=', $dari)
            ->where('DATE(created_at) <=', $sampai)
            ->groupBy('DATE(created_at)')
            ->orderBy('tanggal', 'ASC')
            ->get()
            ->getResult();
    }

    /**
     * Laba Penjualan
     */
    public function getLaporanLaba($dari, $sampai)
    {
        return $this->db->table('t_penjualan_detail')
            ->select("
                DATE(t_penjualan.created_at) as tanggal,
                SUM(
                    (selling_price - hpp_price) * qty
                ) as laba
            ")
            ->join(
                't_penjualan',
                't_penjualan.id=t_penjualan_detail.penjualan_id'
            )
            ->where("
                (
                    t_penjualan.invoice_no LIKE 'INV-%'
                    OR (
                        t_penjualan.invoice_no LIKE 'BTB-%'
                        AND t_penjualan.order_status='completed'
                    )
                )
            ", null, false)
            ->where('DATE(t_penjualan.created_at) >=', $dari)
            ->where('DATE(t_penjualan.created_at) <=', $sampai)
            ->groupBy('DATE(t_penjualan.created_at)')
            ->get()
            ->getResult();
    }

    /**
     * Laporan Metode Pembayaran
     */
    public function getLaporanMetodePembayaran($dari, $sampai)
    {
        return $this->penjualanBase()
            ->select("
                pembayaran,
                COUNT(id) AS jumlah_transaksi,
                SUM(grand_total) AS total_omzet
            ")
            ->where('DATE(created_at) >=', $dari)
            ->where('DATE(created_at) <=', $sampai)
            ->groupBy('pembayaran')
            ->orderBy('jumlah_transaksi', 'DESC')
            ->get()
            ->getResult();
    }
}