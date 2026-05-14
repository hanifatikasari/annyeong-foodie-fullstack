<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table         = 'categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $allowedFields = ['name', 'slug', 'parent_id', 'description', 'image'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mengembalikan semua kategori dalam struktur nested (untuk admin atau keperluan lain).
     */
    public function getNestedCategories(): array
    {
        return $this->orderBy('name', 'ASC')->findAll();
    }

    /**
     * FIX ISSUE #1
     * Mengembalikan HANYA kategori anak (child) dari parent "Produk Jadi".
     * Digunakan di navbar, sidebar, dan home page frontend.
     *
     * Logika:
     * - Cari parent bernama "Produk Jadi"
     * - Ambil semua child-nya (parent_id = id Produk Jadi)
     * - Fallback: jika nama parent berbeda, cari berdasarkan slug "produk-jadi"
     */
    public function getStorefrontCategories(): array
    {
        // Cari parent "Produk Jadi" berdasarkan slug (lebih robust dari nama)
        $parent = $this->where('slug', 'produk-jadi')
                       ->first();

        // Fallback: cari berdasarkan nama jika slug tidak cocok
        if (!$parent) {
            $parent = $this->like('name', 'Produk Jadi', 'none')
                           ->first();
        }

        // Fallback kedua: ambil semua kategori yang PUNYA parent (bukan root)
        // dan parent-nya BUKAN "Bahan Baku"
        if (!$parent) {
            $bahanBaku = $this->where('slug', 'bahan-baku')
                              ->orLike('name', 'Bahan Baku', 'none')
                              ->first();

            $query = $this->where('parent_id IS NOT NULL');
            if ($bahanBaku) {
                $query->where('parent_id !=', $bahanBaku->id);
            }
            return $query->orderBy('name', 'ASC')->findAll();
        }

        // Ambil semua child dari parent "Produk Jadi"
        return $this->where('parent_id', $parent->id)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil kategori berdasarkan slug.
     */
    public function getBySlug(string $slug): ?object
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Ambil parent_id "Produk Jadi" — digunakan untuk filter produk sellable.
     * Mengembalikan ID parent "Produk Jadi", atau null jika tidak ada.
     */
    public function getProdukJadiParentId(): ?int
    {
        $parent = $this->where('slug', 'produk-jadi')->first();
        if (!$parent) {
            $parent = $this->like('name', 'Produk Jadi', 'none')->first();
        }
        return $parent ? (int) $parent->id : null;
    }
}