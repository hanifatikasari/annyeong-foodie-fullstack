<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>Tambah Varian untuk <?= esc($parentProduct->name) ?></h3>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= site_url('admin/dashboard') ?>">Dashboard</a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="<?= site_url('admin/products') ?>">Products</a>
                        </li>

                        <li class="breadcrumb-item active">
                            Tambah Varian
                        </li>
                    </ol>
                </div>
                <form action="<?= site_url('admin/products/'.$parentProduct->id.'/variants') ?>"
                    method="post">

                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label>Nama Varian</label>
                        <input type="text"
                            name="name"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number"
                            name="price"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number"
                            name="stock"
                            class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>