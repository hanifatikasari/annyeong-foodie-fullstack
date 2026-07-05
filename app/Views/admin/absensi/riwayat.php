<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Riwayat Absensi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="<?= site_url('admin/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= site_url('admin/absensi') ?>">Absensi</a>
                    </li>
                    <li class="breadcrumb-item active">Riwayat</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <!-- Tombol kembali -->
        <div class="mb-3">
            <a href="<?= site_url('admin/absensi') ?>" class="btn btn-success">
                <i class="fas fa-arrow-left"></i> Kembali ke Absensi
            </a>
        </div>

        <div class="card">
            
            <div class="card-body">
                <!-- Form Filter -->
                <form method="get">
                    <div class="row">

                        <!-- Bulan -->
                        <div class="col-md-3">
                            <label>Bulan</label>
                            <select name="bulan" class="form-control">
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= sprintf('%02d', $i) ?>"
                                        <?= $bulan == sprintf('%02d', $i) ? 'selected' : '' ?>>
                                        <?= date('F', strtotime("2025-$i-01")) ?>
                                    </option>
                                <?php endfor ?>
                            </select>
                        </div>

                        <!-- Tahun -->
                        <div class="col-md-2">
                            <label>Tahun</label>
                            <select name="tahun" class="form-control">
                                <?php for ($i = date('Y'); $i >= 2024; $i--): ?>
                                    <option <?= $tahun == $i ? 'selected' : '' ?>>
                                        <?= $i ?>
                                    </option>
                                <?php endfor ?>
                            </select>
                        </div>

                        <!-- Pegawai (hanya admin) -->
                        <?php if ($isAdmin): ?>
                            <div class="col-md-4">
                                <label>Pegawai</label>
                                <select name="pegawai" class="form-control">
                                    <option value="">Semua Pegawai</option>
                                    <?php foreach ($pegawaiList as $p): ?>
                                        <option value="<?= $p->id ?>"
                                            <?= $pegawai == $p->id ? 'selected' : '' ?>>
                                            <?= esc($p->first_name . ' ' . $p->last_name) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <!-- Tombol Filter + Export PDF -->
                        <div class="col-md-2 d-flex align-items-end">
                            <label>&nbsp;</label>
                            <button class="btn btn-primary btn-block mr-2">Filter</button>
                            <a href="<?= site_url('admin/absensi/pdf?bulan='.$bulan.'&tahun='.$tahun.'&pegawai='.$pegawai) ?>"
                            class="btn btn-danger btn-block">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Tabel Riwayat Absensi -->
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <?php if ($isAdmin): ?>
                                    <th>Pegawai</th>
                                <?php endif; ?>
                                <th>Tanggal</th>
                                <th>Masuk</th>
                                <th>Status Masuk</th>
                                <th>Keluar</th>
                                <th>Status Keluar</th>
                                <th>Total Jam</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riwayat as $r): ?>
                                <tr>
                                    <?php if ($isAdmin): ?>
                                        <td><?= esc(trim($r->first_name . ' ' . $r->last_name)) ?></td>
                                    <?php endif; ?>
                                    <td><?= date('d-m-Y', strtotime($r->jam_masuk)) ?></td>
                                    <td><?= $r->jam_masuk ? date('H:i', strtotime($r->jam_masuk)) : '-' ?></td>
                                    <td>
                                        <?php if ($r->status_masuk == 'Tepat Waktu'): ?>
                                            <span class="badge badge-success">Tepat Waktu</span>
                                        <?php elseif ($r->status_masuk == 'Terlambat'): ?>
                                            <span class="badge badge-warning">Terlambat</span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $r->jam_keluar ? date('H:i', strtotime($r->jam_keluar)) : '-' ?></td>
                                    <td>
                                        <?php if ($r->status_keluar == 'Normal'): ?>
                                            <span class="badge badge-success">Normal</span>
                                        <?php elseif ($r->status_keluar == 'Pulang Cepat'): ?>
                                            <span class="badge badge-info">Pulang Cepat</span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $r->total_jam ?? '-' ?> Jam</td>
                                    <td><?= esc($r->keterangan) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</section>


<?= $this->endSection() ?>
