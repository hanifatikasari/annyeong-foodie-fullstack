<?php 
    $isEdit = isset($attribute) && !empty($attribute->id);
    $cardClass = $isEdit ? 'card-warning' : 'card-primary';
    $btnClass = $isEdit ? 'btn-warning' : 'btn-primary';
    $title = $isEdit ? '<i class="fas fa-edit"></i> Update Attribute' : '<i class="fas fa-plus"></i> New Attribute';
    $actionUrl = $isEdit ? site_url('admin/attributes/update/' . $attribute->id) : site_url('admin/attributes/store');
?>

<div class="card <?= $cardClass ?>">
    <div class="card-header">
        <h3 class="card-title"><?= $title ?></h3>
    </div>
    
    <form role="form" method="post" action="<?= $actionUrl ?>">
        <?= csrf_field() ?>
        <div class="card-body" style="<?= $isEdit ? 'background-color: #fffdf5;' : '' ?>">
            <?= view('admin/shared/flash_message') ?>
            
            <?php if($isEdit): ?>
                <div class="alert alert-info btn-sm">
                    <i class="fas fa-info-circle"></i> Anda sedang mengubah <strong>Atribut: <?= $attribute->name ?></strong>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" id="attributeName" class="form-control" 
                       value="<?= old('name', $isEdit ? $attribute->name : '') ?>" required>
            </div>

            <div class="form-group">
                <label>Code</label>
                <input type="text" name="code" id="attributeCode" class="form-control" 
                       value="<?= old('code', $isEdit ? $attribute->code : '') ?>" readonly>
                <small class="text-muted">Otomatis dari nama.</small>
            </div>

            <div class="form-group">
                <label>Type</label>
                <?= form_dropdown('type', $attributeTypes, old('type', $attribute->type ?? ''), ['class' => 'form-control']) ?>
            </div>

            <div class="form-group">
                <label>Validation</label>
                <?= form_dropdown('validation', $validations, old('validation', $attribute->validation ?? ''), ['class' => 'form-control']) ?>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Required?</label>
                        <?= form_dropdown('is_required', $isRequiredOptions, old('is_required', $attribute->is_required ?? ''), ['class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Unique?</label>
                        <?= form_dropdown('is_unique', $isUniqueOptions, old('is_unique', $attribute->is_unique ?? ''), ['class' => 'form-control']) ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Configurable Product?</label>
                <?= form_dropdown('is_configurable', $isConfigurableOptions, old('is_configurable', $attribute->is_configurable ?? ''), ['class' => 'form-control']) ?>
            </div>

            <div class="form-group">
                <label>Filtering Product?</label>
                <?= form_dropdown('is_filterable', $isFilterableOptions, old('is_filterable', $attribute->is_filterable ?? ''), ['class' => 'form-control']) ?>
            </div>
        </div>

        <div class="form-group">
            <label>Categories</label>
            <select name="category_ids[]" id="attributeCategories" class="form-control select2" multiple>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat->id ?>"
                        <?= in_array($cat->id, $selectedCategories ?? []) ? 'selected' : '' ?>>
                        <?= $cat->name ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="text-muted">Pilih kategori yang menggunakan attribute ini</small>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn <?= $btnClass ?>"><?= $isEdit ? 'Save Changes' : 'Add Attribute' ?></button>
            <?php if ($isEdit): ?>
                <a href="<?= site_url('admin/attributes') ?>" class="btn btn-default float-right">Cancel</a>
            <?php endif;?>
        </div>
    </form>
</div>

<?php $this->section('script') ?>
<script>
    $('#attributeCategories').select2({
    theme: 'bootstrap4',
    placeholder: "Cari kategori...",
    ajax: {
        url: "<?= site_url('admin/attributes/getCategoriesAjax') ?>",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return { q: params.term };
        },
        processResults: function (data) {
            return { results: data };
        }
    }
});
</script>
<?php $this->endSection() ?>