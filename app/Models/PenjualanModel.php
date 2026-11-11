<?php
namespace App\Models;
use CodeIgniter\Model;
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
        'subtotal_produk',
        'diskon',
        'promo_id',
        'kode_promo',
        'grand_total',
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
        // === Kolom Midtrans (ditambahkan) ===
        'snap_token',
        'midtrans_transaction_id',
        'midtrans_payment_type',
        'midtrans_status',
        'midtrans_order_id',
        // === Kolom Ongkir (ditambahkan) ===
        'delivery_type',
        'shipping_area_id',
        'shipping_cost',
        'handling_fee',
        'delivery_courier_id',
         // === Snapshot data pengiriman ===
        'shipping_recipient_name',
        'shipping_recipient_phone',
        'shipping_area_name',
        'shipping_province',
        'shipping_city',
        'shipping_postal_code',
        'shipping_latitude',
        'shipping_longitude',
        'shipping_gmaps_link',
    ];

    // ----------------------------------------------------------------
    // Generate nomor invoice online — Format: BTB-YYYYMMDD-001
    // ----------------------------------------------------------------
    public function generateOnlineInvoice(): string
    {
        $date   = date('Ymd');
        $prefix = 'BTB-' . $date . '-';

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

    // ----------------------------------------------------------------
    // Generate nomor invoice POS — Format: INV-YYYYMMDD-001
    // ----------------------------------------------------------------
    public function generatePosInvoice(): string
    {
        $date   = date('Ymd');
        $prefix = 'INV-' . $date . '-';

        $last = $this->like('invoice_no', $prefix, 'after')
                    ->orderBy('id', 'DESC')
                    ->first();

        $seq = 1;
        if ($last && !empty($last->invoice_no)) {
            $parts = explode('-', $last->invoice_no);
            $seq = ((int) end($parts)) + 1;
        }

        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
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
                        users.postal_code,
                        m_shipping_area.area_name AS shipping_area_name,
                        CONCAT(courier.first_name, " ", courier.last_name) AS courier_name
                    ')
                    ->join('users', 'users.id = t_penjualan.customer_id', 'left')
                    ->join('m_shipping_area', 'm_shipping_area.id = t_penjualan.shipping_area_id', 'left')
                    ->join('users courier', 'courier.id = t_penjualan.delivery_courier_id', 'left')    
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
                        users.postal_code,
                        m_shipping_area.area_name AS shipping_area_name,
                        CONCAT(courier.first_name, " ", courier.last_name) AS courier_name,
                        CONCAT(kasir.first_name, " ", kasir.last_name) AS kasir_name,
                    ')
                    ->join('users', 'users.id = t_penjualan.customer_id', 'left')
                    ->join('m_shipping_area', 'm_shipping_area.id = t_penjualan.shipping_area_id', 'left')
                    ->join('users courier', 'courier.id = t_penjualan.delivery_courier_id', 'left')
                    ->join('users kasir', 'kasir.id = t_penjualan.kasir_id', 'left')
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
                            ) AS customer_name,
                            m_shipping_area.area_name AS shipping_area_name,
                            CONCAT(courier.first_name, " ", courier.last_name) AS courier_name
                        ')
                        ->join('users', 'users.id = t_penjualan.customer_id', 'left')
                        ->join('m_shipping_area', 'm_shipping_area.id = t_penjualan.shipping_area_id', 'left')
                        ->join('users courier', 'courier.id = t_penjualan.delivery_courier_id', 'left')
                        ->like('t_penjualan.invoice_no', 'BTB-', 'after'); // Hanya invoice online (BTB-)

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
            'payment_status' => 'paid',
            'order_status'   => 'processing',
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