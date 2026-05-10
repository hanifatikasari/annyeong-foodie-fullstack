<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanModel extends Model
{
    protected $table         = 't_penjualan';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $allowedFields = [
        'invoice_no', 'total_harga', 'diskon',
        'total_bayar', 'pembayaran', 'uang_diterima', 'kasir_id',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateInvoice(): string
    {
        $prefix = 'INV-' . date('Ymd') . '-';
        $last   = $this->like('invoice_no', $prefix, 'after')
                       ->orderBy('id', 'DESC')->first();
        $next   = $last ? ((int) substr($last->invoice_no, -3)) + 1 : 1;
        return $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}