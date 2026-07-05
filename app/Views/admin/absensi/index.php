<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <h1>Absensi Pegawai</h1>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('admin/shared/flash_message') ?>
        <div class="row">
            <div class="col-md-5">
                <div class="card card-primary">
                    <div class="card-header"><h3 class="card-title">Absensi Hari Ini</h3></div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="35%">Tanggal</th>
                                <td><?= date('d F Y') ?></td>
                            </tr>

                            <tr>
                                <th>Jam Masuk</th>
                                <td>
                                    <?= $absen && $absen->jam_masuk
                                        ? date('H:i:s', strtotime($absen->jam_masuk))
                                        : '-' ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Status Masuk</th>
                                <td>
                                    <?php if(!$absen || !$absen->status_masuk): ?>
                                        -
                                    <?php elseif($absen->status_masuk == 'Tepat Waktu'): ?>
                                        <span class="badge badge-success">
                                            <?= $absen->status_masuk ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">
                                            <?= $absen->status_masuk ?>
                                        </span>
                                    <?php endif ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Jam Keluar</th>
                                <td>
                                    <?= $absen && $absen->jam_keluar
                                        ? date('H:i:s', strtotime($absen->jam_keluar))
                                        : '-' ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Status Keluar</th>
                                <td>
                                    <?php if(!$absen || !$absen->status_keluar): ?>
                                        -
                                    <?php elseif($absen->status_keluar == 'Normal'): ?>
                                        <span class="badge badge-success">
                                            <?= $absen->status_keluar ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-info">
                                            <?= $absen->status_keluar ?>
                                        </span>
                                    <?php endif ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Total Jam</th>
                                <td>
                                    <?= $absen && $absen->total_jam
                                        ? $absen->total_jam . ' Jam'
                                        : '-' ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Status</th>
                                <td>
                                    <?php if (!$absen): ?>
                                        <span class="badge badge-secondary">Belum Absen</span>
                                    <?php elseif($absen->jam_masuk && !$absen->jam_keluar): ?>
                                        <span class="badge badge-warning">Sedang Bekerja</span>
                                    <?php else: ?>
                                        <span class="badge badge-success"> Selesai </span>
                                    <?php endif ?>
                                </td>
                            </tr>

                        </table>
                    </div>
                    <div class="card-footer">
                        <?php if(!$absen): ?>
                            <form action="<?= site_url('admin/absensi/masuk') ?>" method="post">
                                <?= csrf_field() ?>
                                <button class="btn btn-success btn-block"><i class="fas fa-sign-in-alt"></i>Absen Masuk</button>
                            </form>
                        <?php elseif(!$absen->jam_keluar): ?>
                            <form action="<?= site_url('admin/absensi/keluar') ?>" method="post">
                                <?= csrf_field() ?>
                                <button class="btn btn-danger btn-block"><i class="fas fa-sign-out-alt"></i>Absen Keluar</button>
                            </form>

                        <?php else: ?>
                            <button class="btn btn-secondary btn-block" disabled>Absensi Hari Ini Selesai</button>
                        <?php endif ?>
                    </div>
                </div>
                <div class="mb-3">
    <a href="<?= site_url('admin/absensi/riwayat') ?>"
       class="btn btn-info">
        <i class="fas fa-history"></i>
        Riwayat Absensi
    </a>
</div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>