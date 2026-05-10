<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Resep: <?= esc($product->name) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/recipes') ?>">Recipes</a></li>
                    <li class="breadcrumb-item active"><?= esc($product->name) ?></li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('admin/shared/flash_message') ?>

        <div class="row">
            <!-- FORM TAMBAH BAHAN -->
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-plus"></i> Tambah Bahan ke Resep</h3>
                    </div>
                    <form method="post" action="<?= site_url('admin/recipes/simpan/' . $product->id) ?>">
                        <?= csrf_field() ?>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Bahan Baku <span class="text-danger">*</span></label>
                                <select name="bahan_baku_id" id="bahanSelect" class="form-control" required>
                                    <option value="">-- Pilih Bahan --</option>
                                    <?php foreach ($bahanList as $b): ?>
                                        <option value="<?= $b->id ?>"
                                                data-satuan="<?= $b->satuan ?>"
                                                data-harga="<?= $b->harga_beli_satuan ?>">
                                            <?= esc($b->nama_bahan) ?> (<?= $b->satuan ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Jumlah Kebutuhan per Porsi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.001" name="jumlah_kebutuhan"
                                           id="jumlahInput" class="form-control" placeholder="0.000" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="satuanLabel">-</span>
                                    </div>
                                </div>
                                <small class="text-muted">Masukkan sesuai satuan bahan. Contoh: 250 jika satuan gram, 100 jika satuan ml, atau 2 jika satuan pcs.</small>
                            </div>

                            <div class="form-group">
                                <label>Estimasi Biaya</label>
                                <div class="alert alert-info py-1 mb-0">
                                    <strong>Rp <span id="estimasiBiaya">0</span></strong>
                                    <small class="d-block text-muted">per porsi untuk bahan ini</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Keterangan</label>
                                <input type="text" name="keterangan" class="form-control"
                                       placeholder="Contoh: dicincang halus">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i> Tambah ke Resep
                            </button>
                        </div>
                    </form>
                </div>

                <!-- INFO HPP -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calculator"></i> Ringkasan HPP</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td>HPP per Porsi</td>
                                <td class="text-right font-weight-bold">
                                    Rp <?= number_format($hppTotal) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Harga Jual</td>
                                <td class="text-right">Rp <?= number_format($product->price) ?></td>
                            </tr>
                            <tr class="border-top">
                                <td>Margin Kotor</td>
                                <td class="text-right">
                                    <?php
                                    $margin = ($product->price > 0 && $hppTotal > 0)
                                        ? round((($product->price - $hppTotal) / $product->price) * 100, 1)
                                        : 0;
                                    $cls = $margin >= 30 ? 'success' : ($margin >= 15 ? 'warning' : 'danger');
                                    ?>
                                    <span class="badge badge-<?= $cls ?>">
                                        Rp <?= number_format($product->price - $hppTotal) ?> (<?= $margin ?>%)
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TABEL RESEP -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i>
                            Daftar Bahan — <?= esc($product->name) ?>
                        </h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <?php if (empty($recipes)): ?>
                            <div class="alert alert-warning m-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                Resep belum diisi. Tambahkan bahan baku di form sebelah kiri.
                            </div>
                        <?php else: ?>
                        <table class="table table-hover table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nama Bahan</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Satuan</th>
                                    <th class="text-right">Harga/Satuan</th>
                                    <th class="text-right">Biaya/Porsi</th>
                                    <th>Keterangan</th>
                                    <th class="text-center" style="width:100px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalHpp = 0;
                                foreach ($recipes as $r):
                                    $biaya = $r->jumlah_kebutuhan * $r->harga_beli_satuan;
                                    $totalHpp += $biaya;
                                ?>
                                <tr>
                                    <td><strong><?= esc($r->nama_bahan) ?></strong></td>
                                    <td class="text-center"><?= (float) $r->jumlah_kebutuhan ?></td>
                                    <td class="text-center"><?= esc($r->satuan) ?></td>
                                    <td class="text-right">Rp <?= number_format($r->harga_beli_satuan) ?></td>
                                    <td class="text-right">
                                        <strong>Rp <?= number_format($biaya) ?></strong>
                                    </td>
                                    <td><small class="text-muted"><?= esc($r->keterangan ?? '-') ?></small></td>
                                    <td class="text-center">
                                        <button class="btn btn-warning btn-xs"
                                                onclick="editResep(<?= $r->id ?>, <?= (float)$r->jumlah_kebutuhan ?>, '<?= esc($r->keterangan ?? '') ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="<?= site_url("admin/recipes/hapus/{$product->id}/{$r->id}") ?>"
                                           class="btn btn-danger btn-xs"
                                           onclick="return confirm('Hapus bahan ini dari resep?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="4" class="text-right">Total HPP per Porsi</th>
                                    <th class="text-right text-danger">
                                        Rp <?= number_format($totalHpp) ?>
                                    </th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" id="editForm" action="">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jumlah Bahan</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Jumlah Kebutuhan per Porsi</label>
                        <input type="number" step="0.001" name="jumlah_kebutuhan"
                               id="editJumlah" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <input type="text" name="keterangan" id="editKeterangan" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
// Hitung estimasi biaya saat pilih bahan
document.getElementById('bahanSelect').addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    document.getElementById('satuanLabel').textContent = opt.dataset.satuan || '-';
    hitungEstimasi();
});

document.getElementById('jumlahInput').addEventListener('input', hitungEstimasi);

function hitungEstimasi() {
    const harga  = parseFloat(document.getElementById('bahanSelect').options[document.getElementById('bahanSelect').selectedIndex].dataset.harga) || 0;
    const jumlah = parseFloat(document.getElementById('jumlahInput').value) || 0;
    const biaya  = Math.round(harga * jumlah);
    document.getElementById('estimasiBiaya').textContent = biaya.toLocaleString('id-ID');
}

// Buka modal edit
function editResep(recipeId, jumlah, keterangan) {
    document.getElementById('editForm').action = '<?= site_url("admin/recipes/update/{$product->id}/") ?>' + recipeId;
    document.getElementById('editJumlah').value    = jumlah;
    document.getElementById('editKeterangan').value = keterangan;
    $('#editModal').modal('show');
}
</script>
<?= $this->endSection() ?>