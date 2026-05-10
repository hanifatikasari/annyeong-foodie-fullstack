<?php

namespace App\Models;

use CodeIgniter\Model;

class ProduksiModel extends Model
{
    protected $table            = 't_produksi';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'kode_produksi', 'product_id', 'user_id',
        'qty_hasil', 'tanggal_produksi', 'status_qc', 'catatan',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    /**
     * Generate kode produksi unik: PRD-YYYYMMDD-XXX
     */
    public function generateKode(string $tanggal): string
    {
        $dateStr = date('Ymd', strtotime($tanggal));
        $prefix  = "PRD-{$dateStr}-";

        $last = $this->like('kode_produksi', $prefix, 'after')
                     ->orderBy('id', 'DESC')
                     ->first();

        $nextNum = $last ? ((int) substr($last->kode_produksi, -3)) + 1 : 1;
        return $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }
}