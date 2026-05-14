<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<?php
/**
 * FIX ISSUE #8: Null guards — $user bisa null saat pertama kali
 * Gunakan ?? '' untuk semua property yang opsional
 */
$user = $user ?? $currentUser ?? null;
?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <!-- Sidebar Akun -->
            <div class="col-lg-3 mb-4">
                <?= $this->include('themes/indomarket/account/_sidebar') ?>
            </div>

            <!-- Form Profil -->
            <div class="col-lg-9">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fa fa-user-circle mr-2 text-primary"></i>Profil Saya
                        </h5>
                    </div>
                    <div class="card-body">

                        <!-- Flash Message -->
                        <?php if ($flashSuccess = session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fa fa-check-circle mr-1"></i> <?= esc($flashSuccess) ?>
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            </div>
                        <?php endif; ?>

                        <!-- Validation Errors -->
                        <?php if (!empty(session('errors'))): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?php foreach (session('errors') as $err): ?>
                                    <div><?= esc($err) ?></div>
                                <?php endforeach; ?>
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            </div>
                        <?php endif; ?>

                        <form action="<?= site_url('account/profile/update') ?>"
                              method="post"
                              enctype="multipart/form-data">
                            <?= csrf_field() ?>

                            <!-- Avatar -->
                            <div class="text-center mb-4">
                                <?php if (!empty($user->avatar)): ?>
                                    <img src="<?= base_url(esc($user->avatar)) ?>"
                                         class="rounded-circle mb-2"
                                         width="90" height="90"
                                         style="object-fit:cover;border:3px solid #e83e8c;">
                                <?php else: ?>
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2"
                                         style="width:90px;height:90px;font-size:36px;">
                                        <?= strtoupper(substr($user->first_name ?? 'U', 0, 1)) ?>
                                    </div>
                                <?php endif; ?>

                                <div>
                                    <label class="btn btn-outline-secondary btn-sm mt-1">
                                        <i class="fa fa-camera mr-1"></i> Ganti Foto
                                        <input type="file" name="avatar" accept=".jpg,.jpeg,.png"
                                               class="d-none" id="avatarInput">
                                    </label>
                                    <div id="avatarPreview" class="mt-1"></div>
                                    <small class="text-muted d-block">Format: JPG, PNG. Maks. 2MB.</small>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <!-- Nama Depan -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">Nama Depan <span class="text-danger">*</span></label>
                                        <input type="text"
                                               id="first_name"
                                               name="first_name"
                                               class="form-control"
                                               value="<?= esc(old('first_name', $user->first_name ?? '')) ?>"
                                               required>
                                    </div>
                                </div>

                                <!-- Nama Belakang -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Nama Belakang</label>
                                        <input type="text"
                                               id="last_name"
                                               name="last_name"
                                               class="form-control"
                                               value="<?= esc(old('last_name', $user->last_name ?? '')) ?>">
                                    </div>
                                </div>

                                <!-- Email (read-only) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email"
                                               class="form-control bg-light"
                                               value="<?= esc($user->email ?? '') ?>"
                                               readonly>
                                        <small class="text-muted">Email tidak dapat diubah.</small>
                                    </div>
                                </div>

                                <!-- No. HP -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">No. HP / WhatsApp</label>
                                        <input type="tel"
                                               id="phone"
                                               name="phone"
                                               class="form-control"
                                               placeholder="08xxxxxxxxxx"
                                               value="<?= esc(old('phone', $user->phone ?? '')) ?>">
                                    </div>
                                </div>

                                <!-- Alamat -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="alamat">Alamat Lengkap</label>
                                        <textarea id="alamat"
                                                  name="alamat"
                                                  class="form-control"
                                                  rows="3"
                                                  placeholder="Jl. ..., RT/RW, Kelurahan, Kecamatan"><?= esc(old('alamat', $user->address ?? '')) ?></textarea>
                                        <small class="text-muted">
                                            Alamat ini akan otomatis diisi saat checkout.
                                        </small>
                                    </div>
                                </div>

                                <!-- Kota -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kota">Kota</label>
                                        <input type="text"
                                               id="kota"
                                               name="kota"
                                               class="form-control"
                                               value="<?= esc(old('kota', $user->city ?? '')) ?>">
                                    </div>
                                </div>

                                <!-- Provinsi -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="provinsi">Provinsi</label>
                                        <input type="text"
                                               id="provinsi"
                                               name="provinsi"
                                               class="form-control"
                                               value="<?= esc(old('provinsi', $user->province ?? '')) ?>">
                                    </div>
                                </div>

                                <!-- Kode Pos -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kode_pos">Kode Pos</label>
                                        <input type="text"
                                               id="kode_pos"
                                               name="kode_pos"
                                               class="form-control"
                                               placeholder="52300"
                                               value="<?= esc(old('kode_pos', $user->postal_code ?? '')) ?>">
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fa fa-save mr-1"></i> Simpan Perubahan
                                </button>
                                <a href="<?= site_url('account') ?>" class="btn btn-outline-secondary">
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Preview avatar sebelum upload
document.getElementById('avatarInput').addEventListener('change', function() {
    var file = this.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').innerHTML =
                '<img src="' + e.target.result + '" class="rounded-circle mt-1" width="60" height="60" style="object-fit:cover;">' +
                '<small class="text-success d-block">Foto baru dipilih</small>';
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?= $this->endSection() ?>