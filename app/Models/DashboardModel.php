<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $DBGroup = 'default';

    /**
     * Omzet hari ini
     */
    public function getPenjualanHariIni()
    {
        return $this->db->query("
            SELECT
                SUM(total_bayar) AS total,
                COUNT(*) AS jumlah
            FROM t_penjualan
            WHERE DATE(created_at)=?
            AND (
                    invoice_no LIKE 'INV-%'
                 OR (
                        invoice_no LIKE 'AES-%'
                        AND order_status='completed'
                    )
            )
        ", [date('Y-m-d')])->getRow();
    }

    /**
     * Produksi hari ini
     */
    public function getProduksiHariIni()
    {
        return $this->db->table('t_produksi')
            ->selectSum('qty_hasil', 'total')
            ->selectCount('id', 'jumlah')
            ->where('tanggal_produksi', date('Y-m-d'))
            ->where('status_qc', 'Lolos')
            ->get()
            ->getRow();
    }

    /**
     * Jumlah bahan baku menipis
     */
    public function getBahanMenipis()
    {
        return $this->db->table('m_bahan_baku')
            ->where('stok_sekarang <= stok_minimal', null, false)
            ->where('deleted_at IS NULL')
            ->countAllResults();
    }

    /**
     * Total pelanggan
     */
    public function getTotalPelanggan()
    {
        return $this->db->table('users_groups')
            ->join('groups', 'groups.id=users_groups.group_id')
            ->where('groups.name', 'pelanggan')
            ->countAllResults();
    }

    /**
     * Grafik omzet 7 hari
     */
    public function getGrafikPenjualan()
    {
        $grafik = [];

        for ($i=6;$i>=0;$i--)
        {
            $tgl=date('Y-m-d',strtotime("-$i days"));

            $row=$this->db->query("
                SELECT SUM(total_bayar) total
                FROM t_penjualan
                WHERE DATE(created_at)=?
                AND (
                        invoice_no LIKE 'INV-%'
                     OR (
                            invoice_no LIKE 'AES-%'
                            AND order_status='completed'
                        )
                )
            ",[$tgl])->getRow();

            $grafik[]=[
                'tanggal'=>date('d/m',strtotime($tgl)),
                'total'=>(int)($row->total ?? 0)
            ];
        }

        return $grafik;
    }

    /**
     * Produk stok menipis
     */
    public function getStokMenipis()
    {
        return $this->db->table('products')
            ->select('products.name,
                      products.sku,
                      product_inventories.qty,
                      product_inventories.low_stock_threshold')
            ->join(
                'product_inventories',
                'product_inventories.product_id=products.id'
            )
            ->where(
                'product_inventories.qty <= product_inventories.low_stock_threshold',
                null,
                false
            )
            ->where('products.deleted_at IS NULL')
            ->orderBy('product_inventories.qty','ASC')
            ->limit(5)
            ->get()
            ->getResult();
    }

    /**
     * Top produk 30 hari
     */
    public function getTopProduk()
    {
        return $this->db->table('t_penjualan_detail')
            ->select("
                products.name,
                SUM(t_penjualan_detail.qty) total_qty,
                SUM(t_penjualan_detail.subtotal) total_omzet
            ")
            ->join('products','products.id=t_penjualan_detail.product_id')
            ->join('t_penjualan','t_penjualan.id=t_penjualan_detail.penjualan_id')
            ->where('t_penjualan.created_at >=',date('Y-m-d',strtotime('-30 days')))
            ->where("
                    (
                        t_penjualan.invoice_no LIKE 'INV-%'
                    OR (
                        t_penjualan.invoice_no LIKE 'AES-%'
                        AND t_penjualan.order_status='completed'
                    )
                )
            ",null,false)
            ->groupBy('t_penjualan_detail.product_id')
            ->orderBy('total_qty','DESC')
            ->limit(5)
            ->get()
            ->getResult();
    }
}