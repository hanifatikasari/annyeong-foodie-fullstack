<?php 
    $attributeOption = $attributeOption ?? null;
    $isEdit = !empty($attributeOption);
    // Tentukan URL Action berdasarkan apakah ini mode edit atau tambah
    $isEdit = !empty($attributeOption);
    $actionUrl = $isEdit 
        ? site_url('admin/attributes/'.$attribute->id.'/options/update/'.$attributeOption->id)
        : site_url('admin/attributes/'.$attribute->id.'/options/store/');
    
?>

<div class="card <?= $isEdit ? 'card-warning' : 'card-primary' ?>">
    <div class="card-header">
       <h3 class="card-title text-white">
             <i class="fas <?= $isEdit ? 'fa-edit' : 'fa-plus' ?> mr-1"></i>
             <?= $isEdit ? 'Edit Option' : 'Add Option' ?>
        </h3>
    </div>

    <form role="form" method="post" action="<?= $actionUrl ?>">
        <?= csrf_field() ?>
        
        <div class="card-body">
            <?= view('admin/shared/flash_message') ?>
            
            <div class="form-group">
                <label for="attributeName">Option Name</label>
                <input type="text" name="name" id="attributeName" 
                       class="form-control" 
                       placeholder="Enter option name" 
                       value="<?= esc(set_value('name', $attributeOption->name ?? '')) ?>" required>
            </div>
            
            <!-- Hidden inputs -->
            <input type="hidden" name="id" value="<?= $attributeOption->id ?? '' ?>">
            <input type="hidden" name="attribute_id" value="<?= $attribute->id ?? '' ?>">
        </div>

        <div class="card-footer">
            <button type="submit" class="btn <?= $isEdit ? 'btn-warning' : 'btn-primary' ?>">
                <?= $isEdit ? 'Save Changes' : 'Submit' ?>
            </button>
            <?php if ($isEdit): ?>
                <a href="<?= site_url('admin/attributes/'. $attribute->id . '/options') ?>" class="btn btn-default">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>