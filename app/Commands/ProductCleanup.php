<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ProductCleanup extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Maintenance';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'product:cleanup';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Clean up expired products (>30 days) from the trash bin';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'command:name [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $products = model('ProductModel')
            ->onlyDeleted()
            ->where(
                'deleted_at <=',
                date('Y-m-d H:i:s', strtotime('-30 days'))
            )
            ->findAll();

        foreach ($products as $product) {
            // Skip kalau ternyata sudah ter-hard delete
            if (!model('ProductModel')->withDeleted()->find($product->id)) {
                continue;
            }

            /*
            * Skip child.
            * Nanti child diproses saat parent configurable diproses.
            */
            if (!empty($product->parent_id)) {
                continue;
            }

            /*
            * CONFIGURABLE
            */
            if ($product->type === 'configurable') {

                $variants = model('ProductModel')
                    ->withDeleted()
                    ->where('parent_id', $product->id)
                    ->findAll();
                $familyHasHistory = false;
                foreach ($variants as $variant) {
                    // skip variant yang belum dihapus
                    if (!$variant->deleted_at) {
                        continue;
                    }

                    if ($this->hasTransactionHistory($variant->id)) {
                        $familyHasHistory = true;
                        break;
                    }
                }

                /*
                * Minimal ada satu child punya histori.
                */
                if ($familyHasHistory) {
                    CLI::write(
                        "Historical family: {$product->name}",
                        'yellow'
                    );

                    /*
                    * Parent hanya hapus foto.
                    */
                    $this->deleteProductImages($product->id);
                    foreach ($variants as $variant) {

                        if (!$variant->deleted_at) {
                            continue;
                        }
                        if ($this->hasTransactionHistory($variant->id)) {
                            /*
                            * if Child has histori maka hanya hapus foto
                            */
                            $this->deleteProductImages($variant->id);
                            CLI::write(
                                "Keep historical variant: {$variant->name}",
                                'yellow'
                            );
                        } else {
                            /*
                            * Child tanpa histori
                            */
                            $this->hardDeleteProduct($variant);

                        }
                    }
                    continue;
                }
                /*
                * Tidak ada histori sama sekali
                */
                foreach ($variants as $variant) {

                    if (!$variant->deleted_at) {
                        continue;
                    }
                    $this->hardDeleteProduct($variant);
                }
                $this->hardDeleteProduct($product);
                continue;
            }

            /*
            * SIMPLE PRODUCT
            */
            if ($this->hasTransactionHistory($product->id)) {
                CLI::write(
                    "Historical product: {$product->name}",
                    'yellow'
                );
                $this->deleteProductImages($product->id);
                continue;
            }
            $this->hardDeleteProduct($product);
        }
        CLI::write(
            'Cleanup completed.',
            'green'
        );
    }

    /**
     * Cek apakah produk pernah dipakai transaksi
     */
    private function hasTransactionHistory(int $productId): bool
    {
        $db = db_connect();
        $penjualan = $db->table('t_penjualan_detail')
            ->where('product_id', $productId)
            ->countAllResults();

        $produksi = $db->table('t_produksi')
            ->where('product_id', $productId)
            ->countAllResults();
        return ($penjualan > 0 || $produksi > 0);
    }

    /**
     * Hapus seluruh file gambar produk
     */
    private function deleteProductImages(int $productId)
    {
        $images = model('ProductImageModel')
            ->where('product_id', $productId)
            ->findAll();
        foreach ($images as $image) {
            $paths = [
                $image->original ?? null,
                $image->small ?? null,
                $image->medium ?? null,
                $image->large ?? null,
            ];

            foreach ($paths as $path) {
                if (empty($path)) {
                    continue;
                }
                $fullPath = FCPATH .
                    str_replace('/', DIRECTORY_SEPARATOR, $path);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            model('ProductImageModel')
                ->delete($image->id);
        }
    }

    /**
     * Hard delete produk yang tidak punya histori
     */
    private function hardDeleteProduct($product)
    {
        if ($this->hasTransactionHistory($product->id)) {
            CLI::write(
                "Skip hard delete (history): {$product->name}",
                'yellow'
            );
            return;
        }
        
        $db = db_connect();
        $db->transStart();

        // hapus foto
        $this->deleteProductImages($product->id);

        // hapus relasi inventory
        model('ProductInventoryModel')
            ->where('product_id', $product->id)
            ->delete();

        // hapus relasi attribute
        model('ProductAttributeValueModel')
            ->groupStart()
                ->where('product_id', $product->id)
                ->orWhere('parent_product_id', $product->id)
            ->groupEnd()
            ->delete();

        // hapus relasi kategori
        $db->table('product_categories')
            ->where('product_id', $product->id)
            ->delete();

        // terakhir hapus produk
        model('ProductModel')
            ->delete($product->id, true);

        $db->transComplete();

        CLI::write(
            "Deleted permanently: {$product->name}",
            'red'
        );
    }
}
