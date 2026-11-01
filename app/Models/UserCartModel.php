<?php

namespace App\Models;

use CodeIgniter\Model;

class UserCartModel extends Model
{
    protected $table            = 'user_cart_items';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';

    protected $allowedFields = [
        'user_id',
        'product_id',
        'qty',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getUserCart(int $userId): array
    {
        return $this->where('user_id', $userId)->findAll();
    }

    public function getUserCartItem(int $userId, int $productId): ?array
    {
        return $this->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
    }

    public function saveCartItem(int $userId, int $productId, int $qty): bool
    {
        $item = $this->getUserCartItem($userId, $productId);

        if ($item) {
            return $this->update($item['id'], ['qty' => $qty]);
        }

        return (bool) $this->insert([
            'user_id'    => $userId,
            'product_id' => $productId,
            'qty'        => $qty,
        ]);
    }

    public function removeCartItem(int $userId, int $productId): bool
    {
        return $this->where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();
    }

    public function clearUserCart(int $userId): bool
    {
        return $this->where('user_id', $userId)->delete();
    }
}