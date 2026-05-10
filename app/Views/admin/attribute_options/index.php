<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Attribute Options for <?= esc($attribute->name) ?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?php echo site_url('admin/dashboard') ?>">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="<?php echo site_url('admin/attributes') ?>">Attributes</a></li>
              <li class="breadcrumb-item active">Options</li>
            </ol>
          </div>
        </div>
      </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5">
                <?= $this->include('admin/attribute_options/form', [
					'attribute' => $attribute, 
					'attributeOption' => $attributeOption
				]) ?>
            </div>
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">List of Options</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th style="width: 10px">No</th> 
                                    <th>Name</th>
                                    <th style="width: 100px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1 + (10 * ($pager->getCurrentPage('bootstrap') - 1)); ?>
                                <?php foreach ($attributeOptions as $opt): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= esc($opt->name) ?></td>
                                        <td>
                                            <!-- Link Edit -->
											<a href="<?= site_url('admin/attributes/'. $attribute->id .'/options/edit/'. $opt->id) ?>" 
											class="btn btn-info btn-xs">edit</a>
                                            
                                            <!-- Link Delete -->
                                            <a href="<?= site_url('admin/attributes/'. $attribute->id .'/options/delete/'. $opt->id) ?>" 
                                               class="btn btn-danger btn-xs" 
                                               onclick="return confirm('Yakin hapus opsi ini?')">delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix">
                        <?= $pager->links('bootstrap', 'bootstrap') ?> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>