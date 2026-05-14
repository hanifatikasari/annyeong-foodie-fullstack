<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ReviewModel
 * FIX: returnType = 'object' sesuai requirement
 */
class ReviewModel extends Model
{
    protected $table         = 'product_reviews';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $allowedFields = ['product_id', 'user_id', 'rating', 'ulasan'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil review beserta info user.
     * Mengembalikan array of objects.
     */
    public function getByProduct(int $productId): array
    {
        return $this->select('product_reviews.*, users.first_name, users.last_name, users.avatar')
                    ->join('users', 'users.id = product_reviews.user_id')
                    ->where('product_reviews.product_id', $productId)
                    ->orderBy('product_reviews.id', 'DESC')
                    ->findAll();
    }

    /**
     * Hitung rata-rata rating untuk sebuah produk.
     */
    public function getAverageRating(int $productId): float
    {
        $result = $this->selectAvg('rating', 'avg_rating')
                       ->where('product_id', $productId)
                       ->first();

        return round((float) ($result->avg_rating ?? 0), 1);
    }
}