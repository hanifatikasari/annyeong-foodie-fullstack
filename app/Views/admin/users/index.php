<?= $this->extend('admin/layout') ?>

<?php $ionAuth = new \IonAuth\Libraries\IonAuth(); ?>

<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Kelola User</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kelola User</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('admin/shared/flash_message') ?>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title">Daftar User</h3>
                </div>
                <div class="card-tools">
                    <?php if ($ionAuth->inGroup('pemilik')): ?>
                        <a href="<?= site_url('admin/users/create') ?>" class="btn btn-success btn-sm">Tambah Pegawai</a>
                        <a href="<?= site_url('admin/users/activity') ?>" class="btn btn-info btn-sm">Aktivitas User</a>
                        <form action="<?= site_url('admin/users/reset-password-all') ?>" method="post" class="d-inline" onsubmit="return confirm('Reset semua password ke password default?')">
                            <button type="submit" class="btn btn-warning btn-sm">Reset Semua Password</button>
                        </form>
                    <?php endif ?>
                    <a href="<?= site_url('auth/change_password') ?>" class="btn btn-secondary btn-sm">Ubah Password Saya</a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Groups</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= esc($user->username ?? '') ?></td>
                                    <td><?= esc(trim($user->first_name . ' ' . $user->last_name)) ?></td>
                                    <td><?= esc($user->email ?? '') ?></td>
                                    <td>
                                        <?php if (!empty($user->groups)): ?>
                                            <?php foreach ($user->groups as $group): ?>
                                                <span class="badge badge-secondary"><?= esc($group->name) ?></span>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </td>
                                    <td>
                                        <?= $user->active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak Aktif</span>' ?>
                                    </td>
                                    <td>
                                        <?php if ($ionAuth->inGroup('pemilik')): ?>
                                            <a href="<?= site_url('admin/users/edit/' . $user->id) ?>" class="btn btn-primary btn-sm">Edit</a>
                                            <a href="<?= site_url('admin/users/delete/' . $user->id) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus user ini?')">Hapus</a>
                                            <?php if ($user->active): ?>
                                                <a href="<?= site_url('admin/users/deactivate/' . $user->id) ?>" class="btn btn-warning btn-sm">Nonaktifkan</a>
                                            <?php else: ?>
                                                <a href="<?= site_url('admin/users/activate/' . $user->id) ?>" class="btn btn-success btn-sm">Aktifkan</a>
                                            <?php endif ?>
                                            <a href="<?= site_url('admin/users/reset-password/' . $user->id) ?>" class="btn btn-secondary btn-sm">Reset Password</a>
                                        <?php elseif ($ionAuth->inGroup('admin')): ?>
                                            <a href="<?= site_url('admin/users/reset-password/' . $user->id) ?>" class="btn btn-secondary btn-sm">Reset Password</a>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">Tidak ada user ditemukan.</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
