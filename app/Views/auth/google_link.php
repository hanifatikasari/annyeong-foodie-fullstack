<?= $this->extend('themes/indomarket/layout_auth') ?>

<?= $this->section('content') ?>

<div class="card-body p-4">
    <h1 class="text-center h3">Hubungkan Akun Google</h1>
    <p class="text-muted text-center">
        Email Google Anda sudah terdaftar di sini. Untuk keamanan, masukkan password akun tersebut
        untuk menghubungkannya dengan Google.
    </p>

    <?php if (! empty($message)): ?>
        <?php $isSuccess = preg_match('/Success|Logged Out|Berhasil/i', (string) $message); ?>
        <div class="alert <?= $isSuccess ? 'alert-info' : 'alert-danger' ?> text-dark">
            <?= esc((string) $message) ?>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('auth/google/link') ?>" method="post">
        <?= csrf_field() ?>

        <div class="form-group mb-3">
            <label for="google_email">Email</label>
            <input type="email" id="google_email" class="form-control" value="<?= esc($email) ?>" readonly>
        </div>

        <div class="form-group mb-3">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control"
                   required autocomplete="current-password" autofocus>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-block">Hubungkan &amp; Masuk</button>
        </div>
    </form>

    <div class="mt-3 text-center">
        <p class="mb-2">
            <a href="<?= site_url('auth/forgot_password') ?>">
                Lupa password akun Batterbit?
            </a>
        </p>
        <a href="<?= site_url('auth/google/cancel') ?>">Batal</a>
    </div>
</div>

<?= $this->endSection() ?>