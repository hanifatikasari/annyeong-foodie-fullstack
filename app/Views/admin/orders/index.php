<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
$statusLabels = [
    'pending'    => 'Menunggu Bayar',
    'processing' => 'Diproses',
    'shipped'    => 'Dikirim',
    'completed'  => 'Selesai',
    'cancelled'  => 'Dibatalkan',
];
?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Pesanan Online</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pesanan Online</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-body py-2">
                <form action="" method="get" class="form-inline">
                    <label class="mr-2">Filter Status:</label>
                    <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <?php foreach ($statusLabels as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $status === $value ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="mr-2 ml-3">Pengiriman:</label>
                    <select name="delivery_type" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        <option value="delivery" <?= service('request')->getGet('delivery_type') === 'delivery' ? 'selected' : '' ?>>
                            Delivery
                        </option>
                        <option value="pickup" <?= service('request')->getGet('delivery_type') === 'pickup' ? 'selected' : '' ?>>
                            Pickup
                        </option>
                    </select>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">Daftar Online Orders</h3></div>
            <div class="card-body table-responsive p-0">
                <?= view('admin/shared/flash_message') ?>
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Pelanggan</th>
                            <th>Pengiriman</th>
                            <th>Total</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <?php if (!$order) continue; ?>
                            <tr>
                                <td>
                                    <a href="<?= site_url('admin/orders/detail/' . $order->id) ?>">
                                        <?= esc($order->invoice_no) ?>
                                    </a>
                                </td>
                                <td>
                                    <?= esc($order->customer_name ?? '-') ?>
                                </td>
                                <td>
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
                                </td>
                                <td>Rp <?= number_format($order->grand_total) ?></td>
                                <td><?= esc($order->pembayaran) ?></td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'pending'    => 'warning',
                                        'processing' => 'primary',
                                        'shipped'    => 'info',
                                        'completed'  => 'success',
                                        'cancelled'  => 'danger',
                                    ];
                                    $sc = $statusColors[$order->order_status] ?? 'secondary';
                                    $sl = $statusLabels[$order->order_status] ?? $order->order_status;
                                    ?>
                                    <span class="badge badge-<?= $sc ?>">
                                        <?= esc($sl) ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y H:i', strtotime($order->created_at)) ?></td>
                                <td>
                                    <a href="<?= site_url('admin/orders/detail/' . $order->id) ?>" class="btn btn-sm btn-info">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($orders)): ?>
                            <tr><td colspan="7" class="text-center">Tidak ada data.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <?php if (($totalPages ?? 1) > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center mb-0">

                            <!-- Previous -->
                            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link"
                                href="<?= site_url('admin/orders?page=' . ($currentPage - 1) . ($status ? '&status=' . urlencode($status) : '')) ?>">
                                    &laquo;
                                </a>
                            </li>

                            <!-- Page Numbers -->
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                    <a class="page-link"
                                    href="<?= site_url('admin/orders?page=' . $i . ($status ? '&status=' . urlencode($status) : '')) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <!-- Next -->
                            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link"
                                href="<?= site_url('admin/orders?page=' . ($currentPage + 1) . ($status ? '&status=' . urlencode($status) : '')) ?>">
                                    &raquo;
                                </a>
                            </li>

                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>