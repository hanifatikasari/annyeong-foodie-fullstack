<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="py-5">
    <div class="container">
        <?php
        $statusMap = [
            'pending_payment'      => ['label' => 'Menunggu Pembayaran',    'icon' => 'fa-clock-o',         'color' => 'warning'],
            'pending_verification' => ['label' => 'Verifikasi Pembayaran',  'icon' => 'fa-search',          'color' => 'info'],
            'verified'             => ['label' => 'Pembayaran Terverifikasi','icon' => 'fa-check-circle',   'color' => 'success'],
            'processing'           => ['label' => 'Sedang Diproses',        'icon' => 'fa-cog',             'color' => 'primary'],
            'ready'                => ['label' => 'Siap Dikirim/Diambil',   'icon' => 'fa-box',             'color' => 'purple'],
            'completed'            => ['label' => 'Selesai',                'icon' => 'fa-thumbs-up',       'color' => 'success'],
            'cancelled'            => ['label' => 'Dibatalkan',             'icon' => 'fa-times-circle',    'color' => 'danger'],
        ];
        $st = $statusMap[$order['status_order']] ?? ['label' => $order['status_order'], 'icon' => 'fa-question', 'color' => 'secondary'];
        ?>

        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                        <h5 class="mb-0">Pesanan <?= esc($order['invoice_no']) ?></h5>
                        <span class="badge badge-<?= $st['color'] ?> badge-pill p-2">
                            <i class="fa <?= $st['icon'] ?> mr-1"></i> <?= $st['label'] ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <small class="text-muted">Tanggal Order</small>
                                <div class="font-weight-bold"><?= date('d M Y H:i', strtotime($order['created_at'])) ?></div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Metode Pembayaran</small>
                                <div class="font-weight-bold"><?= esc($order['pembayaran']) ?></div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <small class="text-muted">Nama Penerima</small>
                                <div><?= esc($order['nama_penerima']) ?></div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <small class="text-muted">No. HP</small>
                                <div><?= esc($order['no_hp_penerima']) ?></div>
                            </div>
                            <div class="col-12 mt-2">
                                <small class="text-muted">Alamat Pengiriman</small>
                                <div><?= esc($order['alamat_pengiriman']) ?></div>
                            </div>
                        </div>

                        <!-- Item -->
                        <div class="table-responsive">
                            <table class="table table-sm border">
                                <thead class="bg-light">
                                    <tr><th>Produk</th><th class="text-center">Qty</th><th class="text-right">Subtotal</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($details as $d): ?>
                                        <tr>
                                            <td><?= esc($d['product_name']) ?></td>
                                            <td class="text-center"><?= $d['qty'] ?></td>
                                            <td class="text-right">Rp <?= number_format($d['subtotal']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-primary">
                                        <th colspan="2">TOTAL</th>
                                        <th class="text-right">Rp <?= number_format($order['total_bayar']) ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <?php if ($order['status_order'] === 'pending_payment'): ?>
                            <div class="text-center mt-3">
                                <a href="<?= site_url('checkout/upload/' . $order['invoice_no']) ?>" class="btn btn-warning">
                                    <i class="fa fa-upload mr-1"></i> Upload Bukti Pembayaran
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-center">
                    <a href="<?= site_url('track') ?>" class="btn btn-outline-secondary mr-2">
                        <i class="fa fa-arrow-left mr-1"></i> Lacak Pesanan Lain
                    </a>
                    <a href="<?= site_url('products') ?>" class="btn btn-primary">
                        <i class="fa fa-shopping-bag mr-1"></i> Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>