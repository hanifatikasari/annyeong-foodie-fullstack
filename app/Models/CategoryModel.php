<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model {
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'App\Entities\Category';
    protected $useSoftDeletes   = true; // Aktifkan Soft Delete
    protected $allowedFields    = ['name', 'slug', 'parent_id', 'prefix'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime'; 
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getNestedCategories($parentId = null)
    {
        $categories = $this->where('parent_id', $parentId)
                        ->orderBy('name', 'ASC')
                        ->findAll();

        foreach ($categories as &$category) {
            $category->children = $this->getNestedCategories($category->id);
        }

        return $categories;
    }
}