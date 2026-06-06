<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PenjualanModel — disesuaikan dengan skema DB aktual.
 *
 * Skema DB aktual dari prompt:
 *   t_penjualan:
 *     - id
 *     - invoice_no        (bukan invoice)
 *     - customer_id    (bukan user_id)
 *     - total_harga
 *     - diskon
 *     - total_bayar
 *     - pembayaran
 *     - uang_diterima
 *     - kasir_id
 *     - tipe_order
 *     - order_status   (bukan status_order)
 *     - payment_status
 *     - nama_penerima
 *     - no_hp_penerima
 *     - shipping_address (bukan alamat_pengiriman)
 *     - catatan_order
 *     - payment_proof   (bukan bukti_bayar)
 *     - verified_at
 *     - verified_by
 *     - created_at
 *     - updated_at
 */
class PenjualanModel extends Model
{
    protected $table            = 't_penjualan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'invoice_no',
        'customer_id',
        'total_harga',
        'diskon',
        'total_bayar',
        'pembayaran',
        'uang_diterima',
        'kasir_id',
        'order_status',
        'payment_status',
        'shipping_address',
        'catatan_customer',
        'payment_proof',
        'verified_at',
        'verified_by',
        'created_at',
        'updated_at',
    ];

    // ----------------------------------------------------------------
    // Generate nomor invoice online — Format: AES-YYYYMMDD-001
    // ----------------------------------------------------------------
    public function generateInvoiceNo(): string
    {
        $date   = date('Ymd');
        $prefix = 'AES-' . $date . '-';

        $last = $this->like('invoice_no', $prefix, 'after')
                     ->orderBy('id', 'DESC')
                     ->first();

        $seq = 1;
        if ($last && !empty($last->invoice_no)) {
            $parts = explode('-', $last->invoice_no ?? '');
            $seq   = ((int) end($parts)) + 1;
        }

        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Alias untuk kompatibilitas kode lama
     * yang masih memanggil generateInvoice().
     */
    public function generateInvoice(): string
    {
        return $this->generateInvoiceNo();
    }

    // ----------------------------------------------------------------
    // FIX ISSUE #7: getOrdersByCustomer() sebagai nama canonical
    // ----------------------------------------------------------------
    public function getOrdersByCustomer(int $customerId): array
    {
        return $this->where('customer_id', $customerId)
                    ->orderBy('id', 'DESC')
                    ->findAll();
    }

    /**
     * Alias — agar tidak break kode lama yang masih pakai getOrdersByUser()
     */
    public function getOrdersByUser(int $userId): array
    {
        return $this->getOrdersByCustomer($userId);
    }

    // ----------------------------------------------------------------
    // Ambil order berdasarkan invoice number
    // ----------------------------------------------------------------
    public function getOrderByInvoice(string $invoice): ?object
    {
        return $this->where('invoice_no', $invoice)->first();
    }

     /**
     * Ambil satu order + data customer berdasarkan invoice.
     *
     * Field tambahan:
     * - first_name
     * - last_name
     * - phone
     * - email
     * - address
     * - city
     * - province
     * - postal_code
     */
     public function getOrderWithCustomerByInvoice(string $invoice): ?object
    {
        return $this->select('
                        t_penjualan.*,
                        users.first_name,
                        users.last_name,
                        users.phone,
                        users.email,
                        users.address,
                        users.city,
                        users.province,
                        users.postal_code
                    ')
                    ->join('users', 'users.id = t_penjualan.customer_id', 'left')
                    ->where('t_penjualan.invoice_no', $invoice)
                    ->first();
    }

     /**
     * Ambil satu order + data customer berdasarkan ID.
     */
    public function getOrderWithCustomerById(int $id): ?object
    {
        return $this->select('
                        t_penjualan.*,
                        users.first_name,
                        users.last_name,
                        users.phone,
                        users.email,
                        users.address,
                        users.city,
                        users.province,
                        users.postal_code
                    ')
                    ->join('users', 'users.id = t_penjualan.customer_id', 'left')
                    ->where('t_penjualan.id', $id)
                    ->first();
    }

     /**
     * Ambil semua pesanan online untuk admin.
     *
     * Optional filter:
     * - status
     * - pagination
     */
    public function getOnlineOrdersForAdmin(string $status = '', int $perPage = 15)
    {
        $builder = $this->select('
                            t_penjualan.*,
                            users.first_name,
                            users.last_name,
                            users.phone,
                            users.email,
                            CONCAT(
                                COALESCE(users.first_name, ""),
                                " ",
                                COALESCE(users.last_name, "")
                            ) AS customer_name
                        ')
                        ->join('users', 'users.id = t_penjualan.customer_id', 'left')
                        ->like('t_penjualan.invoice_no', 'AES-', 'after'); // Hanya invoice online (AES-)

        if (!empty($status)) {
            $builder->where('t_penjualan.order_status', $status);
        }

        $builder->orderBy('t_penjualan.id', 'DESC');

        // Jika digunakan dengan paginate(), controller bisa memanggil:
        // $model->getOnlineOrdersForAdmin(...)->paginate(...)
        return $builder;
    }

    /**
     * Ambil semua transaksi POS (invoice INV-).
     */
    public function getPosSales()
    {
        return $this->select('
                        t_penjualan.*,
                        users.first_name,
                        users.last_name
                    ')
                    ->join('users', 'users.id = t_penjualan.kasir_id', 'left')
                    ->like('t_penjualan.invoice_no', 'INV-', 'after')
                    ->orderBy('t_penjualan.id', 'DESC');
    }

    /**
     * Verifikasi pembayaran.
     */
    public function verifyPayment(int $id, ?int $verifiedBy = null): bool
    {
        return (bool) $this->update($id, [
            'payment_status' => 'verified',
            'order_status'   => 'verified',
            'verified_at'    => date('Y-m-d H:i:s'),
            'verified_by'    => $verifiedBy,
        ]);
    }

    /**
     * Update status order.
     */
   public function updateOrderStatus(int $id, string $status): bool
    {
        return $this->update($id, [
            'order_status' => $status,
        ]);
    }

    /**
     * Ambil pesanan terbaru customer.
     */
    public function getLatestOrderByCustomer(int $customerId): ?object
    {
        return $this->where('customer_id', $customerId)
                    ->orderBy('id', 'DESC')
                    ->first();
    }

    /**
     * Hitung total pesanan customer.
     */
    public function countOrdersByCustomer(int $customerId): int
    {
        return $this->where('customer_id', $customerId)
                    ->countAllResults();
    }
}