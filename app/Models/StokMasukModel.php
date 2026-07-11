<?php

namespace App\Models;

use CodeIgniter\Model;

class StokMasukModel extends Model
{
    protected $table            = 't_stok_masuk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'App\Entities\StokMasuk';
    protected $allowedFields    = [
        'bahan_baku_id', 'qty', 'isi_per_satuan', 
        'harga_satuan', 'total_harga', 'nama_supplier', 
        'tanggal_masuk', 'keterangan', 'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // Kosongkan karena migrasi tidak ada updated_at

    // Fungsi untuk join ke tabel bahan baku agar muncul namanya di list
    public function getStokMasuk()
    {
        return $this->select('t_stok_masuk.*, m_bahan_baku.nama_bahan, m_bahan_baku.satuan')
                    ->join('m_bahan_baku', 'm_bahan_baku.id = t_stok_masuk.bahan_baku_id')
                    ->orderBy('t_stok_masuk.id', 'DESC')
                    ->findAll();
    }
}