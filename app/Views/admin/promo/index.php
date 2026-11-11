<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Promo &amp; Diskon</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Promo &amp; Diskon</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <?= view('admin/shared/flash_message') ?>

        <div class="callout callout-info">
            <p class="mb-0 small">
                Promo bersifat <strong>otomatis</strong>: pelanggan mendapat diskon dari tingkat dengan minimal
                belanja tertinggi yang terpenuhi (hanya satu tingkat per transaksi). Minimal belanja dihitung dari
                subtotal produk, di luar ongkir dan biaya penanganan. Perubahan di sini langsung tampil di banner
                keranjang dan halaman checkout.
            </p>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <a href="<?= site_url('admin/promo/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Promo
                        </a>
                    </div>
                    <div class="col-md-6">
                        <form method="get">
                            <div class="input-group">
                                <input type="text" name="table_search" value="<?= esc($keyword) ?>"
                                       class="form-control" placeholder="Cari nama promo">
                                <input type="hidden" name="perPage" value="<?= (int) $perPage ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-default"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Nama Promo</th>
                            <th>Minimal Belanja</th>
                            <th>Diskon</th>
                            <th>Periode</th>
                            <th>Dipakai</th>
                            <th>Status</th>
                            <th width="190">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $badge = [
                            'berjalan'    => ['success',   'Berjalan'],
                            'dijadwalkan' => ['info',      'Dijadwalkan'],
                            'berakhir'    => ['secondary', 'Berakhir'],
                            'kuota_habis' => ['warning',   'Kuota Habis'],
                            'nonaktif'    => ['secondary', 'Nonaktif'],
                        ];
                        $no = 1 + (($pager->getCurrentPage('promo') - 1) * $perPage);
                        ?>
                        <?php if (empty($promos)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada promo.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($promos as $row): ?>
                            <?php [$cls, $txt] = $badge[$row->status] ?? ['secondary', $row->status]; ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($row->nama_promo) ?></td>
                                <td>Rp <?= number_format((int) $row->min_subtotal, 0, ',', '.') ?></td>
                                <td>
                                    <?php if ($row->jenis_diskon === 'percentage'): ?>
                                        <?= (int) $row->nilai_diskon ?>%
                                        <?php if ($row->max_diskon !== null): ?>
                                            <small class="text-muted">(maks Rp <?= number_format((int) $row->max_diskon, 0, ',', '.') ?>)</small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        Rp <?= number_format((int) $row->nilai_diskon, 0, ',', '.') ?>
                                    <?php endif; ?>
                                </td>
                                <td class="small">
                                    <?php if ($row->starts_at === null && $row->ends_at === null): ?>
                                        <span class="text-muted">Tanpa batas</span>
                                    <?php else: ?>
                                        <?= $row->starts_at ? date('d/m/Y H:i', strtotime($row->starts_at)) : '—' ?>
                                        s/d
                                        <?= $row->ends_at ? date('d/m/Y H:i', strtotime($row->ends_at)) : '—' ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= (int) $row->dipakai ?>x
                                    <?php if ($row->usage_limit !== null): ?>
                                        <small class="text-muted">(kuota <?= (int) $row->usage_count ?>/<?= (int) $row->usage_limit ?>)</small>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge badge-<?= $cls ?>"><?= $txt ?></span></td>
                                <td>
                                    <form method="post" action="<?= site_url('admin/promo/toggle/' . $row->id) ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm <?= $row->is_active ? 'btn-success' : 'btn-outline-secondary' ?>"
                                                title="<?= $row->is_active ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                            <i class="fas fa-toggle-<?= $row->is_active ? 'on' : 'off' ?>"></i>
                                        </button>
                                    </form>

                                    <a href="<?= site_url('admin/promo/edit/' . $row->id) ?>"
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form method="post" action="<?= site_url('admin/promo/delete/' . $row->id) ?>"
                                          class="d-inline"
                                          onsubmit="return confirm('Hapus promo ini?')">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-danger btn-sm"
                                                <?= (int) $row->dipakai > 0 ? 'disabled' : '' ?>
                                                title="<?= (int) $row->dipakai > 0 ? 'Sudah dipakai transaksi, nonaktifkan saja' : 'Hapus' ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer clearfix">
                <div class="float-left">
                    <form method="get">
                        <input type="hidden" name="table_search" value="<?= esc($keyword) ?>">
                        <select class="form-control" name="perPage" onchange="this.form.submit()">
                            <?php foreach ([10, 25, 50] as $n): ?>
                                <option value="<?= $n ?>" <?= $perPage === $n ? 'selected' : '' ?>><?= $n ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                <div class="float-right">
                    <?= $pager->links('promo', 'bootstrap') ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>