<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Catat Produksi Baru</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/produksi') ?>">Produksi</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('admin/shared/flash_message') ?>
        <div class="row">
            <div class="col-md-5">
                <div class="card card-primary">
                    <div class="card-header"><h3 class="card-title">Form Produksi</h3></div>
                    <form method="post" action="<?= site_url('admin/produksi/simpan') ?>">
                        <?= csrf_field() ?>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Produk yang Diproduksi <span class="text-danger">*</span></label>
                                <select name="product_id" id="productSelect" class="form-control" required>
                                    <option value="">-- Pilih Produk --</option>
                                    <?php foreach ($products as $p): ?>
                                        <option value="<?= $p->id ?>"
                                                data-hpp="<?= $p->hpp_total ?>">
                                            [<?= esc($p->sku) ?>] <?= esc($p->name) ?>
                                            (HPP: Rp <?= number_format($p->hpp_total) ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <small class="text-muted">Hanya produk yang sudah memiliki resep yang ditampilkan.</small>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Jumlah Produksi (Porsi) <span class="text-danger">*</span></label>
                                <input type="number" name="qty_hasil" id="qtyInput"
                                       class="form-control" min="1" required>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Tanggal Produksi <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_produksi" class="form-control"
                                       value="<?= date('Y-m-d') ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Status Quality Control <span class="text-danger">*</span></label>
                                <select name="status_qc" class="form-control" required>
                                    <option value="Lolos">✅ Lolos QC (Stok bertambah)</option>
                                    <option value="Reject">❌ Reject (Stok tidak bertambah)</option>
                                </select>
                                <small class="text-info">
                                    Jika Reject, bahan baku tetap dikonsumsi namun stok produk tidak bertambah.
                                </small>
                            </div>

                            <div class="form-group">
                                <label>Catatan</label>
                                <textarea name="catatan" class="form-control" rows="2"
                                          placeholder="Catatan tambahan (opsional)"></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" id="btnSimulasi" class="btn btn-info btn-block mb-2">
                                <i class="fas fa-calculator"></i> Cek Ketersediaan Bahan
                            </button>
                            <button type="submit" id="btnSimpan" class="btn btn-success btn-block" disabled>
                                <i class="fas fa-check"></i> Konfirmasi & Proses Produksi
                            </button>
                            <a href="<?= site_url('admin/produksi') ?>" class="btn btn-secondary btn-block mt-1">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Panel simulasi -->
            <div class="col-md-7">
                <div class="card" id="simulasiPanel" style="display:none">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-flask"></i> Hasil Simulasi Produksi</h3>
                    </div>
                    <div class="card-body" id="simulasiContent">
                        <!-- Diisi via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
document.getElementById('btnSimulasi').addEventListener('click', function () {
    const productId = document.getElementById('productSelect').value;
    const qty       = document.getElementById('qtyInput').value;

    if (!productId || !qty || qty <= 0) {
        alert('Pilih produk dan isi jumlah produksi terlebih dahulu!');
        return;
    }

    fetch('<?= site_url('admin/produksi/simulasi') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            product_id: productId,
            qty_hasil: qty,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        })
    })
    .then(r => r.json())
    .then(data => {
        let html = `<p>Kebutuhan bahan untuk <strong>${data.qty} porsi</strong>:</p>`;
        html += '<table class="table table-sm table-bordered">';
        html += '<thead><tr><th>Bahan</th><th>Stok</th><th>Dibutuhkan</th><th>Status</th></tr></thead><tbody>';

        let totalBiaya = 0;
        data.bahan.forEach(b => {
            const badge = b.kurang
                ? `<span class="badge badge-danger">KURANG ${(b.dibutuhkan - b.stok_sekarang).toFixed(3)} ${b.satuan}</span>`
                : `<span class="badge badge-success">Cukup</span>`;
            html += `<tr class="${b.kurang ? 'table-danger' : ''}">
                <td>${b.nama_bahan}</td>
                <td>${b.stok_sekarang} ${b.satuan}</td>
                <td><strong>${b.dibutuhkan} ${b.satuan}</strong></td>
                <td>${badge}</td>
            </tr>`;
            totalBiaya += b.biaya;
        });

        html += `</tbody><tfoot><tr>
            <th colspan="3" class="text-right">Estimasi Biaya Produksi</th>
            <th>Rp ${totalBiaya.toLocaleString('id-ID')}</th>
        </tr></tfoot></table>`;

        if (!data.cukup) {
            html += '<div class="alert alert-danger"><i class="fas fa-times-circle"></i> Stok bahan tidak mencukupi! Tambah stok terlebih dahulu.</div>';
            document.getElementById('btnSimpan').disabled = true;
        } else {
            html += '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Semua bahan tersedia. Klik tombol "Konfirmasi & Proses Produksi".</div>';
            document.getElementById('btnSimpan').disabled = false;
        }

        document.getElementById('simulasiContent').innerHTML = html;
        document.getElementById('simulasiPanel').style.display = 'block';
    });
});
</script>
<?= $this->endSection() ?>