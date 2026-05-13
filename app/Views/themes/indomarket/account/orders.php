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
                    <div class="card-header"><h5 class="mb-0">Riwayat Pesanan</h5></div>
                    <div class="card-body p-0">
                        <?php if (!empty($orders)): ?>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Invoice</th>
                                            <th>Tanggal</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td class="font-weight-bold"><?= esc($order['invoice_no']) ?></td>
                                                <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                                <td>Rp <?= number_format($order['total_bayar']) ?></td>
                                                <td>
                                                    <span class="badge status-badge status-<?= $order['status_order'] ?>">
                                                        <?= ucwords(str_replace('_', ' ', $order['status_order'])) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?= site_url('account/orders/' . $order['invoice_no']) ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                                    <?php if ($order['status_order'] === 'pending_payment'): ?>
                                                        <a href="<?= site_url('checkout/upload/' . $order['invoice_no']) ?>" class="btn btn-sm btn-warning">Upload Bukti</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fa fa-shopping-bag fa-4x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada pesanan. <a href="<?= site_url('products') ?>">Mulai belanja!</a></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>