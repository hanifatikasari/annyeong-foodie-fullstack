<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1>Attributes</h1></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
              <li class="breadcrumb-item active">Attributes</li>
            </ol>
          </div>
        </div>
      </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5">
                <?= $this->include('admin/attributes/form') ?>
            </div>
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">List of Attributes</h3>
                        <div class="card-tools d-flex">
                            <form action="<?= site_url('admin/attributes') ?>" method="get" class="form-inline">
                                <select name="perPage" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                    <option value="5" <?= $perPage == 5 ? 'selected' : '' ?>>5</option>
                                    <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
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
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $currentPage = $pager->getCurrentPage('bootstrap');
                                    $no = ($currentPage - 1) * $perPage + 1;
                                    foreach ($attributes as $row):
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row->name ?></td>
                                    <td><span class="badge badge-info"><?= $row->type ?></span></td>
                                    <td>
                                        <a href="<?= site_url('admin/attributes/edit/'. $row->id) ?>" class="badge bg-info">edit</a>
                                        <?php if ($row->type == 'select'): ?>
                                            <a href="<?= site_url('admin/attributes/'. $row->id .'/options') ?>" class="badge bg-success">options</a>
                                        <?php endif ?>
                                        <a href="<?= site_url('admin/attributes/delete/'. $row->id) ?>" class="badge bg-danger" onclick="return confirm('Hapus atribut ini?')">delete</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix">
                        <div class="float-left">
                            <?php 
                                $total = $pager->getTotal('bootstrap');
                                $start = ($currentPage - 1) * $perPage + 1;
                                $end = min($currentPage * $perPage, $total);
                            ?>
                            <p class="text-sm text-muted">Showing <?= $total > 0 ? $start : 0 ?> to <?= $end ?> of <?= $total ?> entries</p>
                        </div>
                        <div class="float-right">
                            <?= $pager->links('bootstrap', 'bootstrap') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('attributeName');
        const codeInput = document.getElementById('attributeCode');
        if(nameInput) {
            nameInput.addEventListener('keyup', function() {
                codeInput.value = this.value.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
            });
        }
    });
</script>
<?= $this->endSection() ?>