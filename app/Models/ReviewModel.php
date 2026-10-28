<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ReviewModel
 *
 * Mengelola tabel product_reviews.
 * Kebijakan: satu user satu review per produk (UNIQUE KEY product_id + user_id).
 * Kolom is_visible digunakan untuk moderasi admin.
 */
class ReviewModel extends Model
{
    protected $table         = 'product_reviews';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $allowedFields = [
        'product_id',
        'user_id',
        'rating',
        'ulasan',
        'is_visible',
        'penjualan_detail_id',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // ------------------------------------------------------------------
    // FRONTEND: Ambil review VISIBLE beserta info user (halaman produk)
    // ------------------------------------------------------------------
    public function getByProduct(int $productId): array
    {
        return $this->select('product_reviews.*, users.first_name, users.last_name, users.avatar')
                    ->join('users', 'users.id = product_reviews.user_id')
                    ->where('product_reviews.product_id', $productId)
                    ->where('product_reviews.is_visible', 1)
                    ->orderBy('product_reviews.id', 'DESC')
                    ->findAll();
    }

    // ------------------------------------------------------------------
    // FRONTEND: Rata-rata rating HANYA review yang visible
    // ------------------------------------------------------------------
    public function getAverageRating(int $productId): float
    {
        $result = $this->selectAvg('rating', 'avg_rating')
                       ->where('product_id', $productId)
                       ->where('is_visible', 1)
                       ->first();

        return round((float) ($result->avg_rating ?? 0), 1);
    }

    // ------------------------------------------------------------------
    // FRONTEND: Jumlah review visible
    // ------------------------------------------------------------------
    public function getVisibleCount(int $productId): int
    {
        return (int) $this->where('product_id', $productId)
                          ->where('is_visible', 1)
                          ->countAllResults();
    }

    // ------------------------------------------------------------------
    // CUSTOMER: Ambil review milik user untuk produk tertentu
    // ------------------------------------------------------------------
    public function getByProductAndUser(int $productId, int $userId): ?object
    {
        return $this->where('product_id', $productId)
                    ->where('user_id', $userId)
                    ->first();
    }

    // ------------------------------------------------------------------
    // ADMIN: Ambil semua review dengan info user dan produk untuk moderasi
    // ------------------------------------------------------------------
    public function getReviewsForAdmin(string $search = '', string $filterStatus = '', int $filterRating = 0): array
    {
        $builder = $this->select('product_reviews.*, users.first_name, users.last_name, users.email, products.name as product_name')
                        ->join('users', 'users.id = product_reviews.user_id')
                        ->join('products', 'products.id = product_reviews.product_id');

        if ($search !== '') {
            $builder->groupStart()
                    ->like('products.name', $search)
                    ->orLike('users.first_name', $search)
                    ->orLike('users.last_name', $search)
                    ->orLike('product_reviews.ulasan', $search)
                    ->groupEnd();
        }

        if ($filterStatus === 'visible') {
            $builder->where('product_reviews.is_visible', 1);
        } elseif ($filterStatus === 'hidden') {
            $builder->where('product_reviews.is_visible', 0);
        }

        if ($filterRating >= 1 && $filterRating <= 5) {
            $builder->where('product_reviews.rating', $filterRating);
        }

        return $builder->orderBy('product_reviews.id', 'DESC')
                       ->findAll();
    }
}