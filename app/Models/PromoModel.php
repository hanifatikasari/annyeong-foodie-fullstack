<?php

namespace App\Models;

use CodeIgniter\Model;

class PromoModel extends Model
{
    protected $table            = 'm_promo';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'nama_promo',
        'kode_promo',
        'tipe_promo',
        'jenis_diskon',
        'nilai_diskon',
        'min_subtotal',
        'max_diskon',
        'starts_at',
        'ends_at',
        'usage_limit',
        'usage_count',
        'is_active',
    ];

    public function getAutomaticPromo(int $subtotal): ?object
    {
        if ($subtotal <= 0) {
            return null;
        }

        $now = date('Y-m-d H:i:s');

        $promos = $this->where('tipe_promo', 'automatic')
            ->where('is_active', 1)
            ->groupStart()
                ->where('starts_at IS NULL', null, false)
                ->orWhere('starts_at <=', $now)
            ->groupEnd()
            ->groupStart()
                ->where('ends_at IS NULL', null, false)
                ->orWhere('ends_at >=', $now)
            ->groupEnd()
            ->groupStart()
                ->where('usage_limit IS NULL', null, false)
                ->orWhere('usage_count < usage_limit', null, false)
            ->groupEnd()
            ->where('min_subtotal <=', $subtotal)
            ->orderBy('min_subtotal', 'DESC')
            ->findAll();

        if (empty($promos)) {
            return null;
        }

        // Ambil satu promo dengan minimal belanja tertinggi
        $promo = $promos[0];

        $diskon = (int) $promo->nilai_diskon;

        if ($promo->jenis_diskon === 'percentage') {
            $diskon = (int) floor(
                $subtotal * $promo->nilai_diskon / 100
            );

            if ($promo->max_diskon !== null) {
                $diskon = min($diskon, (int) $promo->max_diskon);
            }
        }

        // Diskon tidak boleh melebihi subtotal
        $promo->diskon_aktual = min($diskon, $subtotal);

        return $promo;
    }

        /**
     * Seluruh tingkatan promo otomatis yang masih aktif, urut dari
     * minimal belanja terkecil. HANYA untuk tampilan (banner/progres).
     * Filter aktif/periode/kuota sama dengan getAutomaticPromo(),
     * tanpa filter min_subtotal.
     */
    public function getAutomaticTiers(): array
    {
        $now = date('Y-m-d H:i:s');

        $tiers = $this->where('tipe_promo', 'automatic')
            ->where('is_active', 1)
            ->groupStart()
                ->where('starts_at IS NULL', null, false)
                ->orWhere('starts_at <=', $now)
            ->groupEnd()
            ->groupStart()
                ->where('ends_at IS NULL', null, false)
                ->orWhere('ends_at >=', $now)
            ->groupEnd()
            ->groupStart()
                ->where('usage_limit IS NULL', null, false)
                ->orWhere('usage_count < usage_limit', null, false)
            ->groupEnd()
            ->orderBy('min_subtotal', 'ASC')
            ->findAll();

        foreach ($tiers as $tier) {
            $min = (int) $tier->min_subtotal;

            if ($tier->jenis_diskon === 'percentage') {
                $nominal = (int) floor($min * $tier->nilai_diskon / 100);
                $label   = (int) $tier->nilai_diskon . '%';

                if ($tier->max_diskon !== null) {
                    $nominal = min($nominal, (int) $tier->max_diskon);
                    $label  .= ' (maks Rp'
                        . number_format((int) $tier->max_diskon, 0, ',', '.') . ')';
                }
            } else {
                $nominal = (int) $tier->nilai_diskon;
                $label   = 'Rp' . number_format($nominal, 0, ',', '.');
            }

            // Nominal diskon pada batas minimal belanja (untuk tampilan).
            $tier->diskon_min   = $nominal;
            $tier->diskon_label = $label;
        }

        return $tiers;
    }

    /**
     * Tingkatan berikutnya yang belum tercapai (null jika sudah tertinggi).
     * $tiers harus berasal dari getAutomaticTiers() (urut ASC).
     */
    public function getNextTier(array $tiers, int $subtotal): ?object
    {
        foreach ($tiers as $tier) {
            if ((int) $tier->min_subtotal > $subtotal) {
                return $tier;
            }
        }

        return null;
    }

    // ---------------------------------------------------------------
    // ADMIN: daftar promo otomatis + jumlah pemakaian di t_penjualan
    // ---------------------------------------------------------------
    public function adminList(string $keyword = ''): self
    {
        $this->select(
            'm_promo.*, (SELECT COUNT(*) FROM t_penjualan tp WHERE tp.promo_id = m_promo.id) AS dipakai',
            false
        )->where('tipe_promo', 'automatic');

        if ($keyword !== '') {
            $this->like('nama_promo', $keyword);
        }

        return $this->orderBy('min_subtotal', 'ASC');
    }

    /**
     * Status promo untuk tampilan admin. Aturannya sama dengan filter
     * di getAutomaticPromo()/getAutomaticTiers().
     */
    public function getStatus(object $promo): string
    {
        if (! (int) $promo->is_active) {
            return 'nonaktif';
        }

        $now = date('Y-m-d H:i:s');

        if ($promo->starts_at !== null && $promo->starts_at > $now) {
            return 'dijadwalkan';
        }

        if ($promo->ends_at !== null && $promo->ends_at < $now) {
            return 'berakhir';
        }

        if ($promo->usage_limit !== null
            && (int) $promo->usage_count >= (int) $promo->usage_limit) {
            return 'kuota_habis';
        }

        return 'berjalan';
    }

    /**
     * Sudah ada promo otomatis AKTIF lain dengan minimal belanja yang sama?
     */
    public function hasActiveTier(int $minSubtotal, ?int $exceptId = null): bool
    {
        $this->where('tipe_promo', 'automatic')
            ->where('is_active', 1)
            ->where('min_subtotal', $minSubtotal);

        if ($exceptId !== null) {
            $this->where('id !=', $exceptId);
        }

        return $this->countAllResults() > 0;
    }

    /**
     * Jumlah transaksi yang memakai promo ini.
     */
    public function countUsage(int $id): int
    {
        return (int) $this->db->table('t_penjualan')
            ->where('promo_id', $id)
            ->countAllResults();
    }

    /**
     * Catat satu pemakaian promo secara atomik.
     * Mengembalikan false jika promo tidak ditemukan
     * atau kuotanya sudah habis.
     */
    public function recordUsage(int $promoId): bool
    {
        $builder = $this->builder()
            ->where('id', $promoId)
            ->groupStart()
                ->where('usage_limit IS NULL', null, false)
                ->orWhere('usage_count < usage_limit', null, false)
            ->groupEnd();

        $builder->set('usage_count', 'usage_count + 1', false)
            ->update();

        return $this->db->affectedRows() === 1;
    }
}
