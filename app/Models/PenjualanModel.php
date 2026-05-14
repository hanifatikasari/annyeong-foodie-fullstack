<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PenjualanModel
 *
 * FIX ISSUE #6: Konsistensi nama method — gunakan getOrdersByUser()
 * FIX: returnType = 'object' sesuai requirement project
 */
class PenjualanModel extends Model
{
    protected $table            = 't_penjualan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';  // FIX: harus object, bukan array
    protected $allowedFields    = [
        'invoice_no', 'total_harga', 'diskon', 'total_bayar',
        'pembayaran', 'uang_diterima', 'kasir_id', 'customer_id',
        'status_order', 'nama_penerima', 'no_hp_penerima',
        'alamat_pengiriman', 'catatan_order', 'bukti_bayar',
        'verified_at', 'verified_by',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate nomor invoice unik.
     * Format: AES-YYYYMMDD-001
     */
    public function generateInvoiceNo(): string
    {
        $date   = date('Ymd');
        $prefix = 'AES-' . $date . '-';

        $last = $this->like('invoice_no', $prefix, 'after')
                     ->orderBy('id', 'DESC')
                     ->first();

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last->invoice_no ?? '');
            $seq   = ((int) end($parts)) + 1;
        }

        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    /**
     * FIX ISSUE #6: Nama method yang benar dan konsisten.
     * Mengambil semua order online milik user tertentu.
     */
    public function getOrdersByUser(int $userId): array
    {
        return $this->where('customer_id', $userId)
                    ->orderBy('id', 'DESC')
                    ->findAll();
    }

    /**
     * Alias untuk backward compatibility jika ada kode yang memanggil nama lama.
     */
    public function getOrdersByCustomer(int $customerId): array
    {
        return $this->getOrdersByUser($customerId);
    }

    /**
     * Ambil order berdasarkan nomor invoice.
     */
    public function getOrderByInvoice(string $invoice): ?object
    {
        return $this->where('invoice_no', $invoice)->first();
    }

    /**
     * Ambil semua order online yang menunggu verifikasi.
     */
    public function getPendingPayments(): array
    {
        return $this->whereIn('status_order', ['pending_payment', 'pending_verification'])
                    ->orderBy('id', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil semua order online (untuk admin).
     */
    public function getOnlineOrders(string $status = ''): array
    {
        $query = $this;
        if ($status) {
            $query->where('status_order', $status);
        }
        return $query->orderBy('id', 'DESC')->findAll();
    }
}