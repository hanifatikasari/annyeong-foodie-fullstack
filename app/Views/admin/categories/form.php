<?php 
    // Tentukan warna tema berdasarkan mode
    $isEdit = isset($category);
    $cardClass = $isEdit ? 'card-warning' : 'card-primary';
    $btnClass = $isEdit ? 'btn-warning' : 'btn-primary';
    $title = $isEdit ? '<i class="fas fa-edit"></i> Update Category' : '<i class="fas fa-plus"></i> New Category';
?>

<div class="card <?= $cardClass ?>">
    <div class="card-header">
        <h3 class="card-title"><?= $title ?></h3>
    </div>
    
    <form role="form" method="post" action="<?= site_url('admin/categories/simpan') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $isEdit ? $category->id : '' ?>"/>

        <div class="card-body" style="<?= $isEdit ? 'background-color: #fffdf5;' : '' ?>">
            <?= view('admin/shared/flash_message') ?>
            
            <?php if($isEdit): ?>
                <div class="alert alert-info btn-sm">
                    <i class="fas fa-info-circle"></i> Anda sedang mengubah data <strong>Kategori: <?= $category->name ?></strong>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="categoryName">Name</label>
                <input type="text" name="name" class="form-control <?= (session('errors.name')) ? 'is-invalid' : '' ?>" 
                       value="<?= old('name', $isEdit ? $category->name : '') ?>" 
                       id="categoryName" placeholder="Enter category name" minlength="3" required>

                <?php if (session('errors.name')): ?>
                    <div class="invalid-feedback">
                        <?= session('errors.name') ?>
                    </div>
                <?php endif; ?>

            </div>

            <div class="form-group">
                <label for="categoryPrefix">Prefix (SKU)</label>
                <input type="text" name="prefix" class="form-control <?= (session('errors.prefix')) ? 'is-invalid' : '' ?>" 
                    value="<?= old('prefix', $isEdit ? $category->prefix : '') ?>" 
                    id="categoryPrefix" placeholder="Contoh: MCH, KMB, TPG" 
                    maxlength="3" required>

                <?php if (session('errors.prefix')): ?>
                    <div class="invalid-feedback">
                        <?= session('errors.prefix') ?>
                    </div>
                <?php endif; ?>
                <small class="text-muted">Max 3 karakter. Digunakan untuk generate SKU produk.</small>
            </div>
            
            <div class="form-group">
                <label for="parentCategory">Parent</label>
                <select name="parent_id" class="form-control">
                    <option value="0">-- No Parent (Top Level) --</option>
                    <?php foreach ($parentOptions as $p) : ?>
                        <?php if ($isEdit && $p->id == $category->id) continue; ?>
                        <option value="<?= $p->id ?>" <?= ($isEdit && $category->parent_id == $p->id) ? 'selected' : '' ?>>
                            <?= $p->name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn <?= $btnClass ?>"><?= $isEdit ? 'Save Changes' : 'Add Category' ?></button>
            <?php if ($isEdit): ?>
                <a href="<?= site_url('admin/categories') ?>" class="btn btn-default float-right">Cancel</a>
            <?php endif;?>
        </div>
    </form>
</div>