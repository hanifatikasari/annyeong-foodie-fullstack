<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<!-- Content Header (Page header) -->
<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
		  <div class="col-sm-6">
			<h1>Categories</h1>
		  </div>
		  <div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
			  <li class="breadcrumb-item"><a href="<?php echo site_url('admin/dashboard') ?>">Dashboard</a></li>
			  <li class="breadcrumb-item active">Categories</li>
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
			<div class="col-5">
				<?= $this->include('admin/categories/form') ?>
			</div>
		  	<div class="col-7">
				<div class="card">
			  		<div class="card-header">
					<h3 class="card-title">List of Categories</h3>
					<div class="card-tools d-flex">
						<form action="<?= site_url('admin/categories') ?>" method="get" class="form-inline">
							<select name="perPage" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
								<option value="5" <?= $perPage == 5 ? 'selected' : '' ?>>5</option>
								<option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
								<option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
							</select>
							
							<div class="input-group input-group-sm" style="width: 150px;">
								<input type="text" name="table_search" class="form-control float-right" placeholder="Search" value="<?= $keyword ?? '' ?>">
								<div class="input-group-append">
									<button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
								</div>
							</div>
						</form>
					</div>
			  </div>
			  <!-- /.card-header -->
			  	<div class="card-body table-responsive p-0">
					<table class="table table-hover text-nowrap">
					<thead>
						<tr>
							<th>No</th>
							<th>Prefix</th>
							<th>Name</th>
							<th style="width:15%">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$currentPage = $pager->getCurrentPage('bootstrap');
							$no = ($currentPage - 1) * $perPage + 1;
							foreach ($categories as $category):
						?>
							<tr>
								<td><?= $no++ ?></td>
								<td><span class="badge badge-secondary"><?= $category->prefix ?></span></td>
								<td><?= $category->name ?></td>
								<td>
									<a href="<?= site_url('admin/categories/edit/'. $category->id) ?>" class="badge bg-info">edit</a>
									<a href="<?= site_url('admin/categories/hapus/'. $category->id) ?>" class="badge bg-danger" onclick="return confirm('Hapus kategori ini?')">delete</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
					</table>
			  	</div>
				<!-- /.card-body -->
				<div class="card-footer clearfix">
					<div class="float-left">
						<?php 
							$currentPage = $pager->getCurrentPage('bootstrap');
							$perPage = $pager->getPerPage('bootstrap');
							$total = $pager->getTotal('bootstrap');
							
							$start = ($currentPage - 1) * $perPage + 1;
							$end = min($currentPage * $perPage, $total);
						?>
						<p class="text-sm text-muted">
							Showing <?= $total > 0 ? $start : 0 ?> to <?= $end ?> of <?= $total ?> entries
						</p>
					</div>
					<div class="float-right">
						<?= $pager->links('bootstrap', 'bootstrap') ?>
					</div>
				</div>
			</div>
			<!-- /.card -->
		  	</div>
		</div>
	</div><!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection() ?>