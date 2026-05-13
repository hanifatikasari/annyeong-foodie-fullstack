<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?= $this->include('themes/indomarket/account/_sidebar') ?>
            </div>
            <div class="col-lg-9">
                <div class="card shadow-sm">
                    <div class="card-header"><h5 class="mb-0">Profil Saya</h5></div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                        <?php endif; ?>

                        <form action="<?= site_url('account/profile/update') ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="text-center mb-4">
                                <?php if (!empty($user->avatar)): ?>
                                    <img src="<?= base_url($user->avatar) ?>" class="rounded-circle" width="100" height="100" style="object-fit:cover;">
                                <?php else: ?>
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width:100px;height:100px;font-size:40px;">
                                        <?= strtoupper(substr($user->first_name ?? 'U', 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="mt-2">
                                    <input type="file" name="avatar" class="form-control-file" accept=".jpg,.jpeg,.png" style="max-width:250px;margin:auto;">
                                    <small class="text-muted">Upload foto profil (opsional)</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Depan</label>
                                        <input type="text" name="first_name" class="form-control" value="<?= esc($user->first_name) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Belakang</label>
                                        <input type="text" name="last_name" class="form-control" value="<?= esc($user->last_name ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>No. HP</label>
                                        <input type="text" name="phone" class="form-control" value="<?= esc($user->phone ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Alamat Lengkap</label>
                                        <textarea name="alamat" class="form-control" rows="3"><?= esc($user->alamat ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Kota</label>
                                        <input type="text" name="kota" class="form-control" value="<?= esc($user->kota ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Provinsi</label>
                                        <input type="text" name="provinsi" class="form-control" value="<?= esc($user->provinsi ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Kode Pos</label>
                                        <input type="text" name="kode_pos" class="form-control" value="<?= esc($user->kode_pos ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save mr-1"></i> Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>