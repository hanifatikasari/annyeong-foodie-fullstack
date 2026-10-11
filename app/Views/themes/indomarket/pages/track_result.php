<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="py-5">
    <div class="container">
        <?php
        $statusMap = [
            'pending'              => ['label' => 'Menunggu Pembayaran',    'icon' => 'fa-clock-o',         'color' => 'warning'],
            'processing'           => ['label' => 'Sedang Diproses',        'icon' => 'fa-cog',             'color' => 'primary'],
            'Shipped'              => ['label' => 'Dikirim',                'icon' => 'fa-box',             'color' => 'purple'],
            'completed'            => ['label' => 'Selesai',                'icon' => 'fa-thumbs-up',       'color' => 'success'],
            'cancelled'            => ['label' => 'Dibatalkan',             'icon' => 'fa-times-circle',    'color' => 'danger'],
        ];
        $status = $order->order_status ?? '';
        $st = $statusMap[$status] ?? [
            'label' => $status ?: 'Unknown',
            'icon'  => 'fa-question',
            'color' => 'secondary'
        ];
        ?>
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                        <h5 class="mb-0" style="color: #f0f0f0;">Pesanan <?= esc($order->invoice_no) ?></h5>
                        <span class="badge badge-<?= $st['color'] ?> badge-pill p-2">
                            <i class="fa <?= $st['icon'] ?> mr-1"></i> <?= $st['label'] ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <small class="text-muted">Tanggal Order</small>
                                <div class="font-weight-bold"><?= date('d M Y H:i', strtotime($order->created_at)) ?></div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Metode Pembayaran</small>
                                <div class="font-weight-bold"><?= esc($order->pembayaran) ?></div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <small class="text-muted">Nama Penerima</small>
                               <div>
                                    <?= esc(trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? ''))) ?>
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <small class="text-muted">No. HP</small>
                                <div><?= esc($order->phone ?? '-') ?></div>
                            </div>
                            <div class="col-12 mt-2">
                                <small class="text-muted">Alamat Pengiriman</small>
                                <div><?= esc($order->shipping_address ?? '-') ?></div>
                            </div>
                        </div>

                        <!-- Item -->
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 border">

                                <thead class="bg-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>

                                <tbody>

                                <?php foreach ($details as $d): ?>

                                    <tr>

                                        <td>

                                            <div class="font-weight-bold">
                                                <?= esc($d->product_name) ?>
                                            </div>

                                            <small class="text-muted">
                                                Rp <?= number_format($d->selling_price) ?>
                                                ×
                                                <?= $d->qty ?>
                                            </small>

                                        </td>

                                        <td class="text-right align-middle">

                                            Rp <?= number_format($d->subtotal) ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                                </tbody>

                                <tfoot>
                                    <tr style="background-color: #eddede;">
                                        <td>Total Belanja</td>
                                        <td class="text-right">
                                            Rp <?= number_format($order->subtotal_produk) ?>
                                        </td>
                                    </tr>

                                    <?php if (($order->handling_fee ?? 0) > 0): ?>
                                    <tr>
                                        <td>Handling Fee</td>
                                        <td class="text-right">
                                            Rp <?= number_format($order->handling_fee) ?>
                                        </td>
                                    </tr>
                                    <?php endif; ?>

                                    <?php if (($order->shipping_cost ?? 0) > 0): ?>
                                    <tr>
                                        <td>Ongkos Kirim</td>
                                        <td class="text-right">
                                            Rp <?= number_format($order->shipping_cost) ?>
                                        </td>
                                    </tr>
                                    <?php endif; ?>

                                    <?php if (($order->diskon ?? 0) > 0): ?>
                                    <tr>
                                        <td class="text-success">
                                            Diskon
                                        </td>

                                        <td class="text-right text-success">
                                            - Rp <?= number_format($order->diskon) ?>
                                        </td>
                                    </tr>
                                    <?php endif; ?>

                                    <tr class="table-primary">

                                        <th>Grand Total</th>

                                        <th class="text-right">

                                            Rp <?= number_format($order->grand_total) ?>

                                        </th>

                                    </tr>

                                </tfoot>

                            </table>
                        </div>

                        <?php if (($order->order_status ?? '') === 'pending'): ?>
                            <div class="text-center mt-3">
                                <a href="<?= site_url('checkout/upload/' . $order->invoice_no) ?>" class="btn btn-warning">
                                    <i class="fa fa-upload mr-1"></i> Upload Bukti Pembayaran
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-center">
                    <a href="<?= site_url('/') ?>#track-order" class="btn btn-outline-secondary mr-2">
                        <i class="fa fa-search mr-1"></i> Lacak Pesanan Lain
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