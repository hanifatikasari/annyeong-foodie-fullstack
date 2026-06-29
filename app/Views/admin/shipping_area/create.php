<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">Tambah Area Pengiriman</div>
    <div class="card-body">
        <form method="post" action="<?= site_url('admin/shipping_area/store') ?>">
            <?= csrf_field() ?>
            <?= $this->include('admin/shipping_area/form') ?>
            <button class="btn btn-primary">Simpan</button>
            <a href="<?= site_url('admin/shipping_area') ?>" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>