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
}
