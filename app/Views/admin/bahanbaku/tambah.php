<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Bahan Baku</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('admin/bahanbaku') ?>">Bahan Baku</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-plus mr-1"></i> Form Tambah Bahan Baku
        </div>
        <div class="card-body">
            <form method="post" action="<?= site_url('admin/bahanbaku/simpan') ?>">
                <?= csrf_field() ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Kode Bahan</label>
                            <input type="text" name="kode_bahan" class="form-control" value="Otomatis" readonly>
                            <small class="text-muted">Kode akan dibuat otomatis oleh sistem.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Nama Bahan</label>
                            <input type="text" name="nama_bahan" class="form-control" placeholder="Nama bahan mentah" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Kategori</label>
                            <select name="category_id" class="form-control select2" required>
                                <option value="">-- Cari Kategori --</option>
                                <?php foreach ($categories as $cat) : ?>
                                    <option value="<?= $cat->id ?>"><?= $cat->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Satuan</label>
                            <input type="text" name="satuan" class="form-control" placeholder="contoh: gr, pcs, ml" required maxlength="4">
                            <small class="text-info">*Gunakan satuan terkecil (Contoh: 'gr' untuk Tepung, 'pcs' untuk Telur).</small>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Harga Beli Satuan (Rp)</label>
                            <input type="number" name="harga_beli_satuan" class="form-control <?= (session('errors.harga_beli_satuan')) ? 'is-invalid' : '' ?>" value="<?= old('harga_beli_satuan') ?>" placeholder="Contoh: 15000" required>
                            <small class="text-info">*Harga beli per satuan (Contoh: Rp12000/1000gr = 12).</small>

                            <?php if (session('errors.harga_beli_satuan')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.harga_beli_satuan') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Stok Sekarang</label>
                                    <input type="number" step="0.01" name="stok_sekarang" class="form-control <?= (session('errors.stok_sekarang')) ? 'is-invalid' : '' ?>" value="<?= old('stok_sekarang') ?>" required>
                                    <small class="text-muted">Isi angka sesuai satuan di atas.</small>
                                    <?php if (session('errors.stok_sekarang')) : ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.stok_sekarang') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Stok Minimal</label>
                                    <input type="number" step="0.01" name="stok_minimal" class="form-control <?= (session('errors.stok_minimal')) ? 'is-invalid' : '' ?>" value="<?= old('stok_minimal') ?>" required>
                                    <small class="text-muted">Isi angka sesuai satuan di atas.</small>
                                    <?php if (session('errors.stok_minimal')) : ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.stok_minimal') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Bahan</button>
                <a href="<?= site_url('admin/bahanbaku') ?>" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    const inputHarga = document.getElementById('harga_beli_satuan');
    
    // Buat elemen baru untuk menampilkan preview rupiah di bawah input
    const previewHarga = document.createElement('small');
    previewHarga.className = 'text-muted d-block mt-1';
    previewHarga.innerText = 'Format: Rp 0';
    inputHarga.parentNode.appendChild(previewHarga);

    inputHarga.addEventListener('input', function(e) {
        let value = this.value;
        if (value) {
            // Format angka ke Rupiah untuk preview saja
            let formatted = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
            
            previewHarga.innerText = 'Format: ' + formatted;
        } else {
            previewHarga.innerText = 'Format: Rp 0';
        }
    });
</script>

<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: "Ketik nama kategori...",
        allowClear: true,
        ajax: {
            url: "<?= site_url('admin/bahanbaku/getCategoriesAjax') ?>",
            dataType: 'json',
            delay: 250, // Menunggu user berhenti ngetik (biar nggak boros request)
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
<?= $this->endSection() ?>