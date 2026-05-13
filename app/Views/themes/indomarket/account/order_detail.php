<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?= $this->include('themes/indomarket/account/_sidebar') ?>
            </div>
            <div class="col-lg-9">
                <?php
                $statusMap = [
                    'pending_payment'      => ['label' => 'Menunggu Pembayaran',     'color' => 'warning'],
                    'pending_verification' => ['label' => 'Verifikasi Pembayaran',   'color' => 'info'],
                    'verified'             => ['label' => 'Pembayaran Terverifikasi','color' => 'success'],
                    'processing'           => ['label' => 'Sedang Diproses',         'color' => 'primary'],
                    'ready'                => ['label' => 'Siap Dikirim/Diambil',    'color' => 'secondary'],
                    'completed'            => ['label' => 'Selesai',                 'color' => 'success'],
                    'cancelled'            => ['label' => 'Dibatalkan',              'color' => 'danger'],
                ];
                $st = $statusMap[$order['status_order']] ?? ['label' => $order['status_order'], 'color' => 'secondary'];
                ?>

                <div class="card shadow-sm mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detail Pesanan</h5>
                        <span class="badge badge-<?= $st['color'] ?> p-2"><?= $st['label'] ?></span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4"><small class="text-muted">Invoice</small><div class="font-weight-bold"><?= esc($order['invoice_no']) ?></div></div>
                            <div class="col-md-4"><small class="text-muted">Tanggal</small><div><?= date('d M Y H:i', strtotime($order['created_at'])) ?></div></div>
                            <div class="col-md-4"><small class="text-muted">Pembayaran</small><div><?= esc($order['pembayaran']) ?></div></div>
                            <div class="col-md-8 mt-2"><small class="text-muted">Alamat Pengiriman</small><div><?= esc($order['alamat_pengiriman']) ?></div></div>
                            <div class="col-md-4 mt-2"><small class="text-muted">Penerima</small><div><?= esc($order['nama_penerima']) ?> — <?= esc($order['no_hp_penerima']) ?></div></div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="bg-light">
                                    <tr><th>Produk</th><th class="text-center">Qty</th><th class="text-right">Harga</th><th class="text-right">Subtotal</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($details as $d): ?>
                                        <tr>
                                            <td><?= esc($d['product_name']) ?><br><small class="text-muted"><?= esc($d['sku']) ?></small></td>
                                            <td class="text-center"><?= $d['qty'] ?></td>
                                            <td class="text-right">Rp <?= number_format($d['selling_price']) ?></td>
                                            <td class="text-right font-weight-bold">Rp <?= number_format($d['subtotal']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-primary">
                                        <th colspan="3">Total Bayar</th>
                                        <th class="text-right">Rp <?= number_format($order['total_bayar']) ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <?php if (!empty($order['bukti_bayar'])): ?>
                            <div class="mt-3">
                                <strong>Bukti Pembayaran:</strong><br>
                                <?php $ext = pathinfo($order['bukti_bayar'], PATHINFO_EXTENSION); ?>
                                <?php if (in_array(strtolower($ext), ['jpg','jpeg','png'])): ?>
                                    <img src="<?= base_url($order['bukti_bayar']) ?>" class="img-fluid rounded mt-2" style="max-width:300px;">
                                <?php else: ?>
                                    <a href="<?= base_url($order['bukti_bayar']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary mt-2">
                                        <i class="fa fa-file-pdf-o mr-1"></i> Lihat Bukti
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($order['status_order'] === 'pending_payment'): ?>
                            <div class="mt-3">
                                <a href="<?= site_url('checkout/upload/' . $order['invoice_no']) ?>" class="btn btn-warning">
                                    <i class="fa fa-upload mr-1"></i> Upload Bukti Pembayaran
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="mt-3">
                            <a href="<?= site_url('account/orders') ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="fa fa-arrow-left mr-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>