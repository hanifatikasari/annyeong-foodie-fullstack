<?php

namespace App\Models;

use CodeIgniter\Model;

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
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil semua review untuk suatu produk.
     */
    public function getByProduct(int $productId): array
    {
        return $this->select('
                        product_reviews.*,
                        users.first_name,
                        users.last_name,
                        users.avatar
                    ')
                    ->join('users', 'users.id = product_reviews.user_id')
                    ->where('product_reviews.product_id', $productId)
                    ->orderBy('product_reviews.id', 'DESC')
                    ->findAll();
    }

    /**
     * Hitung rata-rata rating suatu produk.
     */
    public function getAverageRating(int $productId): float
    {
        $result = $this->selectAvg('rating', 'avg_rating')
                       ->where('product_id', $productId)
                       ->first();

        return round((float) ($result->avg_rating ?? 0), 1);
    }

    /**
     * Cek apakah user sudah pernah memberi review pada produk ini.
     */
    public function hasReviewed(int $productId, int $userId): bool
    {
        return $this->where('product_id', $productId)
                    ->where('user_id', $userId)
                    ->countAllResults() > 0;
    }

    /**
     * Ambil review milik user tertentu untuk produk tertentu.
     */
    public function getUserReview(int $productId, int $userId)
    {
        return $this->where('product_id', $productId)
                    ->where('user_id', $userId)
                    ->first();
    }
}