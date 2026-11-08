<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="py-5">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="font-weight-bold mb-1">Notifikasi Saya</h3>
                <p class="text-muted mb-0">Informasi terbaru mengenai pesanan Anda.</p>
            </div>
        </div>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php $unreadCount = 0;
        foreach ($notifications as $notification) {
            if ((int) $notification->is_read === 0) {
                $unreadCount++;
            }
        }
        ?>

        <?php if ($unreadCount > 0): ?>
            <form action="<?= site_url('account/notifications/read-all') ?>"
                  method="post"
                  class="text-right mb-3">
                <?= csrf_field() ?>
                <button type="submit"class="btn btn-sm btn-outline-primary">Tandai semua dibaca</button>
            </form>
        <?php endif; ?>

        <?php if (empty($notifications)): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="far fa-bell fa-3x text-muted mb-3"></i>
                    <h5>Belum Ada Notifikasi</h5>
                    <p class="text-muted mb-0">Notifikasi pesanan Anda akan muncul di sini.</p>
                </div>
            </div>
        <?php else: ?>

            <?php foreach ($notifications as $notification): ?>
                <div class="card shadow-sm mb-3
                    <?= (int) $notification->is_read === 0 ? 'border-left border-primary' : '' ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="pr-3">
                                <h6 class="font-weight-bold mb-1">
                                    <?= esc($notification->title) ?>
                                    <?php if ((int) $notification->is_read === 0): ?>
                                        <span class="badge badge-primary">Baru</span>
                                    <?php endif; ?>
                                </h6>

                                <p class="text-muted mb-2"><?= esc($notification->message) ?></p>
                                <small class="text-muted">
                                    <?= date('d M Y, H:i',strtotime($notification->created_at)) ?>
                                </small>
                            </div>

                            <div class="align-self-center">
                                <?php if (!empty($notification->reference_id)): ?>
                                    <form action="<?= site_url('account/notifications/open/' .$notification->id) ?>"method="post">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Lihat</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
<?= $this->endSection() ?>