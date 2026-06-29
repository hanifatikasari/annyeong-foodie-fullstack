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
                                <strong>Metode Pengiriman:</strong>
                                <?php if ($order->delivery_type === 'delivery'): ?>
                                    <span class="badge badge-info">
                                        <i class="fa fa-truck"></i>
                                        Delivery
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-success">
                                        <i class="fa fa-store"></i>
                                        Pickup
                                    </span>
                                <?php endif; ?>
                                <br>
                                <strong>Pembayaran:</strong> <?= esc($order->pembayaran) ?><br>
                                <strong>Status Pembayaran:</strong>
                                <?php if ($order->payment_status == 'paid'): ?>
                                    <span class="badge badge-success">Paid</span>
                                <?php elseif ($order->payment_status == 'pending_verification'): ?>
                                    <span class="badge badge-warning">Pending Verification</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">
                                        <?= ucwords(str_replace('_',' ', $order->payment_status)) ?>
                                    </span>
                                <?php endif; ?>
                                <br>
                                <?php if ($order->delivery_type === 'delivery'): ?>
                                    <div class="form-group">
                                        <strong>Area Pengiriman:</strong>
                                        <div><?= esc($order->shipping_area_name ?? '-') ?></div>
                                    </div>
                                    <form action="<?= site_url('admin/orders/save-courier/' . $order->id) ?>" method="post">
                                        <?= csrf_field() ?>
                                        <div class="form-row align-items-end">
                                            <div class="col">
                                                <div class="form-group mb-0">
                                                    <label for="delivery_courier_id">Pilih Kurir</label>
                                                    <select id="delivery_courier_id" class="form-control" name="delivery_courier_id" required>
                                                        <option value="">-- Pilih Kurir --</option>
                                                        <?php foreach ($couriers as $courier): ?>
                                                            <option value="<?= $courier->id ?>" <?= $order->delivery_courier_id == $courier->id ? 'selected' : '' ?>>
                                                                <?= esc(trim($courier->first_name . ' ' . $courier->last_name)) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fa fa-save mr-1"></i> Simpan Kurir
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                <?php endif; ?>

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
                                <tr>
                                    <th colspan="3" class="text-right">
                                        Subtotal Produk
                                    </th>
                                    <th class="text-right">
                                        Rp <?= number_format($order->subtotal_produk) ?>
                                    </th>
                                </tr>

                                <?php if (($order->shipping_cost ?? 0) > 0): ?>
                                <tr>
                                    <th colspan="3" class="text-right">
                                        Ongkir
                                    </th>
                                    <th class="text-right">
                                        Rp <?= number_format($order->shipping_cost) ?>
                                    </th>
                                </tr>
                                <?php endif; ?>

                                <?php if (($order->handling_fee ?? 0) > 0): ?>
                                <tr>
                                    <th colspan="3" class="text-right">
                                        Biaya Penanganan
                                    </th>
                                    <th class="text-right">
                                        Rp <?= number_format($order->handling_fee) ?>
                                    </th>
                                </tr>
                                <?php endif; ?>

                                <?php if (($order->diskon ?? 0) > 0): ?>
                                <tr>
                                    <th colspan="3" class="text-right">
                                        Diskon
                                    </th>
                                    <th class="text-right text-success">
                                        - Rp <?= number_format($order->diskon) ?>
                                    </th>
                                </tr>
                                <?php endif; ?>

                                <tr class="table-primary">
                                    <th colspan="3">
                                        GRAND TOTAL
                                    </th>
                                    <th class="text-right">
                                        Rp <?= number_format($order->grand_total) ?>
                                    </th>
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
                <?php if (in_array($order->order_status, ['pending'])): ?>
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
                                    <?php foreach (['pending','processing','shipped','completed','cancelled'] as $s): ?>
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
                    <a href="<?= site_url('admin/penjualan/struk/'.$order->id) ?>"
                        target="_blank" class="btn btn-dark">
                        <i class="fa fa-print"></i>
                        Cetak Struk
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>