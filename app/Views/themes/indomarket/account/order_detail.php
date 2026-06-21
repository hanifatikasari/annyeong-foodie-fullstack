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
                'pending'              => ['label' => 'Menunggu Pembayaran',    'icon' => 'fa-clock-o',         'color' => 'warning'],
                'processing'           => ['label' => 'Sedang Diproses',        'icon' => 'fa-cog',             'color' => 'primary'],
                'Shipped'              => ['label' => 'Dikirim',                'icon' => 'fa-box',             'color' => 'purple'],
                'completed'            => ['label' => 'Selesai',                'icon' => 'fa-thumbs-up',       'color' => 'success'],
                'cancelled'            => ['label' => 'Dibatalkan',             'icon' => 'fa-times-circle',    'color' => 'danger'],
            ];
               $st = $statusMap[$order->order_status] ?? [
    'label' => $order->order_status,
    'color' => 'secondary'
];
                ?>

                <div class="card shadow-sm mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detail Pesanan</h5>
                        <span class="badge badge-<?= $st['color'] ?> p-2"><?= $st['label'] ?></span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4"><small class="text-muted">Invoice</small><div class="font-weight-bold"><?= esc($order->invoice_no) ?></div></div>
                            <div class="col-md-4"><small class="text-muted">Tanggal</small><div><?= date('d M Y H:i', strtotime($order->created_at)) ?></div></div>
                            <div class="col-md-4"><small class="text-muted">Pembayaran</small><div><?= esc($order->pembayaran) ?></div></div>
                            <div class="col-md-8 mt-2"><small class="text-muted">Alamat Pengiriman</small><div><?= esc($order->shipping_address) ?></div></div>
                            <div class="col-md-4 mt-2"><small class="text-muted">Penerima</small><div><?= esc($order->first_name) ?> <?= esc($order->last_name) ?> — <?= esc($order->phone) ?></div></div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="bg-light">
                                    <tr><th>Produk</th><th class="text-center">Qty</th><th class="text-right">Harga</th><th class="text-right">Subtotal</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($details as $d): ?>
                                        <tr>
                                            <td><?= esc($d->product_name) ?><br><small class="text-muted"><?= esc($d->sku) ?></small></td>
                                            <td class="text-center"><?= $d->qty ?></td>
                                            <td class="text-right">Rp <?= number_format($d->selling_price) ?></td>
                                            <td class="text-right font-weight-bold">Rp <?= number_format($d->subtotal) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-primary">
                                        <th colspan="3">Total Bayar</th>
                                        <th class="text-right">Rp <?= number_format($order->total_bayar) ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <?php if (!empty($order->payment_proof)): ?>
                            <div class="mt-3">
                                <strong>Bukti Pembayaran:</strong><br>
                                <?php $ext = pathinfo($order->payment_proof, PATHINFO_EXTENSION); ?>
                                <?php if (in_array(strtolower($ext), ['jpg','jpeg','png'])): ?>
                                    <img src="<?= base_url($order->payment_proof) ?>" class="img-fluid rounded mt-2" style="max-width:300px;">
                                <?php else: ?>
                                    <a href="<?= base_url($order->payment_proof) ?>" target="_blank" class="btn btn-sm btn-outline-secondary mt-2">
                                        <i class="fa fa-file-pdf-o mr-1"></i> Lihat Bukti
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (
                            $order->payment_status === 'unpaid'
                            && in_array($order->pembayaran, ['Transfer', 'QRIS'])
                        ): ?>
                            <div class="mt-3">
                                <a href="<?= site_url('checkout/upload/' . $order->invoice_no) ?>" class="btn btn-warning">
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