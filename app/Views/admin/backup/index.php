<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Backup and Restore</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Backup and Restore</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?php if (! empty($flash)): ?>
            <div class="alert alert-success"><?= esc($flash) ?></div>
        <?php endif ?>
        <?php if (! empty($error)): ?>
            <div class="alert alert-danger"><?= esc($error) ?></div>
        <?php endif ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Backup Database</h3></div>
                    <div class="card-body">
                        <p>Klik tombol di bawah untuk membuat backup SQL instan dan mengunduh file.</p>
                        <a href="<?= site_url('admin/backup/download') ?>" class="btn btn-primary">
                            <i class="fas fa-download"></i> Download Backup SQL
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Restore Database</h3></div>
                    <div class="card-body">
                        <form action="<?= site_url('admin/backup/restore') ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="sql_file">Upload file .sql</label>
                                <input type="file" name="sql_file" id="sql_file" class="form-control-file" accept=".sql" required>
                            </div>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-upload"></i> Restore Database
                            </button>
                        </form>
                        <p class="mt-2 text-muted">Pastikan file backup berasal dari database yang sama.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
