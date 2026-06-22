<nav class="nav flex-column nav-pills">

    <a class="nav-link <?= ($productMenu == 'product_details') ? 'active' : '' ?>"
       href="<?= site_url('admin/products/edit/' . $product->id) ?>">
        Product Details
    </a>

    <a class="nav-link <?= ($productMenu == 'product_images') ? 'active' : '' ?>"
       href="<?= site_url('admin/products/' . $product->id . '/images') ?>">
        Product Images
    </a>

    <?php if ($product->type == model('ProductModel')::CONFIGURABLE): ?>
        <a class="nav-link <?= ($productMenu == 'product_variants') ? 'active' : '' ?>"
           href="<?= site_url('admin/products/' . $product->id . '/variants') ?>">
            Product Variants
        </a>
    <?php endif; ?>

</nav>