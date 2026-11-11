<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Tambah Promo</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/promo') ?>">Promo &amp; Diskon</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('admin/shared/flash_message', ['errors' => session('errors')]) ?>

        <div class="card">
            <div class="card-body">
                <form method="post" action="<?= site_url('admin/promo/store') ?>">
                    <?= csrf_field() ?>
                    <?= $this->include('admin/promo/form') ?>
                    <button class="btn btn-primary">Simpan</button>
                    <a href="<?= site_url('admin/promo') ?>" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>