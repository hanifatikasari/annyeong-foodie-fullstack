<div class="card shadow-sm">
    <div class="card-body text-center py-4">
        <?php if (!empty($currentUser->avatar)): ?>
            <img src="<?= base_url($currentUser->avatar) ?>" class="rounded-circle mb-2" width="80" height="80" style="object-fit:cover;">
        <?php else: ?>
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width:80px;height:80px;font-size:32px;">
                <?= strtoupper(substr($currentUser->first_name ?? 'U', 0, 1)) ?>
            </div>
        <?php endif; ?>
        <h6 class="font-weight-bold mb-0"><?= esc($currentUser->first_name . ' ' . $currentUser->last_name) ?></h6>
        <small class="text-muted"><?= esc($currentUser->email ?? '') ?></small>
    </div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item">
            <a href="<?= site_url('account') ?>" class="text-dark <?= current_url() === site_url('account') ? 'font-weight-bold text-primary' : '' ?>">
                <i class="fa fa-home mr-2"></i> Dashboard
            </a>
        </li>
        <li class="list-group-item">
            <a href="<?= site_url('account/orders') ?>" class="text-dark">
                <i class="fa fa-shopping-bag mr-2"></i> Pesanan Saya
            </a>
        </li>
        <li class="list-group-item">
            <a href="<?= site_url('account/profile') ?>" class="text-dark">
                <i class="fa fa-user mr-2"></i> Profil Saya
            </a>
        </li>
        <li class="list-group-item">
            <a href="<?= site_url('auth/logout') ?>" class="text-danger">
                <i class="fa fa-sign-out mr-2"></i> Logout
            </a>
        </li>
    </ul>
</div>