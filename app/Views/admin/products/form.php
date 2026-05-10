<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<!-- Content Header (Page header) -->
<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
		  <div class="col-sm-6">
			<h1>New Product</h1>
		  </div>
		  <div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item">
					<a href="<?= site_url('admin/dashboard') ?>">Dashboard</a>
				</li>
				<li class="breadcrumb-item">
					<a href="<?= site_url('admin/products') ?>">Products</a>
				</li>

				<?php if (!empty($product)) : ?>
					<li class="breadcrumb-item active">Edit Product</li>
				<?php else : ?>
					<li class="breadcrumb-item active">New Product</li>
				<?php endif; ?>
			</ol>
		  </div>
		</div>
	  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<!-- /.row -->
		<div class="row">
			<?php if (!empty($product)) : ?>
				<div class="col-3">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">Menu</h3>
						</div>
						<div class="card-body">
							<?= $this->include('admin/products/menus'); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		  	<div class="col-9">
			  <div class="card">
				<div class="card-header">
					<h3 class="card-title"><?= isset($product) ? 'Update' : 'New' ?> Product</h3>
				</div>
				<!-- /.card-header -->
				<!-- form start -->
				<?php if (!empty($product)): ?>
					<form role="form" method="post" action="<?= site_url('admin/products/'. $product->id) ?>" novalidate>
					<input name="_method" type="hidden" value="PUT">
				<?php else: ?>
					<form role="form" method="post" action="<?= site_url('admin/products') ?>" novalidate>
				<?php endif; ?>
					<input type="hidden" name="id" value="<?= isset($product->id) ? $product->id : null ?>"/>
					<div class="card-body">
						<?= view('admin/shared/flash_message') ?>
						<div class="form-group">
							<label for="productType">Type</label>
							<?php
								$typeInputProperties = ['class' => 'form-control product-type', 'id' => 'productType', 'required' => true];

								if (!empty($product)) {
									$typeInputProperties = array_merge($typeInputProperties, [
										'readonly' => true,
									]);
								}
							?>
							<?= form_dropdown('type', $types, set_value('type', isset($product->type) ? ($product->type) : '' ), $typeInputProperties) ?>
						</div>
						<div class="form-group">
							<label for="productSku">SKU</label>
							<?= form_input('sku', set_value('sku', isset($product->sku) ? ($product->sku) : '' ), [
								'class' => 'form-control', 
								'id' => 'productSku', 
								'placeholder' => 'Otomatis', 
								'readonly' => true 
							]) ?>
							 <small class="text-muted">SKU akan dibuat otomatis oleh sistem.</small>
						</div>
						<div class="form-group">
							<label for="productName">Name</label>
							<?= form_input('name', set_value('name', isset($product->name) ? ($product->name) : '' ), ['class' => 'form-control', 'id' => 'productName', 'placeholder' => 'Enter product name', 'required' => true]) ?>
						</div>

						<div class="form-group">
							<label for="productCategories">Categories</label>
							<select name="categories[]" id="productCategories" class="form-control select2" multiple="multiple" required>
								<?php if (!empty($product) && isset($product->categories)): ?>
									<?php foreach ($product->categories as $category): ?>
										<option value="<?= $category->id ?>" selected><?= $category->name ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</div>

						<div class="simple-attributes">
							<?= $this->include('admin/products/simple_product_fields') ?>
						</div>
						<?php if (empty($product)):?>
							<div class="configurable-attributes">
								<?= $this->include('admin/products/configurable_attributes') ?>
							</div>
							<div id="variant-combinations"></div>
						<?php endif; ?>

						<?php if (!empty($product) && $product->type == 'configurable'):?>
							<?= $this->include('admin/products/configurable_fields'); ?>
						<?php endif;?>
						<div class="form-group">
							<label for="productShortDescription">Short Description</label>
							<?= form_textarea('short_description', set_value('short_description', isset($product->short_description) ? ($product->short_description) : '' ), 
								[
									'class' => 'form-control', 
									'id' => 'productShortDescription',
									'rows' => '3', // <-- Tambahkan ini supaya ramping
									'maxlength' => '100', 
									'placeholder' => 'max 100 characters'

								]) 
							?>
							<small id="charCount" class="text-muted">0 / 100 karakter</small>
						</div>

						<div class="form-group">
							<label for="productDescription">Description</label>
							<?= form_textarea('description', set_value('description', isset($product->description) ? ($product->description) : '' ), 
								[
									'class' => 'form-control', 
									'id' => 'productDescription',
									'rows' => '5' // <-- agak tinggi sdkit
								]) 
							?>
						</div>
						<div class="form-group">
							<label for="productStatus">Status</label>
							<?= form_dropdown('status', $statuses, set_value('status', isset($product->status) ? ($product->status) : '' ), ['class' => 'form-control', 'id' => 'productStatus']) ?>
						</div>
					</div>
					<!-- /.card-body -->

					<div class="card-footer">
						<button type="submit" class="btn btn-primary">Submit</button>
						
						<?php if (!empty($product)): ?>
							<a href="<?= site_url('admin/products') ?>" class="btn btn-default">Cancel</a>
						<?php endif;?>
					</div>
				</form>
			</div>
			<!-- /.card -->
			</div>
			<!-- /.card -->
		  	</div>
		</div>
	</div><!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection() ?>

<?=$this->section('script')?>
	<script>
	$(document).ready(function() {
		$('#productCategories').select2({
			theme: 'bootstrap4',
			placeholder: "Ketik nama kategori...",
			allowClear: true,
			ajax: {
				url: "<?= site_url('admin/products/getCategoriesAjax') ?>",
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term // Kata kunci yang diketik
					};
				},
				processResults: function (data) {
					return {
						results: data
					};
				},
				cache: true
			}
		});
	});
	</script>

	<script>
		$('#productCategories').on('change', function () {

			let categoryIds = $(this).val();

			$.ajax({
				url: "<?= site_url('admin/products/getAttributesByCategory') ?>",
				type: 'POST',
				data: {
					category_ids: categoryIds
				},

				success: function(attributes) {

					let html = '';

					attributes.forEach(attr => {

						html += `
							<div class="form-group">
								<label>${attr.name}</label>

								<select
									name="configurable[${attr.code}][]"
									multiple
									class="form-control configurable-select"
								>
						`;

						attr.options.forEach(opt => {

							html += `
								<option value="${opt.id}">
									${opt.name}
								</option>
							`;
						});

						html += `
								</select>
							</div>
						`;
					});

					$('#attribute-container').html(html); //render
					
					$('.configurable-select').select2({
						theme: 'bootstrap4',
						placeholder: 'Pilih opsi...',
						closeOnSelect: false
					});

					// regenerate combinations
					generateVariantCombinations();
				}
			});
		});
	</script>

	<script>
	$(document).ready(function() {
		// Fungsi Counter Karakter untuk Short Description
		$('#productShortDescription').on('input', function() {
			var length = $(this).val().length;
			$('#charCount').text(length + ' / 100 karakter');
			
			// Opsional: Kasih warna merah kalau udah limit 
			if (length >= 100) {
				$('#charCount').removeClass('text-muted').addClass('text-danger font-weight-bold');
			} else {
				$('#charCount').removeClass('text-danger font-weight-bold').addClass('text-muted');
			}
		});
	});
	</script>

	<script>
		$(document).ready(function() {
			function showHideProductAttributes() {
				var productType = $('.product-type').val();
				
				if (productType == 'configurable') {
					$('.simple-attributes').hide();
					$('.configurable-attributes').show();
				} else {
					$('.simple-attributes').show();
					$('.configurable-attributes').hide();
				}
			}

			// Jalankan saat halaman pertama kali dibuka
			showHideProductAttributes();

			// Jalankan saat dropdown tipe produk berubah
			$('.product-type').change(function() {
				showHideProductAttributes();
			});
		});
</script>


<script>
	function generateVariantCombinations() {

		let attributes = [];

		$('#attribute-container select').each(function() {

			let attrName = $(this).attr('name');

			let values = [];

			$(this).find('option:selected').each(function () {
				values.push({
					id: $(this).val(),
					text: $(this).text().trim()
				});
			});

			if (values.length > 0) {
				attributes.push({
					name: attrName,
					values: values
				});
			}
		});

		if (attributes.length === 0) {
			$('#variant-combinations').html('');
			return;
		}

		let combinations = [[]];

		attributes.forEach(attr => {

			let temp = [];

			combinations.forEach(combo => {

				attr.values.forEach(val => {

					temp.push([
						...combo,
						val
					]);

				});

			});

			combinations = temp;
		});

		let html = '<hr><h5>Valid Variant Combinations</h5>';

		combinations.forEach((combo, index) => {

			let label = combo.map(c => c.text).join(' + ');

			html += `
				<div class="form-check mb-2">
					<input 
						type="checkbox"
						class="form-check-input"
						name="valid_variants[]"
						value="${index}"
						checked
					>

					<label class="form-check-label">
						${label}
					</label>
				</div>
			`;
		});

		$('#variant-combinations').html(html);
	}

	$(document).on('change', '#attribute-container select', function () {
		generateVariantCombinations();
	});
</script>
<?=$this->endSection()?>
