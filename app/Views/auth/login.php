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

    <?php /* ---- Login dengan Google (OAuth 2.0) ---- */ ?>
    <div class="text-center text-muted my-3"><small>atau</small></div>

    <a href="<?= site_url('auth/google') ?>"
       class="btn btn-outline-secondary btn-block d-flex align-items-center justify-content-center">
        <svg width="18" height="18" viewBox="0 0 48 48" class="mr-2" aria-hidden="true" focusable="false">
            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
        </svg>
        <span>Continue with Google</span>
    </a>

    <p class="mt-3 text-center">
        <a href="forgot_password"><?php echo lang('Auth.login_forgot_password');?></a>
    </p>
</div>

<?= $this->endSection() ?>