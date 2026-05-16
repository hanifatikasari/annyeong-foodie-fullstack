<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Detail Pesanan</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/orders') ?>">Online Orders</a></li>
                    <li class="breadcrumb-item active"><?= esc($order->invoice_no) ?></li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('admin/shared/flash_message') ?>
        <div class="row">
            <!-- Detail Order -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><?= esc($order->invoice_no) ?></h3>
                        <span class="badge status-badge status-<?= $order->order_status ?> p-2">
                            <?= ucwords(str_replace('_', ' ', $order->order_status)) ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                               <strong>Penerima:</strong>
                                <?= esc(trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? ''))) ?><br>

                                <strong>No. HP:</strong>
                                <?= esc($order->phone ?? '-') ?><br>

                                <strong>Alamat:</strong><br>
                                <?= nl2br(esc($order->shipping_address ?? '-')) ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Tanggal:</strong> <?= date('d M Y H:i', strtotime($order->created_at)) ?><br>
                                <strong>Pembayaran:</strong> <?= esc($order->pembayaran) ?><br>
                                <?php if (!empty($order->catatan_customer)): ?>
                                    <strong>Catatan:</strong> <?= esc($order->catatan_customer) ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr><th>Produk</th><th class="text-center">Qty</th><th class="text-right">Harga</th><th class="text-right">Subtotal</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($details as $d): ?>
                                    <tr>
                                        <td><?= esc($d->product_name) ?> <small class="text-muted">(<?= esc($d->sku) ?>)</small></td>
                                        <td class="text-center"><?= $d->qty ?></td>
                                        <td class="text-right">Rp <?= number_format($d->selling_price) ?></td>
                                        <td class="text-right">Rp <?= number_format($d->subtotal) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <th colspan="3">TOTAL</th>
                                    <th class="text-right">Rp <?= number_format($order->total_bayar) ?></th>
                                </tr>
                            </tfoot>
                        </table>

                        <?php if (!empty($order->payment_proof)): ?>
                            <div class="mt-3">
                                <strong>Bukti Pembayaran:</strong><br>
                                <?php $ext = pathinfo($order->payment_proof, PATHINFO_EXTENSION); ?>
                                <?php if (in_array(strtolower($ext), ['jpg','jpeg','png'])): ?>
                                    <a href="<?= base_url($order->payment_proof) ?>" target="_blank">
                                        <img src="<?= base_url($order->payment_proof) ?>" class="img-fluid mt-2 rounded" style="max-width:300px;">
                                    </a>
                                <?php else: ?>
                                    <a href="<?= base_url($order->payment_proof) ?>" target="_blank" class="btn btn-sm btn-outline-secondary mt-2">
                                        <i class="fa fa-file-pdf-o mr-1"></i> Lihat Bukti PDF
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="col-md-4">
                <?php if (in_array($order->order_status, ['pending_verification'])): ?>
                    <div class="card card-success">
                        <div class="card-header"><h3 class="card-title">Verifikasi Pembayaran</h3></div>
                        <div class="card-body">
                            <p>Pastikan pembayaran sudah diterima sebelum verifikasi.</p>
                            <form action="<?= site_url('admin/orders/verify/' . $order->id) ?>" method="post">
                                <?= csrf_field() ?>
                                <button class="btn btn-success btn-block" onclick="return confirm('Verifikasi pembayaran ini?')">
                                    <i class="fa fa-check-circle mr-1"></i> Verifikasi Pembayaran
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header"><h3 class="card-title">Update Status</h3></div>
                    <div class="card-body">
                        <form action="<?= site_url('admin/orders/update-status/' . $order->id) ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="form-group">
                                <select name="order_status" class="form-control">
                                    <?php foreach (['pending_payment','pending_verification','verified','processing','ready','completed','cancelled'] as $s): ?>
                                        <option value="<?= $s ?>" <?= $order->order_status === $s ? 'selected' : '' ?>>
                                            <?= ucwords(str_replace('_', ' ', $s)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button class="btn btn-primary btn-block" onclick="return confirm('Update status pesanan?')">
                                <i class="fa fa-sync mr-1"></i> Update Status
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <a href="<?= site_url('admin/orders') ?>" class="btn btn-default btn-block">
                            <i class="fa fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>