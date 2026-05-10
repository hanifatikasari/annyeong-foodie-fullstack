<?= $this->extend('themes/indomarket/layout_auth') ?>

<?= $this->section('content') ?>

<div class="card-body p-4"> <h1 class="text-center"><?php echo lang('Auth.login_heading');?></h1>
    <p class="text-muted text-center"><?php echo lang('Auth.login_subheading');?></p>

    <div id="infoMessage" class="mb-3">
        <?php if (!empty($message)): ?>
            <?php 
                // Tentukan warna berdasarkan isi pesan
                // Jika ada kata 'Success' atau 'Logged Out', pakai warna Biru (info)
                // Selain itu (Error/Incorrect/Required), pakai warna Merah (danger)
                $isSuccess = preg_match('/Success|Logged Out|Berhasil/i', $message);
                $class = $isSuccess ? 'alert-info' : 'alert-danger';
            ?>
            <div class="alert <?= $class ?> text-dark">
                <?= $message ?>
            </div>
        <?php endif; ?>
    </div>

    <?php echo form_open('auth/login');?>

        <div class="form-group mb-3">
            <label for="identity"><?php echo lang('Auth.login_identity_label');?></label>
            <?php echo form_input($identity);?>
        </div>

        <div class="form-group mb-3">
            <label for="password"><?php echo lang('Auth.login_password_label');?></label>
            <?php echo form_input($password);?>
        </div>

        <div class="form-check mb-3">
            <?php echo form_checkbox('remember', '1', false, 'id="remember" class="form-check-input"');?>
            <label class="form-check-label" for="remember">
                <?php echo lang('Auth.login_remember_label');?>
            </label>
        </div>

        <div class="d-grid"> <?php echo form_submit('submit', lang('Auth.login_submit_btn'), 'class="btn btn-primary btn-block"');?>
        </div>

    <?php echo form_close();?>

    <p class="mt-3 text-center">
        <a href="forgot_password"><?php echo lang('Auth.login_forgot_password');?></a>
    </p>
</div>

<?= $this->endSection() ?>