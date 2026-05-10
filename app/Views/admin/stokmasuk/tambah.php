<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4" id="tambahStok">
    <h1 class="mt-4">Tambah Stok Masuk</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#bagianRiwayat">Riwayat Stok Masuk</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-cart-plus mr-1"></i> Form Input Stok Belanja
        </div>
        <div class="card-body">
            <form method="post" action="<?= site_url('admin/stokmasuk/simpan') ?>">
                <?= csrf_field() ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Bahan Baku</label>
                            <select name="bahan_baku_id" class="form-control select2" required>
                                <option value="">-- Pilih Bahan --</option>
                                <?php foreach ($bahan as $b) : ?>
                                    <option value="<?= $b->id ?>"><?= $b->nama_bahan ?> (Satuan: <?= $b->satuan ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Jumlah Beli (Qty)</label>
                                <input type="number" step="0.01" name="qty" id="qty" class="form-control" value="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Satuan Beli</label>
                                <select id="pilih_satuan" class="form-control" required>
                                    <option>Gram / Pcs (Eceran)</option>
                                    <option>Kilogram (Kg)</option>
                                    <option>Liter (L)</option>
                                    <option>Pack</option>
                                    <option>Karung (25kg)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Isi per Satuan (Gram)</label>
                            <input type="number" name="isi_per_satuan" id="isi_per_satuan" class="form-control bg-light" required>
                            <small class="text-info">*Dikonversi otomatis ke satuan dasar database</small>
                        </div>
                        
                        <div class="alert alert-info py-2">
                            <label class="font-weight-bold mb-0">Total Masuk ke Stok:</label>
                            <h3 id="display_total" class="mb-0">1</h3>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Harga per Satuan Beli (Rp)</label>
                        <input type="number" name="harga_satuan" class="form-control" placeholder="Contoh: 15000" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Nama Supplier</label>
                        <input type="text" name="nama_supplier" class="form-control" required>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> Simpan & Update Stok</button>
                    <a href="<?= site_url('admin/stokmasuk') ?>" class="btn btn-secondary btn-lg">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    const inputQty = document.getElementById('qty');
    const selectSatuan = document.getElementById('pilih_satuan');
    const inputIsi = document.getElementById('isi_per_satuan');
    const displayTotal = document.getElementById('display_total');

    function hitungOtomatis() {
        let qty = parseFloat(inputQty.value) || 0;
        let isi = parseFloat(inputIsi.value) || 0;

        let total = qty * isi;
        // Tampilkan dengan format angka bagus
        displayTotal.innerText = total.toLocaleString('id-ID') + " (Sesuai Satuan Dasar)";
    }

    inputQty.addEventListener('input', hitungOtomatis);
    selectSatuan.addEventListener('change', function(){
        inputIsi.value = this.value;
        hitungOtomatis();
    });
    // 3. Saat angka "Isi per Satuan" diedit manual, hitung ulang totalnya!
    inputIsi.addEventListener('input', hitungOtomatis);
</script>

<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: "Ketik nama bahan baku...",
        allowClear: true,
        ajax: {
            url: "<?= site_url('admin/stokmasuk/getBahanbakuAjax') ?>",
            dataType: 'json',
            headers: {
                "X-CSRF-TOKEN": "<?= csrf_hash() ?>" // Tambahkan ini agar Ajax gak diblokir
            },
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