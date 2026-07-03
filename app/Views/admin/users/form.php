<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><?= $user ? 'Edit Pegawai' : 'Tambah Pegawai' ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/users') ?>">Kelola User</a></li>
                    <li class="breadcrumb-item active"><?= $user ? 'Edit' : 'Tambah' ?></li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('admin/shared/flash_message') ?>
        <div class="card">
            <div class="card-body">
                <form action="<?= site_url($user ? 'admin/users/update/' . $user->id : 'admin/users/store') ?>" method="post">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="<?= esc($user->username ?? old('username')) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Depan</label>
                        <input type="text" name="first_name" class="form-control" value="<?= esc($user->first_name ?? old('first_name')) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Belakang</label>
                        <input type="text" name="last_name" class="form-control" value="<?= esc($user->last_name ?? old('last_name')) ?>">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= esc($user->email ?? old('email')) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?= esc($user->phone ?? old('phone')) ?>">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="group" class="form-control" required>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= esc($role->id) ?>" <?= isset($userGroup) && $userGroup == $role->id ? 'selected' : '' ?>><?= esc($role->name) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <?php if (! $user): ?>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Ulangi Password</label>
                            <input type="password" name="password_confirm" class="form-control" required>
                        </div>
                    <?php endif ?>
                    <button type="submit" class="btn btn-primary"><?= $user ? 'Simpan Perubahan' : 'Tambah User' ?></button>
                    <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
